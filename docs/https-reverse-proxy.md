# HTTPS dan reverse proxy production

## Environment Laravel

Gunakan nilai berikut pada environment production. Jangan commit `APP_KEY`, password, atau secret lainnya.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wisataku.web.id
FORCE_HTTPS=true

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Opsional bila aset memakai origin terpisah. Tidak diperlukan untuk origin aplikasi yang sama.
# ASSET_URL=https://wisataku.web.id
```

`SESSION_DOMAIN` tidak perlu diatur jika session hanya digunakan pada host yang sedang diakses. Gunakan `.wisataku.web.id` hanya jika session memang harus dibagi dengan subdomain.

Laravel mempercayai proxy loopback `127.0.0.1` dan `::1` di `bootstrap/app.php`, sesuai topologi contoh Nginx satu host. Jika proxy berada di container/host lain, ganti atau tambahkan IP/CIDR aktual setelah memeriksa jaringan. Jangan menggunakan wildcard jika origin Laravel dapat diakses langsung dari internet; wildcard hanya layak ketika firewall memastikan semua request selalu melewati proxy tepercaya.

## Redirect port 80

`$request_uri` wajib digunakan agar query seperti `lat`, `lng`, filter, dan `page` tetap ada.

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name wisataku.web.id www.wisataku.web.id;

    return 301 https://$host$request_uri;
}
```

## TLS reverse proxy

Ganti `<laravel-upstream>` dengan upstream aktual dari deployment. Repository ini tidak menyediakan Compose atau port production sehingga tidak ada nama service/port yang dapat diasumsikan.

```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name wisataku.web.id www.wisataku.web.id;

    ssl_certificate /etc/letsencrypt/live/wisataku.web.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/wisataku.web.id/privkey.pem;

    location / {
        proxy_pass http://<laravel-upstream>;
        proxy_http_version 1.1;

        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Port 443;
    }
}
```

Jika Nginx melayani Laravel langsung melalui PHP-FPM, gunakan front controller yang mempertahankan query string:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_param HTTPS on;
    fastcgi_param HTTP_X_FORWARDED_PROTO https;
    fastcgi_pass <php-fpm-upstream>;
}
```

## Deployment

```bash
cd /path/project
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan down
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
sudo nginx -t
sudo systemctl reload nginx
```

Repository saat ini tidak mempunyai file Compose. Jika production memakai Docker, temukan nama service aktual terlebih dahulu:

```bash
docker compose ps
docker compose exec <service-laravel> php artisan optimize:clear
docker compose exec <service-laravel> php artisan config:cache
docker compose exec <service-laravel> php artisan route:cache
docker compose exec <service-laravel> php artisan view:cache
```

## Verifikasi production

```bash
curl -I http://wisataku.web.id
curl -I "http://wisataku.web.id/wisata?lat=-5.1&lng=119.4&page=2"
curl -I "https://wisataku.web.id/wisata?lat=-5.1&lng=119.4"
```

Respons HTTP harus `301` menuju URL HTTPS yang masih membawa query. Respons HTTPS tidak boleh kembali ke HTTP atau berulang. Di browser, uji home, lokasi, pagination, survei, generate/hasil rekomendasi, rating, login, halaman admin, dan logout sambil memeriksa Network serta peringatan mixed content di Console.
