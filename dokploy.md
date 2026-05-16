# Deploy Apotek Zema di Dokploy

## Build

- **Build type:** Dockerfile  
- **Dockerfile path:** `Dockerfile` (root)  
- **Port container:** `80` ← **WAJIB** di Dokploy (bukan 3000). Kalau salah → **502 Bad Gateway**
- **Protocol:** HTTP (SSL di-handle Traefik/Dokploy)

## Environment variables (wajib)

| Variable | Contoh |
|----------|--------|
| `APP_NAME` | Apotek Zema |
| `APP_ENV` | **production** (jangan `local` di server) |
| `APP_KEY` | base64:... (generate: `php artisan key:generate --show`) |
| `APP_DEBUG` | **false** |
| `APP_URL` | https://apotek.domainkamu.com |
| `DB_CONNECTION` | `pgsql` atau `mysql` (sesuai database Dokploy) |
| `DB_HOST` | host database Dokploy / eksternal |
| `DB_PORT` | 3306 |
| `DB_DATABASE` | apotek_zema |
| `DB_USERNAME` | ... |
| `DB_PASSWORD` | ... |
| `SESSION_DRIVER` | database |
| `QUEUE_CONNECTION` | database |
| `CACHE_STORE` | database |
| `RUN_MIGRATIONS` | true |
| `RUN_OPTIMIZE` | true |

## Health check

- Path: `/up` (Laravel 11 built-in)

## Proses di dalam container (Supervisor)

| Program | Fungsi |
|---------|--------|
| `php-fpm` | Jalankan PHP |
| `nginx` | Web server (port 80) |
| `laravel-queue` | `queue:work database` |
| `laravel-scheduler` | `schedule:work` |

## Persistent storage (disarankan mount volume)

- `/var/www/html/storage`
- `/var/www/html/bootstrap/cache`

## 502 Bad Gateway?

1. **Cek Port aplikasi di Dokploy = `80`** (Application → Ports / Domains).
2. Pastikan container log ada: `nginx entered RUNNING` dan `php-fpm entered RUNNING`.
3. ENV: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-kamu.com`.
4. Redeploy setelah push fix terbaru (nginx `conf.d` + php-fpm port 9000).

## Setelah deploy pertama

1. Pastikan database sudah dibuat & kredensial benar.  
2. Set `RUN_MIGRATIONS=true` → migrasi jalan otomatis saat container start.  
3. Buat user admin/kasir lewat seeder atau tinker jika belum ada.
