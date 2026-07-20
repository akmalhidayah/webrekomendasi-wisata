# Uji manual regresi

## Pagination admin

1. Login sebagai admin dan buka daftar destinasi dengan data lebih dari 10 baris.
2. Pastikan paginator Bootstrap terlihat, status aktif/disabled jelas, dan ringkasan jumlah benar.
3. Terapkan pencarian, kategori, atau status; pindah ke halaman berikutnya dan pastikan query tetap ada di URL.
4. Ulangi pada lebar mobile dan pastikan tidak ada overflow horizontal halaman.
5. Buka index yang datanya tidak melebihi `perPage`; ringkasan tetap tampil tanpa elemen `<ul class="pagination">`.

## Lokasi halaman destinasi

1. Hapus `wisataUserLocation` dan `wisataLocationRedirectAttempted` lewat DevTools, lalu buka `/wisata`.
2. Aktifkan lokasi dan pastikan Network hanya menunjukkan maksimal satu navigasi menuju URL dengan `lat` dan `lng`.
3. Refresh beberapa kali; pastikan tidak ada request `/wisata` berulang dan tidak ada error JavaScript tanpa penanganan.
4. Tolak izin lokasi atau simulasi timeout; halaman dan daftar wisata harus tetap terlihat, sedangkan tombol dapat dicoba kembali.
5. Simpan JSON rusak atau lokasi dengan `storedAt` lebih lama dari 30 menit; reload dan pastikan lokasi diabaikan tanpa navigasi ulang.
6. Klik **Hapus lokasi**; pastikan storage, guard, serta query `lat`/`lng` hilang dan izin lokasi tidak diminta otomatis.
7. Uji pencarian, kategori, dan pagination bersama koordinat; seluruh query harus tetap ada pada link halaman berikutnya.
