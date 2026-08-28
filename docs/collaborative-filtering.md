# Dokumentasi Collaborative Filtering

## Konsep

Sistem menggunakan **User-Based Collaborative Filtering**. Guest target dibandingkan dengan guest lain berdasarkan pola rating awal pada destinasi yang sama. Guest dengan pola paling mirip menjadi tetangga yang ratingnya dipakai untuk memprediksi minat target terhadap destinasi yang belum dinilai.

Implementasi terdapat pada `app/Services/CollaborativeFilteringService.php` dan dibuat manual tanpa package rekomendasi eksternal.

## Sumber data

- `survey_preferensi.rating_awal` adalah sumber utama. Nilai 1–5 menggambarkan minat awal pengunjung.
- `rating_kunjungan.rating` menyimpan pengalaman setelah berkunjung. Hanya rating berstatus `disetujui` yang ditampilkan sebagai agregat publik. Versi algoritma saat ini belum mencampurkan rating kunjungan ke matriks agar makna minat awal dan pengalaman aktual tetap terpisah.

## Cosine Similarity

Kemiripan guest `u` dan `v` dihitung hanya pada wisata yang dinilai oleh keduanya:

```text
similarity(u,v) = Σ((r_u,i - mean_u) × (r_v,i - mean_v))
                  ───────────────────────────────────────────────
                  √Σ((r_u,i - mean_u)²) × √Σ((r_v,i - mean_v)²)
```

Rating dipusatkan terhadap rata-rata masing-masing guest agar yang dibandingkan
adalah pola minat, bukan sekadar kecenderungan memberi nilai tinggi atau rendah.
Jika semua rating target sama, penyebut menjadi nol sehingga Collaborative
Filtering tidak memiliki sinyal yang valid. Jika kedua guest tidak memiliki
wisata yang sama, similarity juga bernilai `0`. Hanya similarity positif yang
digunakan.

## Prediksi rating

Untuk wisata `i` yang belum dinilai target, prediksi memakai rata-rata target
sebagai baseline dan deviasi rating tetangga:

```text
prediction(u,i) = mean_u + Σ(similarity(u,v) × (rating(v,i) - mean_v))
                  ─────────────────────────────────────────────────────
                                  Σ|similarity(u,v)|
```

Hanya guest tetangga yang pernah menilai wisata `i` yang berkontribusi. `nilai_similarity` hasil rekomendasi menyimpan similarity tertinggi dari tetangga yang berkontribusi. Nilai disimpan dengan empat angka desimal.

## Alur sistem

1. Wisatawan membuka survei tanpa login dan sistem membuat `guest_visitors` berdasarkan sesi.
2. Wisatawan memberi rating awal untuk 10 destinasi.
3. Sistem membentuk matriks user-item dari `survey_preferensi`.
4. Jika rating bervariasi, sistem menghitung cosine similarity target dengan setiap guest lain.
5. Jika rating seragam, sistem melewati Collaborative Filtering dan memakai mode kualitas, budget, rating, dan popularitas.
6. Sistem mencari wisata aktif yang belum dinilai target.
7. Sistem menghitung skor setiap kandidat dan mengambil lima nilai tertinggi.
8. Hasil lama target dihapus, kemudian hasil baru disimpan ke `hasil_rekomendasi` beserta ranking.
9. Wisatawan melihat hasil rekomendasi pada `/rekomendasi/hasil`.

## Rating survei seragam

Rating rendah atau tengah yang seragam tetap menghasilkan rekomendasi. Pada
kondisi semua rating berada di rentang 1–2 atau semuanya bernilai 3, sistem
menggunakan metode `Quality Budget & Popularity` karena rating survei tidak
memberi pembeda antar destinasi. Kandidat dipilih dari destinasi aktif yang
belum dinilai, lalu diurutkan berdasarkan:

- rating/kualitas destinasi sebagai faktor utama;
- budget maksimum sebagai batas keras dan preferensi budget bila diisi;
- jarak lokasi bila pengguna mengizinkannya;
- jumlah rating sebagai penentu jika skor utama sama.

Rating 4–5 yang seragam tetap menggunakan metode `Broad Interest` dengan
perhitungan kualitas, budget, dan jarak yang sama. Collaborative Filtering hanya
digunakan ketika rating survei memiliki variasi yang cukup.

## Fallback

Fallback digunakan jika target belum memiliki rating, belum ada guest lain yang cukup beririsan, atau tidak ada kandidat dengan prediksi valid. Sistem memilih wisata aktif yang belum dinilai target berdasarkan rata-rata `rating_awal` tertinggi. Jika wisata belum memiliki rating, prediksi default adalah `3.0000`. Hasil tetap disimpan dengan metode `Collaborative Filtering - Fallback` sehingga antarmuka dapat memberi penjelasan kepada pengguna.

## Contoh perhitungan sederhana

Terdapat tiga guest dan lima wisata. Tanda `-` berarti belum dinilai.

| Guest | W1 | W2 | W3 | W4 | W5 |
|---|---:|---:|---:|---:|---:|
| A (target) | 5 | 4 | 3 | - | - |
| B | 5 | 4 | 2 | 5 | 3 |
| C | 1 | 2 | 5 | 2 | 5 |

Similarity A–B:

```text
mean A = 4, mean B = 11/3
((5-4)×(5-11/3) + (4-4)×(4-11/3) + (3-4)×(2-11/3))
/ (√((5-4)²+(4-4)²+(3-4)²) × √((5-11/3)²+(4-11/3)²+(2-11/3)²))
× (3/5)
= 0.5892
```

Similarity A–C:

```text
mean A = 4, mean C = 8/3
((5-4)×(1-8/3) + (4-4)×(2-8/3) + (3-4)×(5-8/3))
/ (√((5-4)²+(4-4)²+(3-4)²) × √((1-8/3)²+(2-8/3)²+(5-8/3)²))
× (3/5)
= -0.5765
```

Similarity negatif tidak digunakan sebagai tetangga. Rata-rata rating B pada
seluruh wisata yang dinilainya adalah `19/5 = 3,8`. Jika B menjadi tetangga
positif, prediksi A untuk W4 memakai rata-rata A sebagai baseline:

```text
mean A + similarity(A,B) × (rating B,W4 - mean B)
= 4 + 0.5892 × (5 - 3.8)
= 4.7070
```

Untuk W5:

```text
mean A + 0.5892 × (3 - 3.8)
= 3.5286
```

Karena prediksi W5 lebih tinggi, W5 ditempatkan di atas W4.

## Survey preferensi dan rating kunjungan

| Aspek | Survey Preferensi | Rating Kunjungan |
|---|---|---|
| Waktu | Sebelum memperoleh rekomendasi | Setelah benar-benar berkunjung |
| Makna | Tingkat minat awal | Penilaian pengalaman aktual |
| Moderasi | Tidak dimoderasi | Pending, disetujui, atau ditolak admin |
| Penggunaan saat ini | Matriks Collaborative Filtering | Rata-rata dan ulasan publik setelah disetujui |
