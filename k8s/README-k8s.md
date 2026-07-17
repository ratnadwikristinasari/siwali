# ==============================================================================
# README-k8s.md — Panduan Deploy SiWali ke k3s
# ==============================================================================
# Infrastruktur:
#   VM Nginx (public IP)  ─────────────────────────────────────────────────────
#     domain: be-jti.jtinova.com (atau domain publik lainnya)
#     Port 80/443 → Nginx → proxy ke VM k3s
#
#   VM k3s (internal IP: 10.10.0.55) ─────────────────────────────────────────
#     k3s single-node: JTIForm (port 32724) + SiWali (port 32725)
#     Docker: MySQL, Redis, RabbitMQ, dll.
#
# Path routing:
#   example.com/jtiform/ → 10.10.0.31:32724 (JTIForm k3s)
#   example.com/siwali/  → 10.10.0.55:32725 (SiWali k3s)  ← INI
# ==============================================================================

## Alias kubectl di VM k3s

```bash
alias kubectl="sudo k3s kubectl"
```

## Setup awal (hanya sekali)

```bash
# 1. Buat namespace
kubectl create namespace siwali

# 2. Setup GHCR pull secret
kubectl create secret docker-registry ghcr-creds \
  --docker-server=ghcr.io \
  --docker-username=YOUR_GITHUB_USERNAME \
  --docker-password=ghp_YOUR_PAT_TOKEN \
  -n siwali

# 3. Apply semua manifest k3s
kubectl apply -f k8s/service-siwali.yaml
kubectl apply -f k8s/ingress-siwali.yaml
kubectl apply -f k8s/deployment-siwali.yaml
kubectl apply -f k8s/deployment-siwali-worker.yaml
kubectl apply -f k8s/hpa-siwali.yaml
```

## Deploy ulang setelah image baru

```bash
# Rolling restart (pull image terbaru, zero-downtime)
kubectl rollout restart deployment siwali-app -n siwali
kubectl rollout restart deployment siwali-worker -n siwali

# Monitor proses
kubectl rollout status deployment siwali-app -n siwali
```

## Monitoring

```bash
# Lihat semua pod SiWali
kubectl get pods -n siwali

# Resource usage
kubectl top pods -n siwali

# Log real-time
kubectl logs -f deployment/siwali-app -n siwali --tail=100

# Log worker
kubectl logs -f deployment/siwali-worker -n siwali --tail=50

# Semua events (error, OOM, dll)
kubectl get events -n siwali --sort-by='.lastTimestamp'
```

## Setup Nginx di VM Publik

```bash
# Copy config ke VM Nginx
scp k8s/nginx-siwali.conf user@VM_NGINX_IP:/etc/nginx/project-jti.d/siwali.conf

# Test config
sudo nginx -t

# Reload
sudo systemctl reload nginx
```

## Troubleshooting path prefix

```bash
# Test dari dalam pod — apakah URL yang di-generate sudah benar?
kubectl exec -n siwali deployment/siwali-app -- \
  php artisan tinker --execute="echo url('/test');"
# Output yang benar: https://be-jti.jtinova.com/siwali/test

# Test /up endpoint via NodePort langsung (tanpa Nginx)
curl http://10.10.0.55:32725/up

# Test via Nginx publik
curl https://be-jti.jtinova.com/siwali/up
```

## Jika URL masih generate http:// bukan https://

```bash
# Cek apakah TrustProxies aktif dengan benar
kubectl exec -n siwali deployment/siwali-app -- \
  php artisan tinker --execute="
    \$req = request();
    echo 'Proto: ' . \$req->header('X-Forwarded-Proto') . PHP_EOL;
    echo 'URL: ' . url('/') . PHP_EOL;
  "
```

## Jika URL tidak include /siwali prefix

Pastikan:
1. `APP_URL=https://be-jti.jtinova.com/siwali` di deployment env
2. Nginx mengirim header `X-Forwarded-Prefix: /siwali`
3. `TrustProxies` sudah ditambahkan ke `bootstrap/app.php` dengan `HEADER_X_FORWARDED_PREFIX`
