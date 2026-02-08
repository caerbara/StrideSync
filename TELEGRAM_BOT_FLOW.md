# StrideSync Telegram Bot - Complete Flow Documentation

## Overview
Your Telegram bot implements a complete running buddy and session management system with 5 main features:
1. **My Profile** - View and edit user profile
2. **Find Buddy** - Find nearby runners and send invitations
3. **Check Invitations** - Accept/decline buddy invitations
4. **Running Sessions** - Browse and join nearby running sessions
5. **Create Session** - Link to website for creating running sessions

---

## Bot User Journey

### 1. Initial Setup (New User)

When a user sends `/start`:
- Bot creates a user account automatically
- Asks for **Gender** (Male/Female buttons)
- After gender selection, asks for **Average Running Pace** (e.g., 6:30/km)
- Requests **Current Location** via location sharing button
- Once profile is complete, user sees the **Main Menu**

```
/start → Select Gender → Enter Pace → Share Location → Main Menu
```

---

## Main Menu (5 Buttons)

After profile completion, user gets a main menu with 5 buttons:

```
┌─────────────────────────────┐
│  ✅ Welcome back, User!     │
│  What would you like to do? │
├─────────────────────────────┤
│  [👤 My Profile]            │
│  [🤝 Find Buddy]            │
│  [💬 Check Invitations]     │
│  [🏃 Running Sessions]      │
│  [➕ Create Session]        │
└─────────────────────────────┘
```

---

## Feature Details

### 1. 👤 My Profile

**Purpose:** View and edit profile information

**Flow:**
```
Click "My Profile"
    ↓
Display Profile Info:
├─ Name
├─ Gender
├─ Average Pace
└─ Location Status
    ↓
Edit Options (Inline Buttons):
├─ ✏️ Edit Gender
├─ ✏️ Edit Pace
└─ 📍 Update Location
```

**What Happens:**
- Shows current profile data
- User can click buttons to edit any field
- Editing gender/pace uses inline buttons or text input
- Editing location requests location sharing

---

### 2. 🤝 Find Buddy

**Purpose:** Discover nearby runners and send running invitations

**Flow:**
```
Click "Find Buddy"
    ↓
Check Profile Completion:
├─ ❌ If incomplete → Show error message
└─ ✅ If complete → Find nearby users
    ↓
Find Nearby Runners:
├─ Within 5km radius
├─ Show up to 10 closest users
└─ Display for each:
   ├─ Name
   ├─ Average Pace
   └─ Gender
    ↓
User Actions:
└─ Click "✅ Invite to Run" button
    ↓
Invitation Sent:
├─ Confirmation to sender
└─ Notification to receiver
    ├─ Receiver sees in "Check Invitations"
    ├─ Can Accept or Decline
    └─ Sender gets notified of response
```

**Database Action:**
- Creates `JoinedSession` record with:
  - `user_id`: person sending invitation
  - `invited_user_id`: person receiving invitation
  - `status`: 'invited'
  - `session_id`: NULL (not a session join)

---

### 3. 💬 Check Invitations

**Purpose:** View and manage buddy invitations received

**Flow:**
```
Click "Check Invitations"
    ↓
Fetch Received Invitations:
├─ From: joined_sessions.invited_user_id = current_user.id
├─ Show each person who invited you
└─ Display:
   ├─ Name
   ├─ Average Pace
   └─ "Wants to run with you!"
    ↓
User Actions (Inline Buttons):
├─ ✅ Accept
│  └─ Updates status to 'accepted'
│     └─ Notifies sender: "User accepted!"
└─ ❌ Decline
   └─ Deletes invitation
      └─ Conversation ends
```

**Database Actions:**
- Accept: Updates `status = 'accepted'`
- Decline: Deletes the record

---

### 4. 🏃 Running Sessions

**Purpose:** Browse nearby running sessions and join them

**Flow:**
```
Click "Running Sessions"
    ↓
Check Profile Completion:
├─ ❌ If incomplete → Show error
└─ ✅ If complete → Find nearby sessions
    ↓
Find Nearby Sessions:
├─ Within 5km radius of user
├─ Status ≠ 'completed'
├─ Show up to 10 closest sessions
└─ Display for each:
   ├─ Session Name
   ├─ Scheduled Date & Time
   ├─ Distance (km)
   └─ Participant Count
    ↓
User Actions:
└─ Click "✅ Join Session" button
    ↓
Join Session:
├─ Check if already joined
│  └─ If yes → Show "Already joined!" alert
└─ If no:
   ├─ Creates JoinedSession record
   ├─ Confirms to user: "You joined session!"
   └─ Notifies session creator:
      └─ "User joined your session!"
```

**Database Action:**
- Creates `JoinedSession` record with:
  - `session_id`: running_sessions.session_id
  - `user_id`: current user
  - `invited_user_id`: NULL
  - `status`: 'joined'

---

### 5. ➕ Create Session

**Purpose:** Guide users to website for creating sessions

**Flow:**
```
Click "Create Session"
    ↓
Display Guide Message:
├─ Link: https://stridesync.app/sessions/create
├─ Option to click link
└─ QR code (optional)
    ↓
User Action:
└─ Click link → Opens website
   └─ User creates session on website
      └─ Session appears in "Running Sessions"
```

---

## Database Schema

### Relevant Tables

#### `users` table
```
id (PK)
telegram_id (unique)
name
email
password
gender (Male/Female/Other)
avg_pace (string: "6:30/km")
location (JSON: {latitude, longitude, updated_at})
telegram_state (initial|waiting_gender|waiting_pace|waiting_location|profile_complete)
```

#### `joined_sessions` table
```
jsession_id (PK)
session_id (FK to running_sessions, nullable)
user_id (FK to users)
invited_user_id (FK to users, nullable) ← NEW
status (joined|invited|accepted|declined) ← NEW
joined_at (timestamp)
created_at (timestamp)
updated_at (timestamp)
```

#### `running_sessions` table
```
session_id (PK)
user_id (FK to users)
name
scheduled_at
location (JSON: {latitude, longitude})
distance
status (active|completed)
...
```

---

## Location-Based Search Algorithm

The bot uses **Haversine formula** to calculate distances:

```php
// Find users/sessions within 5km radius
Distance = calculateDistance(userLat, userLon, otherLat, otherLon)

If Distance ≤ 5km → Show in list
```

**Example:**
- User at: 40.7128°N, 74.0060°W (New York)
- Shows runners/sessions within 5km radius
- About 5-10 minute radius depending on area density

---

## User States (telegram_state)

User progression through states:

```
initial
    ↓
waiting_gender (user selects gender)
    ↓
waiting_pace (user enters pace)
    ↓
waiting_location (user shares location)
    ↓
profile_complete (user sees main menu)
    ↓
[User stays in this state, can edit anytime]
```

---

## Button Types

### 1. Reply Keyboard (Persistent at bottom)
- Used for: Main menu buttons, location sharing
- Always visible until dismissed
- Can use `request_location` for location sharing

Example:
```
[👤 My Profile] [🤝 Find Buddy]
[💬 Check Invitations] [🏃 Running Sessions]
[➕ Create Session]
```

### 2. Inline Buttons (In-message clickable)
- Used for: Single-choice selections, confirmations
- Inline with message
- Must use `callback_data` (not text)

Example:
```
"Select your gender:"
[👨 Male] [👩 Female]
```

---

## Callback Query Handlers

When user clicks inline button, triggers `handleCallbackQuery()`:

```
gender_male / gender_female
    → Updates gender, asks for pace

invite_buddy_{buddyId}
    → Creates invitation, notifies both users

accept_invite_{invitationId}
    → Updates status to 'accepted', notifies sender

decline_invite_{invitationId}
    → Deletes invitation

join_session_{sessionId}
    → Creates JoinedSession, notifies creator

edit_gender / edit_pace / edit_location
    → Triggers edit flow
```

---

## Error Handling

### Profile Not Complete
Any feature requiring location will:
- Check if `user->telegram_state === 'profile_complete'`
- If not → Show error: "❌ Please complete your profile first"

### Already Joined Session
- Prevents duplicate joins
- Shows alert: "You already joined this session!"

### No Results
- Find Buddy: "😢 No runners found in your area yet"
- Sessions: "🏃 No running sessions in your area yet"

---

## API Calls to Telegram

All communication via Telegram Bot API:

```php
// Send message
POST /sendMessage
{
    chat_id, text, parse_mode='HTML', reply_markup
}

// Answer callback (removes spinning loading)
POST /answerCallbackQuery
{
    callback_query_id, text, show_alert
}

// Send photo (optional for QR code)
POST /sendPhoto
{
    chat_id, photo
}
```

---

## Testing the Bot

### 1. Set Webhook
```
GET /api/telegram/set-webhook
```

### 2. Check Webhook Status
```
GET /api/telegram/webhook-info
```

### 3. Send Test Message
- Open Telegram
- Search for your bot by username
- Click Start
- Follow the flow

---

## Environment Variables Needed

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
```

---

## Future Enhancements

1. **QR Code for Session Creation** - Generate QR codes linking to website
2. **Photo Verification** - Users can upload running photos
3. **Ratings** - Rate buddy/session experiences
4. **Real-time Notifications** - Push notifications for invitations
5. **Strava Integration** - Link Strava activities
6. **Advanced Filtering** - Filter by pace, gender, experience level
7. **Chat Feature** - Direct messaging between buddies

---

## Code Structure

### Main Controller: `TelegramWebhookController`

**Sections:**
1. Main Handler - Webhook entry point
2. Message Handler - Route by text/state
3. Main Menu - Display 5 buttons
4. Profile Management - View/edit profile
5. Find Buddy - Discover nearby runners
6. Check Invitations - Manage buddy requests
7. Running Sessions - Browse nearby sessions
8. Create Session - Link to website
9. Callback Query Handler - Handle button clicks
10. Helper Functions - API calls, calculations
11. Webhook Setup - Configure Telegram webhook

**Key Methods:**
- `handle()` - Main webhook handler
- `handleMessage()` - Route messages
- `showMainMenu()` - Display 5 button menu
- `showMyProfile()` - Profile view/edit
- `showFindBuddy()` - Find nearby runners
- `showCheckInvitations()` - Manage invitations
- `showRunningSessions()` - Browse sessions
- `showCreateSessionGuide()` - Link to website
- `handleCallbackQuery()` - Button click handler
- `calculateDistance()` - Haversine formula

---

## Summary

Your bot creates a complete **social running network** where users can:
- ✅ Build their profile (gender, pace, location)
- ✅ Discover running buddies nearby
- ✅ Send/receive buddy invitations
- ✅ Browse and join group sessions
- ✅ Create sessions on your website

All features are location-aware (5km radius) and include real-time notifications!



