# StrideSync - Digital Ocean Deployment Guide

This guide walks you through deploying StrideSync to Digital Ocean App Platform with a Managed MySQL database.

## Prerequisites

Before starting, ensure you have:
- [ ] A [Digital Ocean account](https://cloud.digitalocean.com/registrations/new)
- [ ] Your project pushed to GitHub (public or private repository)
- [ ] Your Telegram Bot Token (from [@BotFather](https://t.me/botfather))
- [ ] Cloudinary account credentials (optional, for image uploads)

---

## Step 1: Push Your Code to GitHub

If you haven't already, push your code to GitHub:

```bash
# Initialize git if needed
git init

# Add all files
git add .

# Commit
git commit -m "Prepare for Digital Ocean deployment"

# Add your GitHub repository as remote
git remote add origin https://github.com/YOUR_USERNAME/stridesync.git

# Push to main branch
git push -u origin main
```

---

## Step 2: Generate Laravel APP_KEY

Before deploying, generate your application key locally:

```bash
cd /home/username/Desktop/StrideSync
php artisan key:generate --show
```

**Save this key!** You'll need it in Step 4. It looks like: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=`

---

## Step 3: Create App on Digital Ocean

### Option A: Using the Web Console (Recommended)

1. Go to [Digital Ocean App Platform](https://cloud.digitalocean.com/apps)
2. Click **"Create App"**
3. Select **GitHub** as source
4. Authorize Digital Ocean to access your repository
5. Select your `stridesync` repository and `main` branch
6. Click **"Next"**

### Configure Resources:

| Resource | Settings |
|----------|----------|
| **Web Service** | Basic ($5/month) - 512 MB RAM, 1 vCPU |
| **Worker** | Basic ($5/month) - For queue processing |
| **Database** | MySQL - Dev Database ($7/month) or Production ($15/month) |

### Option B: Using doctl CLI

```bash
# Install doctl
# See: https://docs.digitalocean.com/reference/doctl/how-to/install/

# Authenticate
doctl auth init

# Create app from spec
doctl apps create --spec .do/app.yaml
```

---

## Step 4: Configure Environment Variables

In the Digital Ocean App console, go to **Settings → App-Level Environment Variables** and add:

### Required Variables

| Variable | Value | Type |
|----------|-------|------|
| `APP_KEY` | `base64:your-generated-key-here` | **Encrypted** |
| `APP_ENV` | `production` | Plain |
| `APP_DEBUG` | `false` | Plain |
| `APP_URL` | `https://your-app-xxxxx.ondigitalocean.app` | Plain |
| `LOG_CHANNEL` | `stderr` | Plain |
| `LOG_LEVEL` | `error` | Plain |

### Database (Auto-configured if using DO Managed Database)

| Variable | Value | Type |
|----------|-------|------|
| `DB_CONNECTION` | `mysql` | Plain |
| `DATABASE_URL` | `${db.DATABASE_URL}` | Bindable |

### Session & Queue

| Variable | Value | Type |
|----------|-------|------|
| `SESSION_DRIVER` | `database` | Plain |
| `QUEUE_CONNECTION` | `database` | Plain |
| `CACHE_STORE` | `database` | Plain |

### Telegram Bot

| Variable | Value | Type |
|----------|-------|------|
| `TELEGRAM_BOT_TOKEN` | `your-telegram-bot-token` | **Encrypted** |

### Cloudinary (Optional - for image uploads)

| Variable | Value | Type |
|----------|-------|------|
| `CLOUDINARY_CLOUD_NAME` | `your-cloud-name` | **Encrypted** |
| `CLOUDINARY_API_KEY` | `your-api-key` | **Encrypted** |
| `CLOUDINARY_API_SECRET` | `your-api-secret` | **Encrypted** |

---

## Step 5: Configure Build & Run Commands

In the App Platform settings, set:

### Build Command:
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Run Command:
```bash
heroku-php-apache2 public/
```

### Migrations (handled automatically)
The `.do/app.yaml` includes a **PRE_DEPLOY Job** that runs migrations automatically before each deployment. No manual setup needed!

---

## Step 6: Deploy

1. Click **"Create Resources"** to start the deployment
2. Wait for the build to complete (usually 3-5 minutes)
3. Once deployed, your app will be available at the provided URL

---

## Step 7: Configure Telegram Webhook

After deployment, set up your Telegram webhook:

```bash
# Replace with your actual values
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://your-app-xxxxx.ondigitalocean.app/telegram/webhook"}'
```

Or visit this URL in your browser:
```
https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://your-app-xxxxx.ondigitalocean.app/telegram/webhook
```

---

## Step 8: Verify Deployment

### Check Application Health
1. Visit your app URL in a browser
2. You should see the StrideSync homepage

### Check Database Migrations
In Digital Ocean console → your app → Console tab:
```bash
php artisan migrate:status
```

### Check Telegram Bot
Send `/start` to your bot on Telegram - it should respond!

---

## Troubleshooting

### View Logs
- Go to your app in Digital Ocean console
- Click **"Runtime Logs"** tab
- Filter by component (web, worker)

### Common Issues

| Issue | Solution |
|-------|----------|
| 500 Error | Check `APP_KEY` is set correctly |
| Database connection error | Verify `DATABASE_URL` is bound to your managed database |
| Assets not loading | Ensure `npm run build` ran in build command |
| Telegram not responding | Check webhook URL and `TELEGRAM_BOT_TOKEN` |

### Run Artisan Commands
In the Console tab:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Estimated Monthly Cost

| Resource | Cost |
|----------|------|
| Web Service (Basic) | $5/month |
| Worker (Basic) | $5/month |
| MySQL Database (Dev) | $7/month |
| **Total** | **~$17/month** |

> 💡 **Tip**: You can start without the worker ($5 less) and use `QUEUE_CONNECTION=sync` for testing. Add the worker when you need background job processing.

---

## Next Steps

- [ ] Set up a custom domain (optional)
- [ ] Configure SSL (automatically provided by App Platform)
- [ ] Set up monitoring and alerts
- [ ] Configure automatic backups for database

---

## Quick Reference

| Item | Value |
|------|-------|
| PHP Version | 8.2+ |
| Node Version | 18+ (auto-detected) |
| Web Server | Apache (via heroku-php-apache2) |
| Region | Singapore (sgp) - change in app.yaml if needed |


