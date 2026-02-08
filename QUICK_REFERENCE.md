# StrideSync Admin - Quick Reference Guide

## 🚀 Quick Start

### URLs
| Feature | URL | Method |
|---------|-----|--------|
| Dashboard | `/admin/dashboard` | GET |
| User List | `/users` | GET |
| Create User | `/users/create` → `/users` | GET → POST |
| Edit User | `/users/{id}/edit` → `/users/{id}` | GET → PUT |
| View User | `/users/{id}` | GET |
| Delete User | `/users/{id}` | DELETE |
| Telegram Settings | `/admin/telegram` | GET |

---

## 👥 User Management

### Create User Form Fields
```
• Name (required)
• Email (required, unique)
• Password (required, min 6 chars)
• Password Confirmation (required)
• Gender (optional)
• Pace (optional)
• Location (optional)
```

### Edit User Form Fields
```
Same as create PLUS:
• Telegram ID (read-only in view)
• Admin Privilege toggle
• Account dates (display only)
```

### User Validation Rules
```
name: required|string|max:255
email: required|email|unique:users
password: required|string|min:6|confirmed (create only)
password: string|min:6|confirmed (update, optional)
gender: nullable|string
avg_pace: nullable|string
location: nullable|string
```

---

## 🤖 Telegram Bot Management

### Status Cards
| Card | Shows |
|------|-------|
| Bot Status | Active/Offline |
| Webhook Status | Connected/Not Set |
| Total Users | User count |
| Telegram Linked | Count & percentage |

### Forms & Actions
| Action | Max Length | Endpoint |
|--------|-----------|----------|
| Broadcast Message | 4096 chars | `/admin/telegram/broadcast` |
| Short Description | 120 chars | `/admin/telegram/update-short-description` |
| Full Description | 512 chars | `/admin/telegram/update-description` |
| Webhook Setup | N/A | `/admin/telegram/set-webhook` |
| Webhook Remove | N/A | `/admin/telegram/remove-webhook` |

### Broadcast Message HTML Support
```html
<b>bold</b>
<i>italic</i>
<u>underline</u>
<code>code</code>
```

---

## 📊 Dashboard Statistics

**Real-time Cards:**
- Total registered users
- Telegram-connected users
- Active running sessions
- Total sessions created

**Monitoring Sections:**
- Recent user registrations
- Live running sessions
- Telegram statistics
- Most active users
- Upcoming sessions
- Past sessions history

**Auto-refresh:** Every 30 seconds via AJAX

---

## 🔐 Admin Requirements

### To Access Admin Features
```php
// User must have:
$user->is_admin === 1 // Boolean flag in users table

// Routes protected by:
Route::middleware(['auth', 'admin'])->group(...)
```

### Admin Users in System
```
Current Admin:
• Name: Syahir Hafiz
• Email: syahir@gmail.com
• ID: 1 (database)
```

---

## 📁 File Structure

```
Controllers:
├── AdminController.php (137 lines)
│   └── dashboard(), getStats(), viewUser(), viewSession()
├── UserController.php (100+ lines)
│   └── index(), create(), store(), show(), edit(), update(), destroy()
└── TelegramAdminController.php (263 lines)
    └── index(), setWebhook(), removeWebhook(), broadcast(), etc.

Views:
├── admin/dashboard.blade.php (353 lines)
├── admin/users/
│   ├── index.blade.php (list)
│   ├── create.blade.php (form)
│   ├── edit.blade.php (form)
│   └── show.blade.php (profile)
└── admin/telegram/
    └── index.blade.php (management)

Models:
├── User.php (fillable: is_admin, telegram_id, gender, etc.)
├── RunningSession.php (casts: start_time, end_time)
└── JoinedSession.php
```

---

## ✅ Verification Checklist

```
✓ AdminController exists & works
✓ UserController CRUD complete
✓ TelegramAdminController configured
✓ All views created & cached
✓ Database tables & fields present
✓ Admin middleware registered
✓ Routes protected with auth + admin
✓ User model includes is_admin in fillable
✓ Pagination working (15/page)
✓ Date formatting safe
✓ Form validation active
✓ Modal popups functional
✓ Auto-refresh working
✓ Telegram webhook available
✓ Broadcast messaging enabled
```

---

## 🚦 Common Operations

### Create Admin User
```php
php artisan tinker
> User::create([
    'name' => 'Admin Name',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'is_admin' => 1
]);
```

### Make Existing User Admin
```php
$user = User::find(1);
$user->update(['is_admin' => 1]);
```

### Clear Cache (if needed)
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Test Telegram Configuration
```
1. Have valid TELEGRAM_BOT_TOKEN in .env
2. Set webhook via admin panel
3. Bot should receive messages
4. Check logs: storage/logs/
```

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| "Admin access required" | User must have is_admin=1 |
| "Target class [admin] does not exist" | Verify Kernel.php middleware config |
| "Format on string" error | RunningSession model has datetime casting |
| Webhook not connecting | Check TELEGRAM_BOT_TOKEN, ensure HTTPS |
| Users not in list | Check is_admin flag, should be 0 for listing |
| Form validation missing | Run `php artisan cache:clear` |
| Views not updating | Run `php artisan view:clear` |

---

## 📞 API Endpoints (Admin Only)

### GET Endpoints
```
/admin/dashboard              → Main dashboard
/admin/stats                  → JSON statistics
/admin/users/{id}             → View specific user
/admin/sessions/{id}          → View specific session modal
/users                        → User list (paginated)
/users/{id}                   → User details
/admin/telegram               → Telegram management
```

### POST Endpoints
```
/users                           → Create user
/users/{id}                      → Update user
/users/{id}                      → Delete user
/admin/telegram/set-webhook      → Configure webhook
/admin/telegram/remove-webhook   → Remove webhook
/admin/telegram/broadcast        → Send broadcast message
/admin/telegram/send-message     → Send individual message
/admin/telegram/update-description → Set bot description
/admin/telegram/update-short-description → Set short description
```

---

## 🎨 UI Theme

**Colors:**
- Primary Accent: `#a1e8c5` (emerald)
- Dark Background: `#000000` (black)
- Card Background: `#1F2937` (gray-800)
- Text: `#FFFFFF` (white)
- Subtle Text: `#9CA3AF` (gray-400)

**Styling:**
- Tailwind CSS
- Dark mode optimized
- Responsive design
- Glass-morphism effects

---

## 📝 Documentation Files

1. **ADMIN_IMPLEMENTATION_COMPLETE.md** - Full implementation details
2. **TESTING_GUIDE.md** - 23-point testing checklist
3. **README.md** - Project overview
4. **ADMIN_SOLUTION.md** - Solution summary
5. **CODE_CHANGES.md** - Detailed code changes

---

## 🎯 Feature Summary

**User Management:**
- ✅ Create users with validation
- ✅ List users with pagination
- ✅ View user profiles
- ✅ Edit user information
- ✅ Delete users (admin protected)
- ✅ Toggle admin privileges

**Telegram Bot Control:**
- ✅ Configure webhook
- ✅ Monitor bot status
- ✅ Update descriptions
- ✅ Broadcast messages
- ✅ Send individual messages
- ✅ View statistics

**Dashboard Monitoring:**
- ✅ Real-time statistics
- ✅ User activity tracking
- ✅ Session monitoring
- ✅ Auto-refresh (30s)
- ✅ Modal details

---

## 🔍 Key Improvements Made

1. **Fixed Date Formatting:** Added safe handling for Carbon dates vs strings
2. **Added User Model Fields:** is_admin now in fillable array
3. **Complete User CRUD:** All operations with validation
4. **Telegram Management:** Full bot control interface
5. **Security:** Admin middleware protecting all routes
6. **Validation:** Form validation on all operations
7. **UX:** Modal popups, auto-refresh, pagination
8. **Database:** All necessary fields and relationships

---

## ✨ Status: PRODUCTION READY

All components verified and tested. System ready for live deployment.

- **Syntax:** ✅ No errors
- **Validation:** ✅ Active
- **Security:** ✅ Protected
- **Performance:** ✅ Optimized
- **Testing:** ✅ Complete

---

*Last Updated: Current Session*
*Admin System Version: 2.0 (Complete)*


