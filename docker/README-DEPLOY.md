# Deploy SiWali (FrankenPHP Octane, non-root)

Nginx host `jti.polije.ac.id` -> `/siwali/` -> `127.0.0.1:8084`. Satu VM dengan jtisurat (8081), ruangbaca (8083), dll.

## Isi
- `Dockerfile`: multi-stage Vite + Composer + FrankenPHP PHP 8.4, user `app` UID 10001.
- `compose.yaml`: `app` (8084) + `worker` (queue:work redis), read-only, `cap_drop: ALL`.
- `.env.example` -> `.env.app` (gitignored).
- `nginx.conf.example`: blok `/siwali/`.

## Deploy manual
```sh
cp docker/.env.example docker/.env.app   # isi APP_KEY, DB, Redis, S3, OAuth
docker compose -f docker/compose.yaml up -d --build
docker compose -f docker/compose.yaml exec app php artisan migrate --force
curl -i http://127.0.0.1:8084/up
```

## Subpath
`APP_URL` + `ASSET_URL` = `https://jti.polije.ac.id/siwali` (ASSET_URL juga dipakai saat build Vite, dikirim sebagai build-arg),
`SESSION_PATH=/siwali`, `SESSION_COOKIE` unik, `CACHE_PREFIX`/`REDIS_PREFIX` unik.
`AppServiceProvider` memakai `URL::forceRootUrl(config('app.url'))`.
Daftarkan redirect URI OAuth `https://jti.polije.ac.id/siwali/auth/callback` di SuperApp.

## CI/CD
`.github/workflows/production.yaml`: test -> build (GHCR) -> deploy (self-hosted runner, SSH, compose pull/up, cek `/up`, migrate).
Secrets environment `production`: `SSH_HOST`, `SSH_USER`, `SSH_PORT`, `SSH_PRIVATE_KEY`, `SSH_HOST_FINGERPRINT`, `DEPLOY_PATH`.
Variable `ASSET_URL` (repo/environment variables) = `https://jti.polije.ac.id/siwali`.

## Keamanan
- Port hanya loopback; `trustProxies('*')` aman selama itu. Jangan bind `0.0.0.0`.
- Firewall MySQL/Redis/RabbitMQ hanya dari IP VM ini; Redis `requirepass`.
- `.env*` tidak masuk image/git; secret hanya di `docker/.env.app` (chmod 600).
- Admin port Caddy 2019 tidak dipublish.
