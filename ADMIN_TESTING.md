# Testing the Admin Dashboard

## Quick Start

### 1. Access the Dashboard
Navigate to: `http://127.0.0.1:8000/admin/dashboard`

**Note:** You must be logged in as an admin user (where `is_admin = 1` in database)

### 2. Test Login Flow
1. Log in with an admin account email/password
2. If user has `is_admin = 1`, you'll be redirected to `/admin/dashboard`
3. If user doesn't have admin privileges, you'll be redirected to `/user/dashboard`

## Setting Up an Admin User

If you need to create an admin user, run:

```php
// In tinker console
php artisan tinker

// Create admin user
$user = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@stridesync.local',
    'password' => bcrypt('password123'),
    'is_admin' => true,
]);

// Or update existing user
$user = App\Models\User::find(1);
$user->is_admin = true;
$user->save();
```

## Dashboard Elements to Test

### 1. Statistics Cards
- [ ] Total Users count displays correctly
- [ ] Telegram Users shows correct conversion rate
- [ ] Complete Profiles shows accurate percentage
- [ ] Active Sessions shows current count

### 2. Recent Registrations Section
- [ ] List displays recent users
- [ ] Telegram badge shows for users with telegram_id
- [ ] Profile completion badge displays correctly
- [ ] Click on user opens modal with details

### 3. Active Sessions Section
- [ ] Displays only sessions where start_time <= now AND end_time >= now
- [ ] Shows participant count
- [ ] Click opens session details modal

### 4. Telegram Bot Status
- [ ] Shows registered telegram users
- [ ] Shows incomplete profiles count
- [ ] "Manage Bot" button links to telegram management

### 5. Most Active Users
- [ ] Lists users by session count
- [ ] Shows count of sessions created

### 6. Upcoming Sessions
- [ ] Shows sessions where start_time > now
- [ ] Sorted by start_time ascending
- [ ] Click opens session details

### 7. Past Sessions Table
- [ ] Shows completed sessions
- [ ] Displays organizer name
- [ ] Shows location
- [ ] Shows completion date
- [ ] Displays participant count

### 8. Modals
- [ ] User details modal shows all user info
- [ ] Session details modal shows session info and participants
- [ ] Modals close on X button
- [ ] Modals close on escape key
- [ ] Modals close when clicking outside

### 9. Real-Time Features
- [ ] Stats refresh automatically every 30 seconds (check console)
- [ ] No page reload needed for updates

## API Endpoints to Test

```bash
# Get statistics (JSON)
curl http://127.0.0.1:8000/admin/stats \
  -H "Accept: application/json"

# Response should be:
{
  "totalUsers": 10,
  "usersWithTelegram": 8,
  "activeSessions": 2,
  "totalParticipations": 15,
  "timestamp": "2024-12-10T10:30:00+00:00"
}
```

## Troubleshooting

### Problem: "Target class [admin] does not exist"
**Solution:** This has been fixed. Ensure you're using the updated routes/web.php and AdminController.

### Problem: 403 Unauthorized
**Solution:** The logged-in user doesn't have `is_admin = 1`. Update the user record:
```bash
php artisan tinker
$user = App\Models\User::find(1);
$user->is_admin = true;
$user->save();
```

### Problem: Can't access `/admin/dashboard`
**Solution:** Make sure you're logged in first. If not logged in, Laravel will redirect you to the login page.

### Problem: Modal not loading user/session details
**Solution:** Check browser console for errors. Ensure the fetch URLs are correct:
- `/admin/users/{id}` - User details
- `/admin/sessions/{id}` - Session details

## Sample Test Data

To generate test data, you can create sample sessions:

```php
php artisan tinker

// Create a user
$user = App\Models\User::create([
    'name' => 'John Runner',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
    'telegram_id' => '123456789',
    'gender' => 'Male',
    'avg_pace' => '8:30 min/km',
    'location' => 'Downtown Park'
]);

// Create a running session
$session = App\Models\RunningSession::create([
    'user_id' => $user->id,
    'start_time' => now()->addHours(1),
    'end_time' => now()->addHours(2),
    'average_pace' => '8:30 min/km',
    'duration' => '60 minutes',
    'location_name' => 'Downtown Park'
]);

// Add participants
$participant = App\Models\User::create([
    'name' => 'Jane Jogger',
    'email' => 'jane@example.com',
    'password' => bcrypt('password')
]);

App\Models\JoinedSession::create([
    'session_id' => $session->id,
    'user_id' => $participant->id,
    'joined_at' => now()
]);
```

## Notes

- The dashboard uses a dark theme with Tailwind CSS
- All admin routes are protected with the `admin` middleware
- Statistics are fetched from the database on page load
- Real-time updates happen via AJAX to `/admin/stats`
- User and session details are loaded on-demand via modals


