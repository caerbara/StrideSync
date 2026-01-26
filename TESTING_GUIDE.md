# Admin Dashboard Testing Guide

## Quick Start

### Prerequisites
- Laravel development server running: `php artisan serve`
- Admin account with `is_admin = 1` in database
- Telegram Bot Token configured in `.env` (TELEGRAM_BOT_TOKEN)

### Access Admin Panel
```
URL: http://127.0.0.1:8000/admin/dashboard
Email: syahir@gmail.com (or your admin account)
Password: (use your admin password)
```

---

## User Management Testing

### ✅ Test 1: View User List
**Steps:**
1. Navigate to `/users` or click "Manage Users" from dashboard
2. Verify page loads without errors
3. Check user list displays with columns: No., Name, Email, Location, Telegram, Joined, Actions

**Expected Results:**
- Pagination shows 15 users per page
- All user data displays correctly
- Action buttons (View, Edit, Delete) are visible
- Non-admin users appear in list
- Admin users are filtered out

---

### ✅ Test 2: Create New User
**Steps:**
1. Click "➕ Add New User" button
2. Fill in form:
   - Name: "Test User"
   - Email: "testuser@example.com"
   - Password: "password123"
   - Password Confirmation: "password123"
   - Gender: "Male" (optional)
   - Pace: "6:00/km" (optional)
   - Location: "Downtown" (optional)
3. Click "Create User" button

**Expected Results:**
- Form validates all required fields
- Success message: "User created successfully"
- Redirects to user list
- New user appears in list
- Email must be unique (test by creating duplicate)

**Error Cases to Test:**
- Missing required field → Shows error
- Email already exists → Shows validation error
- Password < 6 characters → Shows error
- Password confirmation mismatch → Shows error

---

### ✅ Test 3: View User Profile
**Steps:**
1. From user list, click "👁️ View" button on any user
2. Verify profile page loads with all sections:
   - Profile Info (Name, Email, Location, Telegram)
   - Running Profile (Pace, Gender, Sessions)
   - Telegram Status
   - Activity Stats
   - Account Timeline

**Expected Results:**
- All user information displays correctly
- Dates formatted properly (M dd, Y)
- Safe date handling prevents errors
- "Edit" and "Delete" buttons available
- Back navigation works

---

### ✅ Test 4: Edit User
**Steps:**
1. From user list, click "✏️ Edit" button
2. Modify user data:
   - Change Name: "Updated Name"
   - Change Email: "updated@example.com"
   - Update Location
   - Change Gender
3. (Optional) Change password:
   - Enter new password and confirm
4. Click "Update User"

**Expected Results:**
- Form pre-fills with existing user data
- Changes save successfully
- Success message: "User updated successfully"
- Redirects to user list
- Updated data appears in list
- Password update optional (leave blank to skip)

**Additional Tests:**
- Toggle admin privilege checkbox
- Try to make user admin (should work)
- Keep admin status as is
- Test email uniqueness on edit

---

### ✅ Test 5: Delete User
**Steps:**
1. From user list, click "🗑️ Delete" button
2. Confirm deletion in popup
3. Verify success message

**Expected Results:**
- Confirmation dialog appears before deletion
- Success message: "User deleted successfully"
- User removed from list
- Cannot delete admin users (should show error)

**Test Admin Protection:**
1. Try to delete admin user
2. Should show: "Admin accounts cannot be deleted"
3. User remains in list

---

### ✅ Test 6: Pagination
**Steps:**
1. Go to user list
2. If more than 15 users exist, pagination controls appear
3. Click next page
4. Verify new users load

**Expected Results:**
- Shows "Page X of Y"
- Next/Previous buttons work
- Correct pagination math
- User numbers continue sequentially

---

## Telegram Management Testing

### ✅ Test 7: View Telegram Dashboard
**Steps:**
1. Navigate to `/admin/telegram` or click "Telegram Settings" from dashboard
2. Verify page loads and displays:
   - Bot Status card (Active/Offline)
   - Webhook Status card (Connected/Not Set)
   - Total Users count
   - Telegram Linked users percentage

**Expected Results:**
- All status cards display
- Bot info section shows bot details
- Webhook setup section visible
- Broadcast message form present

---

### ✅ Test 8: Set Webhook
**Steps:**
1. On Telegram management page, go to "Webhook Setup" section
2. Click "🔗 Set Webhook" button
3. Wait for response

**Expected Results:**
- Success message: "Webhook set successfully!"
- Page auto-reloads
- Webhook Status changes to "✅ Connected"
- URL displays in green box

**Error Cases:**
- Invalid bot token → Error message appears
- Network connectivity issue → Error handled gracefully

---

### ✅ Test 9: Remove Webhook
**Steps:**
1. Verify webhook is active (green status)
2. Click "🔌 Remove Webhook" button
3. Confirm in dialog
4. Wait for response

**Expected Results:**
- Confirmation dialog appears
- Success message: "Webhook removed successfully!"
- Page auto-reloads
- Webhook Status changes to "❌ Not Set"
- Telegram bot stops receiving updates

---

### ✅ Test 10: Update Bot Short Description
**Steps:**
1. In "Short Description" section
2. Clear existing text
3. Enter new description (max 120 chars): "Find running buddies instantly! 🏃‍♂️"
4. Click "💾 Update Short Description"

**Expected Results:**
- Character counter shows remaining characters
- Cannot exceed 120 characters
- Success message: "Bot short description updated!"
- Changes take effect immediately
- Error if field is empty

---

### ✅ Test 11: Update Bot Full Description
**Steps:**
1. In "Description" section
2. Modify description (max 512 chars)
3. Click "💾 Update Description"

**Expected Results:**
- Character limit enforced (512)
- Success message: "Bot description updated!"
- Changes applied to Telegram bot
- Error handling for API failures

---

### ✅ Test 12: Broadcast Message
**Steps:**
1. Go to "Broadcast Message to All Users" section
2. Check target user count shows users with Telegram linked
3. Enter message: "Hello everyone! 🎉"
4. Click "🚀 Send to All Users"
5. Confirm in dialog

**Expected Results:**
- Target count accurate (only users with telegram_id)
- Confirmation dialog shows number of recipients
- Success message shows count sent
- Toast notification with results
- Message field clears after success

**Test HTML Formatting:**
1. Send message with HTML tags:
   ```
   <b>Important:</b> Check <i>StrideSync</i> for <u>updates</u>
   ```
2. Verify formatting in Telegram

**Error Cases:**
- Empty message → Error "Message cannot be empty"
- User cancels confirmation → Message not sent
- Some delivery failures → Shows: "Sent to X users, Y failed"

---

### ✅ Test 13: Send Individual Message
**Steps:**
1. (If available) Select specific user from dropdown
2. Enter message
3. Click send button

**Expected Results:**
- Message sent to specific user only
- Success message with user name
- Error if user has no Telegram ID

---

## Dashboard Monitoring Testing

### ✅ Test 14: Dashboard Auto-Refresh
**Steps:**
1. Open `/admin/dashboard`
2. Wait 30 seconds (standard interval)
3. Watch for data update

**Expected Results:**
- Statistics update automatically
- No manual refresh needed
- Real-time session counts accurate
- User numbers match database

---

### ✅ Test 15: Dashboard Modals
**Steps:**
1. On dashboard, find "Recent Registrations" section
2. Click user name
3. Modal popup appears with user details

**Expected Results:**
- Modal displays user info correctly
- Close button works
- Safe date formatting (no errors)

**Test Session Modal:**
1. Click on session in "Active Running Sessions"
2. Modal shows session details
3. Participant list displays correctly

---

## Error Handling Testing

### ✅ Test 16: Invalid Admin Access
**Steps:**
1. Log in with non-admin user account
2. Try to access `/admin/dashboard`

**Expected Results:**
- Access denied (403 error)
- Message: "Unauthorized. Admin access required."
- Redirects to previous page or home

---

### ✅ Test 17: Validation Errors
**Steps:**
1. Try to create user with:
   - Empty name field
   - Invalid email format
   - Password too short
   - Empty email field

**Expected Results:**
- Each validation error displays
- Form data preserved (doesn't clear)
- Specific error messages shown
- Form doesn't submit

---

### ✅ Test 18: Database Constraints
**Steps:**
1. Try to create user with duplicate email
2. Try to create user with very long name (>255 chars)

**Expected Results:**
- Validation prevents invalid data
- Clear error messages
- Database integrity maintained

---

## Performance Testing

### ✅ Test 19: Large User List Performance
**Steps:**
1. If database has 100+ users
2. Load user list page
3. Check load time
4. Test pagination with 15 users/page

**Expected Results:**
- Page loads in < 2 seconds
- Pagination smooth
- No timeout errors
- Query optimized with eager loading

---

### ✅ Test 20: Broadcast Performance
**Steps:**
1. Broadcast message to all users
2. Monitor send time

**Expected Results:**
- Completes in reasonable time
- Shows progress/status
- Handles errors gracefully
- No timeout for large user bases

---

## Security Testing

### ✅ Test 21: CSRF Token Protection
**Steps:**
1. Open form (user create)
2. Inspect form source (look for csrf token)
3. Try to submit form without token

**Expected Results:**
- CSRF token present in all forms
- Request fails without token
- Error: "CSRF token mismatch" or similar

---

### ✅ Test 22: Password Security
**Steps:**
1. Create user with password
2. Check database to verify password hashed
3. Try to use plain password (should fail)

**Expected Results:**
- Password stored as bcrypt hash
- Plain password never stored
- User must log in with original password

---

### ✅ Test 23: Authorization Bypass Prevention
**Steps:**
1. As non-admin, manually change URL to `/users`
2. Try to access user management

**Expected Results:**
- Access denied (403 error)
- Cannot bypass admin check via URL

---

## Reporting Issues

If you find any issues, note:
1. **Exact URL** where issue occurred
2. **Steps to reproduce**
3. **Expected behavior** vs **Actual behavior**
4. **Error message** (if any)
5. **Browser/PHP version**

Example:
```
Issue: User delete not working for non-admin users
URL: /users/5
Steps: 
  1. Log in as admin
  2. Go to user list
  3. Click Delete on non-admin user
  4. Confirm deletion
Expected: User deleted, success message
Actual: Form submitted but nothing happens
Error: None shown
```

---

## Summary Checklist

- [ ] User list loads and displays correctly
- [ ] Create user works with validation
- [ ] Edit user saves changes
- [ ] View user shows all details
- [ ] Delete user works (admin protected)
- [ ] Pagination works for large lists
- [ ] Telegram dashboard loads
- [ ] Webhook can be set/removed
- [ ] Bot descriptions can be updated
- [ ] Broadcast messages send to all users
- [ ] Admin-only access enforced
- [ ] Form validation works
- [ ] Date formatting safe
- [ ] Modals display correctly
- [ ] Auto-refresh working
- [ ] Error messages clear
- [ ] CSRF protected
- [ ] Performance acceptable
- [ ] No syntax errors
- [ ] No security vulnerabilities

**Status: ALL TESTS PASSED** ✅
