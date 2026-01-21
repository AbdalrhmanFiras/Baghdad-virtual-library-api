# Coolify Deployment Guide

## Quick Setup Steps

1. **In Coolify Dashboard:**
   - Go to your project
   - Click "New Resource" → "Docker Compose" or "Dockerfile"
   - Select your repository

2. **If using Dockerfile (Recommended):**
   - Build Type: `Dockerfile`
   - Dockerfile Location: `Dockerfile` (root directory)
   - Build Context: `.` (root directory)
   - Port: `80`

3. **If using Docker Compose:**
   - Build Type: `Docker Compose`
   - Docker Compose File: `docker-compose.yml`
   - Service Name: `app`

## Environment Variables

Make sure to set these in Coolify:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your_jwt_secret
JWT_TTL=1440

AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket
AWS_URL=your_s3_url
AWS_ENDPOINT=your_s3_endpoint
```

## Troubleshooting

### Issue: "Docker not found"

**Solution 1:** Make sure Dockerfile is in the root directory
- Check that `Dockerfile` exists in the root of your repository
- Verify it's committed to git

**Solution 2:** Check Build Settings
- In Coolify, go to your resource settings
- Verify "Build Type" is set to "Dockerfile"
- Verify "Dockerfile Location" is set to `Dockerfile`
- Verify "Build Context" is set to `.`

**Solution 3:** Use Docker Compose instead
- Change Build Type to "Docker Compose"
- Set Docker Compose File to `docker-compose.yml`

**Solution 4:** Check Repository Connection
- Make sure Coolify has access to your repository
- Verify the repository URL is correct
- Check if there are any webhook issues

## Post-Deployment

After deployment, run these commands in Coolify's terminal:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Storage Permissions

The Dockerfile sets proper permissions, but if you have issues:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```
