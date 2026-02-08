# StrideSync Telegram Bot Setup Guide

## ✅ Installation Complete!

Your StrideSync Telegram Bot has been integrated with the following components:

### 📦 Installed Packages
- `irazasyed/telegram-bot-sdk` - Telegram Bot API wrapper

### 🤖 Bot Commands Available
1. **/start** - Welcome message and quick actions
2. **/help** - List all available commands  
3. **/sessions** - View the latest 5 running sessions
4. **/my_sessions** - View your personal running sessions
5. **/join** - Join a running session
6. **/profile** - View your profile

### 🔧 Configuration

Your bot token is stored in `.env`:
```
TELEGRAM_BOT_TOKEN=8500790190:AAGTweICWPGCpCVltB6gq6HeD4xUPzGZGI
TELEGRAM_BOT_NAME=StrideSyncBot
```

### 🌐 Webhook Setup

To activate your bot, you need to set the webhook. Run:

```bash
# In your terminal, visit this URL to set the webhook:
http://localhost:8000/api/telegram/set-webhook

# Check webhook info:
http://localhost:8000/api/telegram/webhook-info
```

**IMPORTANT:** Your local server won't work for the webhook. Once deployed to a server with HTTPS, update the webhook URL.

### 📱 Bot URL
- **Telegram Bot:** [@StrideSyncBot](https://t.me/StrideSyncBot)
- **Test it:** `/start` command

### 🔗 Integration Points

1. **User Registration**
   - When users register, their `telegram_id` is stored in the database
   - Users can link their Telegram account to their profile

2. **Session Notifications**
   - Users get notifications when new sessions are created
   - Users are notified when someone joins their session

3. **Broadcast Messages**
   - Admins can send broadcast messages to all users via Telegram

### 📊 Database Fields

The `users` table already has:
- `telegram_id` - Stores user's Telegram chat ID

### 🚀 Next Steps

1. ✅ Set webhook on production server (not local)
2. Open Telegram and search for: **@StrideSyncBot**
3. Click `/start` to initialize
4. Users can link their accounts via the bot
5. Enable session notifications

### 🤝 **NEW: Buddy Match Integration**

The **Buddy Match** button on the dashboard now connects with the Telegram bot!

**Features:**
- Click "Buddy Match" button → See all available running buddies
- View their pace, PB time, and session count
- Click "Connect" → Buddy receives a Telegram request
- Users can link their Telegram account in-app

**How it works:**
1. User clicks "Buddy Match" button
2. System fetches all active users with Telegram linked
3. Shows buddy profiles with running stats
4. Clicking "Connect" sends a Telegram message to the buddy
5. Buddies can plan runs together via Telegram DM

---

- [ ] Inline keyboard for quick session joining
- [ ] User location-based session recommendations
- [ ] Buddy matching via Telegram
- [ ] Real-time race leaderboards
- [ ] Push notifications for nearby runners
- [ ] Voice message session updates

---

**Created:** November 11, 2025
**Bot Status:** Ready for testing on @StrideSyncBot


