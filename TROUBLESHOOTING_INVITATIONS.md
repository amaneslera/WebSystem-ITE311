# Quick Test - Are invitations working?

## Step-by-Step Test:

### 1. Verify the table exists
Open phpMyAdmin or run:
```sql
SHOW TABLES LIKE 'enrollment_invitations';
```
You should see the table listed.

### 2. Clear your browser cache
Press **Ctrl + Shift + Delete** and clear cache, or try **Ctrl + F5** to hard refresh.

### 3. Test as Teacher (NEW enrollment)

**Login as teacher:**
- Email: `jim@lms.com`
- Password: `jim123`

**Enroll a student:**
1. Go to "My Courses"
2. Click "View Students" on any course
3. Click "Enroll Student" button
4. Select a student (e.g., Hezekiah Bernasor)
5. Click "Enroll" button
6. **You should see:** "Invitation sent successfully! Student will be notified."
   - If you see "Student enrolled successfully" - the old code is running!

### 4. Test as Student

**Login as student:**
- Email: `hezekiah@gmail.com`  
- Password: `student123`

**Check dashboard:**
1. You should see a **YELLOW alert box** at the top
2. It says "You have 1 pending course invitation"
3. Click the "View Invitations" button
4. You'll see the invitation with **Accept** and **Decline** buttons

### 5. Alternative - Direct URL

If the alert doesn't show, try going directly to:
```
http://localhost/enrollment/my-invitations
```

---

## Troubleshooting:

### If you see "Student enrolled successfully" instead of "invitation sent":
The browser is using cached JavaScript. Try:
1. Press Ctrl + F5 to hard refresh
2. Clear browser cache completely
3. Close and reopen browser

### If alert doesn't appear but invitation exists:
1. Check browser console for JavaScript errors (F12)
2. Verify PHP is loading the model correctly
3. Check if database query is working

### If enrollment_invitations table is empty:
The migration ran but no invitation was created yet. Try enrolling a student again AFTER clearing cache.

### Manual Database Check:
Run this in phpMyAdmin:
```sql
SELECT ei.*, 
       u.name as student_name, 
       c.course_name, 
       c.course_code
FROM enrollment_invitations ei
JOIN users u ON ei.user_id = u.id
JOIN courses c ON ei.course_id = c.id
WHERE ei.status = 'pending';
```

If this returns results, invitations exist and should show on dashboard.

---

## Quick Fix if Still Not Working:

Clear all caches and try student self-enrollment:
1. Login as student
2. Browse courses
3. Click "Enroll" on a course you're NOT enrolled in
4. You should see: "Enrollment request submitted! Please wait for approval."
5. Login as teacher/admin
6. You should see blue alert for pending requests

---

The invitation system ONLY works for enrollments done AFTER the migration. Any enrollments before that were direct enrollments.
