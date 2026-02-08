# StrideSync Telegram Bot - Visual Diagrams

## 1. Complete User Journey

```
┌─────────────────────────────────────────────────────────────┐
│                    TELEGRAM BOT FLOW                        │
└─────────────────────────────────────────────────────────────┘

                        User sends /start
                             ↓
                     [CREATE USER ACCOUNT]
                             ↓
                    Select Gender (inline)
                    [👨 Male] [👩 Female]
                             ↓
                     Enter Running Pace
                    (Text input: 6:30/km)
                             ↓
                    Share Location Button
                  [📍 Send My Location]
                             ↓
                  ✅ PROFILE COMPLETE ✅
                             ↓
            ┌─────────────────────────────┐
            │     MAIN MENU (5 BUTTONS)   │
            ├─────────────────────────────┤
            │  1. 👤 My Profile           │
            │  2. 🤝 Find Buddy           │
            │  3. 💬 Check Invitations    │
            │  4. 🏃 Running Sessions     │
            │  5. ➕ Create Session       │
            └─────────────────────────────┘
```

---

## 2. Feature #1: My Profile Flow

```
User clicks "👤 My Profile"
        ↓
    [DISPLAY PROFILE]
    ├─ Name: John
    ├─ Gender: Male
    ├─ Pace: 6:30/km
    └─ Location: ✅ Set
        ↓
  [EDIT OPTIONS - Inline Buttons]
  ├─ [✏️ Edit Gender]
  ├─ [✏️ Edit Pace]
  └─ [📍 Update Location]
        ↓
  User selects one option
        ↓
  [EDIT MODE]
  ├─ Gender → Gender buttons
  ├─ Pace → Text input
  └─ Location → Share location
        ↓
  [SAVE & SHOW MAIN MENU]
```

---

## 3. Feature #2: Find Buddy Flow (Invitation System)

```
User clicks "🤝 Find Buddy"
        ↓
   [VALIDATE PROFILE]
   ├─ Has location? YES ✅
   └─ No → Show error & exit
        ↓
[FIND NEARBY RUNNERS - 5km radius]
├─ All users within 5km
├─ Max 10 closest users
├─ Ordered by distance
└─ For each user:
        ↓
   ┌──────────────────┐
   │  👤 NAME         │
   │  ⚡ 6:30/km      │
   │  👥 Male         │
   │ [✅ Invite]      │
   └──────────────────┘
        ↓
User clicks "✅ Invite to Run"
        ↓
[CREATE INVITATION]
INSERT joined_sessions (
  user_id=123,           ← Sender
  invited_user_id=456,   ← Receiver
  status='invited'
)
        ↓
[SEND NOTIFICATIONS]
├─ Sender: "✅ Invite sent to John!"
└─ Receiver: "🤝 Jane invited you!"
```

---

## 4. Feature #3: Check Invitations Flow

```
User clicks "💬 Check Invitations"
        ↓
[FIND INCOMING INVITATIONS]
SELECT from joined_sessions WHERE
  invited_user_id = current_user_id
  AND status IN ('invited', 'pending')
        ↓
No results? "💬 No new invitations"
Yes? Show each:
        ↓
   ┌──────────────────┐
   │  👤 JOHN         │
   │  ⚡ 6:30/km      │
   │  Wants to run!   │
   │ [✅] [❌]        │
   └──────────────────┘
        ↓
User clicks button
        ↓
    ┌─── Accept ───┐
    │              ↓
    │        UPDATE status='accepted'
    │              ↓
    │        Notify SENDER:
    │        "✅ User accepted!"
    │
    │
    └─── Decline ──┐
                   ↓
              DELETE record
                   ↓
              Conversation ends
```

---

## 5. Feature #4: Running Sessions Flow

```
User clicks "🏃 Running Sessions"
        ↓
   [VALIDATE PROFILE]
   ├─ Has location? YES ✅
   └─ No → Show error & exit
        ↓
[FIND NEARBY SESSIONS - 5km radius]
├─ All active sessions
├─ Within 5km
├─ Max 10 closest
└─ Ordered by distance
        ↓
No results? "🏃 No sessions nearby"
Yes? Show each:
        ↓
   ┌──────────────────┐
   │  MORNING RUN     │
   │  📅 Dec 12, 7am  │
   │  📍 10km         │
   │  👥 3 members    │
   │ [✅ Join]        │
   └──────────────────┘
        ↓
User clicks "✅ Join Session"
        ↓
   [CHECK IF ALREADY JOINED]
   ├─ Yes? → Alert "Already joined!"
   └─ No? → Continue
        ↓
[JOIN SESSION]
INSERT joined_sessions (
  session_id=789,    ← Session
  user_id=123,       ← User
  status='joined'
)
        ↓
[SEND NOTIFICATIONS]
├─ User: "✅ You joined Morning Run!"
└─ Creator: "👥 John joined your session!"
```

---

## 6. Feature #5: Create Session Flow

```
User clicks "➕ Create Session"
        ↓
[DISPLAY GUIDE]
├─ Message with link:
│  https://stridesync.app/sessions/create
├─ Option: Click link to website
└─ Optional: QR code image
        ↓
User clicks link
        ↓
[OPENS WEBSITE]
Browser → /sessions/create
        ↓
User fills:
├─ Session name
├─ Date & time
├─ Distance
├─ Location
└─ Description
        ↓
User clicks Create
        ↓
[SESSION STORED IN DB]
INSERT running_sessions (...)
        ↓
[RETURNS TO TELEGRAM]
User can see in "🏃 Running Sessions"
Others can join!
```

---

## 7. Database Relationship Diagram

```
┌──────────────┐           ┌────────────────────┐           ┌───────────────┐
│    USERS     │           │ JOINED_SESSIONS    │           │ RUNNING_SESSIONS│
├──────────────┤           ├────────────────────┤           ├───────────────┤
│ id (PK)      │◄──┐       │ jsession_id (PK)   │           │ session_id (PK)
│ name         │   │   ┌───│ session_id (FK) ─────────────┤ user_id (FK) ─┐
│ telegram_id  │   │   │   │ user_id (FK) ──────────┐     │ name          │ │
│ gender       │───┼───┤   │ invited_user_id (FK)   │     │ scheduled_at  │ │
│ avg_pace     │   │   │   │ status                 │     │ location      │ │
│ location     │   │   │   │ joined_at              │     │ distance      │ │
└──────────────┘   │   │   │ created_at             │     │ status        │ │
                   │   │   │ updated_at             │     └───────────────┘ │
                   │   │   └────────────────────────┘                       │
                   │   └──────────────────────────────────────────────────┘
                   │
                   └─────────── 1:M relationship
```

**Relationships:**
- User → JoinedSessions (1:M) - User can have many joined sessions
- User → JoinedSessions (1:M on invited_user_id) - User can receive many invitations
- RunningSession → JoinedSessions (1:M) - Session can have many joined users

---

## 8. Invitation vs Session Join - Side by Side

```
BUDDY INVITATION                    SESSION JOIN
─────────────────────────────────────────────────────

Initiate: User A                    Initiate: User A
Target: Specific User B             Target: Group Session

session_id: NULL                    session_id: 789 (not null)
user_id: A                          user_id: A
invited_user_id: B                  invited_user_id: NULL
status: 'invited'                   status: 'joined'

Interaction: 1-on-1                 Interaction: Group
Notification: B can accept/decline  Notification: Creator notified
Next step: Plan run together        Next step: Run with group

Record in DB:                       Record in DB:
┌─────────────────────┐             ┌──────────────────┐
│ Invitation Table    │             │ Participant List │
├─────────────────────┤             ├──────────────────┤
│ User: A → B         │             │ Session: 789     │
│ Status: invited     │             │ User: A (joined) │
│ Can be: accepted    │             │ Status: joined   │
│         declined    │             │ No interactions  │
│         deleted     │             └──────────────────┘
└─────────────────────┘
```

---

## 9. Message to Callback Flow

```
USER INTERFACE:
┌────────────────────────────┐
│ What is your gender?       │
│                            │
│ [👨 Male] [👩 Female]      │ ← Inline Buttons
└────────────────────────────┘

User clicks "👨 Male"
        ↓
TELEGRAM APP:
Sends callback_query to webhook with:
{
  callback_query_id: "abc123",
  from: { id: 123456789 },
  data: "gender_male"
}
        ↓
YOUR SERVER:
Receives at POST /api/telegram/webhook
        ↓
handleCallbackQuery() {
  $data = "gender_male"
  if (strpos($data, 'gender_') === 0) {
    $gender = str_replace('gender_', '', $data) → 'male'
    User.update(gender: 'Male')
    Send response message
  }
}
        ↓
TELEGRAM API:
answerCallbackQuery(callback_query_id)
        ↓
USER SEES:
Loading spinner disappears
Response message appears
```

---

## 10. Location Distance Calculation

```
USER A                          USER B
  ↓                               ↓
Latitude: 40.7128             Latitude: 40.7589
Longitude: -74.0060           Longitude: -73.9851
  ↓                               ↓
  └─── Haversine Formula ───┘
             ↓
    Distance = 5.2 km
             ↓
   Is 5.2 km ≤ 5 km?
   NO → Don't show User B
   ↓
   [User B filtered out]


SAME SCENARIO:
USER A                          USER C
  ↓                               ↓
Latitude: 40.7128             Latitude: 40.7289
Longitude: -74.0060           Longitude: -73.9965
  ↓                               ↓
  └─── Haversine Formula ───┘
             ↓
    Distance = 1.8 km
             ↓
   Is 1.8 km ≤ 5 km?
   YES → Show User C!
   ↓
   [User C appears in list]
```

---

## 11. State Machine Diagram

```
        ┌──────────────┐
        │   INITIAL    │
        │   (New User) │
        └──────┬───────┘
               │ /start
               ↓
        ┌──────────────────┐
        │  WAITING_GENDER  │ ←─────────┐
        │  Ask for Gender  │           │
        └──────┬───────────┘           │
               │ User selects           │
               ↓                        │
        ┌──────────────────┐           │
        │   WAITING_PACE   │           │
        │  Ask for Pace    │           │
        └──────┬───────────┘           │
               │ User enters pace      │
               ↓                        │
        ┌──────────────────┐           │
        │WAITING_LOCATION  │           │
        │Ask for Location  │           │
        └──────┬───────────┘           │
               │ User shares location   │
               ↓                        │
        ┌──────────────────────┐       │
        │ PROFILE_COMPLETE    │       │
        │ Access main menu    │       │
        └──────┬───────────────┘       │
               │                       │
               └─ Edit profile ────────┘
                  (stay in same state)
```

---

## 12. Notification Flow Chart

```
                    INVITE BUDDY
                        │
        ┌───────────────┴───────────────┐
        │                               │
        ↓                               ↓
    SENDER                          RECEIVER
        │                               │
        ├─ Show: "Invite sent"         ├─ Show: "You're invited!"
        │                               │
        │                        (Check Invitations)
        │                               │
        │                      ┌────────┴────────┐
        │                      │                 │
        │                      ↓                 ↓
        │                   ACCEPT           DECLINE
        │                      │                 │
        │                      ├─ Update DB    ├─ Delete DB
        │                      │                 │
        └──────────┬───────────┴─────────┬──────┘
                   │                     │
                   ↓                     ↓
            Send Notification    No Notification
            "User Accepted!"     (Conversation ends)
                   │
                   ↓
            Both Ready to Run!
```

---

## 13. Complete API Call Sequence

```
CLIENT                              SERVER                    DATABASE
   │                                   │                           │
   ├─ /start ────────────────────────→ │                           │
   │                                   ├─ Create User ────────────→ │
   │                          SELECT by id                          │
   │                                   ←─ User data ───────────────┤
   │
   │                    [Gender Selection - Callback]
   │
   ├─ callback_query ─────────────────→ │                           │
   │ (gender_male)                      ├─ Update User ────────────→ │
   │                                    │ gender='Male'              │
   │ ← sendMessage ─────────────────────┤                           │
   │ "What's your pace?"                │                           │
   │
   │                    [Pace Input - Text Message]
   │
   ├─ Message: "6:30/km" ─────────────→ │                           │
   │                                    ├─ Update User ────────────→ │
   │                                    │ avg_pace='6:30/km'         │
   │ ← sendMessage ────────────────────┤                           │
   │ "Share location"                   │                           │
   │
   │                    [Location Share]
   │
   ├─ Location Data ───────────────────→ │                           │
   │ {lat, lon}                         ├─ Update User ────────────→ │
   │                                    │ location=JSON              │
   │                                    │ state='profile_complete'   │
   │ ← sendMessage ────────────────────┤                           │
   │ "Profile Complete!" + Menu         │                           │
   │
   │              [User clicks "Find Buddy"]
   │
   ├─ Message: "Find Buddy" ──────────→ │                           │
   │                                    ├─ SELECT Users ───────────→ │
   │                                    │ where location IS NOT NULL │
   │                          Calculate distances (Haversine)       │
   │                                    ←─ Nearby users ───────────┤
   │ ← sendMessage x5 ─────────────────┤ (up to 10)                │
   │ "User A: 6:00/km"                 │                           │
   │ [Invite] button                    │                           │
   │
   │              [User clicks "Invite to Run"]
   │
   ├─ callback_query ─────────────────→ │                           │
   │ (invite_buddy_456)                 ├─ INSERT JoinedSession ───→ │
   │                                    │ user_id=123, invited_id=456│
   │                                    │ status='invited'           │
   │                                    │                           │
   │                                    ├─ sendMessage to User A ──┤
   │ ← sendMessage ─────────────────────┤ "Invite sent!"            │
   │                                    │                           │
   │                                    ├─ sendMessage to User B ──┤
   │                                    │ "User A invited you!"     │
   │
   └───────────────────────────────────→ └───────────────────────────┘
```

---

## 14. Error Handling Flow

```
User clicks feature requiring profile
        ↓
IS profile_complete?
   ├─ YES ✅
   │   ↓
   │   Execute feature
   │
   └─ NO ❌
       ↓
   Send error:
   "❌ Please complete your profile first"
       ↓
   Return to main menu


User clicks "Join Session" (already joined)
        ↓
SELECT from joined_sessions WHERE
  session_id = X AND user_id = Y
   ├─ Exists? ✅
   │   ↓
   │   answerCallbackQuery with alert:
   │   "You already joined this session!"
   │   ✋ STOP
   │
   └─ Doesn't exist? ❌
       ↓
       CREATE new joined_sessions record
       Send confirmation messages
```



