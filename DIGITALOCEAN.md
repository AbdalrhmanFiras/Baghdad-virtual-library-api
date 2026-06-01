# DigitalOcean Deployment Guide

## Quick Setup Steps

1. **Create a Droplet:**
   - Image: Ubuntu 22.04 LTS
   - Plan: Basic $12/mo (2GB RAM / 1 vCPU) minimum
   - Region: Frankfurt (fra1) recommended
   - Auth: SSH Key

2. **Point your domain** `abdalrhman.cupital.xyz` → Droplet IP

3. **SSH into droplet and install dependencies:**

```bash
ssh root@<DROPLET_IP>

# Install Docker
curl -fsSL https://get.docker.com | sh
apt install docker-compose-plugin -y

# Install Nginx + Certbot
apt install nginx certbot python3-certbot-nginx -y
```

4. **Setup project directory:**

```bash
mkdir -p /root/baghdad-library
cd /root/baghdad-library
```

5. **Create `.env.production`** in `/root/baghdad-library/` — copy from your `.env` and update:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `LOG_LEVEL=error`

6. **Copy `docker-compose.yml`** to `/root/baghdad-library/`

7. **Configure Nginx reverse proxy:**

```bash
cat > /etc/nginx/sites-available/baghdad-library << 'EOF'
server {
    listen 80;
    server_name abdalrhman.cupital.xyz;

    location / {
        proxy_pass         http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header   Host              $host;
        proxy_set_header   X-Real-IP         $remote_addr;
        proxy_set_header   X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
        client_max_body_size 100M;
    }
}
EOF

ln -s /etc/nginx/sites-available/baghdad-library /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

8. **Get SSL certificate:**

```bash
certbot --nginx -d abdalrhman.cupital.xyz
```

9. **Deploy:**

```bash
cd /root/baghdad-library
docker compose up -d --build
```

## GitHub Actions (CI/CD)

Add these secrets in your repo (Settings → Secrets → Actions):

| Secret | Value |
|--------|-------|
| `DO_HOST` | Your Droplet IP |
| `DO_SSH_KEY` | Your private SSH key for the droplet |

Then every push to `main` auto-deploys.

## Troubleshooting

```bash
# View app logs
docker logs laravel-app

# SSH into container
docker exec -it laravel-app bash

# Restart
docker compose restart

# Rebuild after code changes
docker compose up -d --build
```
