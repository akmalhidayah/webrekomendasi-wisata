# Verifikasi query string Nginx

Konfigurasi reverse proxy harus meneruskan query string ke Laravel dan saat mengalihkan HTTP ke HTTPS:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

return 301 https://$host$request_uri;
```

Jangan gunakan `$uri` pada redirect HTTPS karena `lat`, `lng`, filter, pencarian, dan `page` dapat hilang. Verifikasi deployment aktual dengan:

```bash
curl -I "http://domain.com/wisata?lat=-5.1&lng=119.4"
curl -I "https://domain.com/wisata?lat=-5.1&lng=119.4"
```

Jika respons pertama mengandung header `Location`, nilainya harus tetap memuat `?lat=-5.1&lng=119.4`. Request HTTPS harus mencapai Laravel tanpa redirect bolak-balik.
