# StrideSync Telegram Bot - Quick Reference

## 🎯 The 5-Button Flow

```
┌─────────────────────────────────────────┐
│        /start or Welcome Back           │
├─────────────────────────────────────────┤
│  New User? Complete Profile:            │
│  → Gender → Pace → Location             │
├─────────────────────────────────────────┤
│  MAIN MENU - Choose One:                │
├─────────────────────────────────────────┤
│  1️⃣  👤 MY PROFILE                      │
│     View & Edit Profile Info            │
│     └─ Can edit: Gender, Pace, Location │
├─────────────────────────────────────────┤
│  2️⃣  🤝 FIND BUDDY                      │
│     Find Runners Near You (5km radius)  │
│     └─ Click "Invite to Run"            │
│     └─ Sends invitation & notifies      │
├─────────────────────────────────────────┤
│  3️⃣  💬 CHECK INVITATIONS               │
│     See Who Wants to Run With You       │
│     └─ Accept ✅ or Decline ❌          │
│     └─ Notifies them of your choice     │
├─────────────────────────────────────────┤
│  4️⃣  🏃 RUNNING SESSIONS                │
│     Find Group Sessions Near You        │
│     └─ Click "Join Session"             │
│     └─ Confirms & notifies creator      │
├─────────────────────────────────────────┤
│  5️⃣  ➕ CREATE SESSION                  │
│     Link to Website                     │
│     └─ Opens: /sessions/create          │
│     └─ Session appears in "Sessions"    │
└─────────────────────────────────────────┘
```

---

## 📊 Feature Comparison

| Feature | Type | Radius | Search | Notify |
|---------|------|--------|--------|--------|
| Find Buddy | Invitation | 5km | By location | Both users |
| Check Invitations | Incoming | N/A | Who invited you | Sender |
| Running Sessions | Join | 5km | By location | Creator |
| Create Session | Website | N/A | Create on web | N/A |

---

## 💾 Database Records Created

### When User Invites Buddy
```sql
INSERT INTO joined_sessions (
  session_id,      -- NULL (invitation, not session)
  user_id,         -- Person sending
  invited_user_id, -- Person receiving
  status           -- 'invited'
)
```

### When User Joins Session
```sql
INSERT INTO joined_sessions (
  session_id,      -- Session ID
  user_id,         -- Person joining
  invited_user_id, -- NULL (it's a session join)
  status           -- 'joined'
)
```

### When Invite Accepted
```sql
UPDATE joined_sessions
SET status = 'accepted'
WHERE invited_user_id = user_id
```

---

## 🔔 Notification Flow

### Buddy Invitation
```
User A clicks "Invite to Run"
    ↓
CREATE joined_sessions (user_id=A, invited_user_id=B, status='invited')
    ↓
User A gets: "✅ Invitation sent to User B!"
User B gets: "🤝 User A invited you to run together!"
    ↓
User B clicks Check Invitations
    ↓
Can Accept ✅ or Decline ❌
    ↓
If Accept:
  User A gets: "✅ User B accepted your invitation!"
  User B gets: "✅ You accepted the invitation!"
```

### Session Join
```
User A clicks "Join Session"
    ↓
CREATE joined_sessions (session_id=S, user_id=A, status='joined')
    ↓
User A gets: "✅ You joined session XYZ!"
Session Creator gets: "👥 User A joined your session!"
```

---

## 🗺️ Location Algorithm

**Distance Calculation:** Haversine Formula

```
Find all users/sessions
Filter where distance ≤ 5km from user location
Sort by distance (closest first)
Show top 10 results
```

**Example Radius:**
- 5km ≈ 3 miles
- ≈ 5-10 min bike ride
- ≈ 30-40 min walk

---

## 📱 Button Types

### Reply Keyboard (Bottom of Screen)
- Persistent
- Multiple buttons
- Used for: Main menu, location sharing
- Example: `👤 My Profile | 🤝 Find Buddy`

### Inline Buttons (In Message)
- Clickable in message
- Single message typically
- Used for: Selections, confirmations
- Example: `[✅ Accept] [❌ Decline]`

---

## 🎮 User States

```
initial
    ↓
waiting_gender     (User selects: Male/Female)
    ↓
waiting_pace       (User types: 6:30/km)
    ↓
waiting_location   (User shares location)
    ↓
profile_complete   (User can access all features)
    ↓
[Can edit profile anytime from main menu]
```

---

## 🔧 How Callback Queries Work

When user clicks an inline button:

```
User clicks button with callback_data: "invite_buddy_123"
    ↓
Telegram sends callback_query to webhook
    ↓
handleCallbackQuery() matches callback_data
    ↓
if (strpos($data, 'invite_buddy_') === 0) {
    $buddyId = str_replace('invite_buddy_', '', $data);
    // Create invitation, send notifications
}
    ↓
answerCallbackQuery() removes spinning loading
```

**Callback Data Examples:**
- `gender_male` / `gender_female` - Gender selection
- `invite_buddy_123` - Invite user #123
- `accept_invite_456` - Accept invitation #456
- `decline_invite_456` - Decline invitation #456
- `join_session_789` - Join session #789
- `edit_gender` / `edit_pace` / `edit_location` - Edit profile

---

## 📋 Checklist for Testing

- [ ] `/start` creates new user
- [ ] Gender selection works
- [ ] Pace input works
- [ ] Location sharing works
- [ ] Profile completion shows main menu
- [ ] "My Profile" displays all info
- [ ] "Edit Gender" opens inline buttons
- [ ] "Edit Pace" asks for input
- [ ] "Edit Location" requests location
- [ ] "Find Buddy" shows nearby users
- [ ] "Invite to Run" sends notification
- [ ] "Check Invitations" shows incoming
- [ ] Accept/Decline buttons work
- [ ] "Running Sessions" shows nearby
- [ ] "Join Session" works
- [ ] "Create Session" shows link
- [ ] Notifications sent to other users
- [ ] Distance calculation correct (5km max)
- [ ] Error messages show when incomplete

---

## 🐛 Common Issues

### "No runners found"
- Check if other users have location set
- Check distance calculation (5km radius)
- Test with multiple users in same area

### "Profile not complete"
- Ensure user went through gender → pace → location
- Check `telegram_state` in users table
- Verify location is stored as JSON

### Notifications not received
- Check other user has telegram_id set
- Verify sendMessage() API calls succeed
- Check Telegram bot token is correct

### Distance always too far
- Verify coordinates are valid (lat: -90 to 90, lon: -180 to 180)
- Check Haversine formula in calculateDistance()
- Test with known coordinates

---

## 🚀 Deployment Checklist

- [ ] Set TELEGRAM_BOT_TOKEN in .env
- [ ] Run migration for joined_sessions updates
- [ ] Set webhook: `GET /api/telegram/set-webhook`
- [ ] Verify webhook: `GET /api/telegram/webhook-info`
- [ ] Test with real Telegram bot
- [ ] Monitor logs: `storage/logs/laravel.log`
- [ ] Check database records are created
- [ ] Verify notifications are sent

---

## 📞 API Endpoints

```
POST /api/telegram/webhook
  ↓ Receives messages and callbacks

GET /api/telegram/set-webhook
  ↓ Set up webhook URL

GET /api/telegram/webhook-info
  ↓ Check webhook status
```

---

## 💡 Tips & Tricks

1. **Test Location Search**
   - Use same latitude/longitude for multiple users
   - Or use coordinates within 5km of each other
   - Tool: Use Google Maps to get coordinates

2. **Debug Callbacks**
   - Check laravel.log for incoming data
   - Log the callback_data value
   - Use callback_query_id to acknowledge

3. **Better Notifications**
   - Add emoji for visual clarity
   - Include user names in messages
   - Use HTML formatting for emphasis

4. **Improve UX**
   - Set `one_time_keyboard: true` for less clutter
   - Use `resize_keyboard: true` for mobile
   - Group related buttons together

---

## 📚 Related Files

- Controller: `app/Http/Controllers/TelegramWebhookController.php`
- Models: `app/Models/User.php`, `JoinedSession.php`, `RunningSession.php`
- Routes: `routes/api.php`
- Migration: `database/migrations/*joined_sessions*.php`
- Full Docs: `TELEGRAM_BOT_FLOW.md`


