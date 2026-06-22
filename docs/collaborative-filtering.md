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
similarity(u,v) = Σ(r_u,i × r_v,i)
                  ─────────────────────────────
                  √Σ(r_u,i²) × √Σ(r_v,i²)
```

Jika kedua guest tidak memiliki wisata yang sama, similarity bernilai `0`. Hanya similarity positif yang digunakan.

## Prediksi rating

Untuk wisata `i` yang belum dinilai target:

```text
prediction(u,i) = Σ(similarity(u,v) × rating(v,i))
                  ─────────────────────────────────
                         Σ|similarity(u,v)|
```

Hanya guest tetangga yang pernah menilai wisata `i` yang berkontribusi. `nilai_similarity` hasil rekomendasi menyimpan similarity tertinggi dari tetangga yang berkontribusi. Nilai disimpan dengan empat angka desimal.

## Alur sistem

1. Wisatawan membuka survei tanpa login dan sistem membuat `guest_visitors` berdasarkan sesi.
2. Wisatawan memberi rating awal untuk 10 destinasi.
3. Sistem membentuk matriks user-item dari `survey_preferensi`.
4. Sistem menghitung cosine similarity target dengan setiap guest lain.
5. Sistem mencari wisata aktif yang belum dinilai target.
6. Sistem menghitung prediksi setiap kandidat dan mengambil lima nilai tertinggi.
7. Hasil lama target dihapus, kemudian hasil baru disimpan ke `hasil_rekomendasi` beserta ranking.
8. Wisatawan melihat hasil rekomendasi pada `/rekomendasi/hasil`.

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
(5×5 + 4×4 + 3×2) / (√(5²+4²+3²) × √(5²+4²+2²))
= 47 / (√50 × √45)
= 0.9908
```

Similarity A–C:

```text
(5×1 + 4×2 + 3×5) / (√50 × √30)
= 28 / (√50 × √30)
= 0.7230
```

Prediksi A untuk W4:

```text
((0.9908×5) + (0.7230×2)) / (0.9908+0.7230) = 3.7344
```

Prediksi A untuk W5:

```text
((0.9908×3) + (0.7230×5)) / (0.9908+0.7230) = 3.8437
```

Karena prediksi W5 lebih tinggi, W5 ditempatkan di atas W4.

## Survey preferensi dan rating kunjungan

| Aspek | Survey Preferensi | Rating Kunjungan |
|---|---|---|
| Waktu | Sebelum memperoleh rekomendasi | Setelah benar-benar berkunjung |
| Makna | Tingkat minat awal | Penilaian pengalaman aktual |
| Moderasi | Tidak dimoderasi | Pending, disetujui, atau ditolak admin |
| Penggunaan saat ini | Matriks Collaborative Filtering | Rata-rata dan ulasan publik setelah disetujui |
