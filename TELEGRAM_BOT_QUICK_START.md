# 🚀 StrideSync Telegram Bot - Quick Start (5 Minutes)

## Step 1: Get Your Bot Token (2 minutes)

1. Open Telegram
2. Search for **@BotFather**
3. Send `/newbot`
4. Follow prompts (name your bot, set username)
5. Copy your token: `1234567890:ABCDefGHIjklmnoPQRstUvwXYz`

---

## Step 2: Set Token in Laravel (1 minute)

```bash
# Edit your .env file
TELEGRAM_BOT_TOKEN=1234567890:ABCDefGHIjklmnoPQRstUvwXYz
```

---

## Step 3: Run Migration (1 minute)

```bash
cd c:\laravel-projects\Stridesync\Stridesync
php artisan migrate
```

---

## Step 4: Activate Webhook (30 seconds)

Visit in your browser:
```
https://your-domain.com/api/telegram/set-webhook
```

Or via curl:
```bash
curl "https://your-domain.com/api/telegram/set-webhook"
```

---

## Step 5: Test Your Bot (1 minute)

1. Open Telegram
2. Search for your bot by username
3. Click `/start`
4. Complete profile (Gender → Pace → Location)
5. See **5-button menu** appear ✅

---

## 🎯 The 5 Buttons

| Button | What It Does |
|--------|-------------|
| 👤 My Profile | View & edit your info |
| 🤝 Find Buddy | Find runners within 5km |
| 💬 Check Invitations | See who wants to run with you |
| 🏃 Running Sessions | Join nearby group runs |
| ➕ Create Session | Link to website |

---

## ✅ Done!

Your bot is live! Users can now:
- ✅ Find running buddies nearby
- ✅ Send/receive invitations
- ✅ Join group sessions
- ✅ Create sessions on your website

---

## 🔍 Verify It's Working

```bash
# Check webhook status
curl "https://your-domain.com/api/telegram/webhook-info"

# Should show:
# {
#   "ok": true,
#   "result": {
#     "url": "https://your-domain.com/api/telegram/webhook",
#     "has_custom_certificate": false,
#     "pending_update_count": 0
#   }
# }
```

---

## 📱 Full Documentation

For detailed info, see:
- `TELEGRAM_BOT_FLOW.md` - Features explained
- `TELEGRAM_BOT_QUICK_REFERENCE.md` - Quick lookup
- `TELEGRAM_BOT_DIAGRAMS.md` - Visual flows
- `IMPLEMENTATION_GUIDE.md` - Technical details

---

## 🐛 Troubleshooting

**"Webhook error"**
- Check your domain is accessible from internet
- Verify TELEGRAM_BOT_TOKEN is correct
- Check laravel logs: `tail -f storage/logs/laravel.log`

**"No runners found"**
- Need multiple users in same location
- 5km radius limit applies
- Test with coordinates within 5km of each other

**"Commands not working"**
- Make sure `/start` completed (shows 5-button menu)
- Check browser console for errors
- Verify webhook is active

---

## 🎉 You're Ready!

Your Telegram bot is **production-ready** with 5 complete features!

**Next:** Monitor your logs and gather user feedback! 📊
