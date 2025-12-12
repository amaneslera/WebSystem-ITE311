# How to See and Accept Course Invitations

## For Students 👨‍🎓

When a teacher or admin enrolls you in a course, you'll now see **accept/decline buttons** in these places:

### 1. Dashboard Alert (NEW! ✨)
- Log in to your student account
- On your dashboard, you'll see a **yellow alert box** at the top if you have pending invitations
- The alert shows how many invitations you have
- Click **"View Invitations"** button to see them

### 2. Direct Link
Visit: `http://localhost/enrollment/my-invitations`

### 3. What You'll See
Each invitation shows:
- Course name and code
- Who invited you (teacher/admin name and role)
- Their message (if any)
- **Accept button** (green) ✅
- **Decline button** (red) ❌

### 4. Accepting/Declining
1. Click Accept or Decline
2. A modal opens where you can add an optional message
3. Click confirm
4. You'll be enrolled (if accepted) and the inviter will be notified!

---

## For Teachers 👨‍🏫

When students request to enroll in your courses, you'll see:

### 1. Dashboard Alert (NEW! ✨)
- Blue alert box shows pending enrollment requests
- Shows count of requests for your courses
- Click **"Review Requests"** to approve/decline

### 2. Direct Link
Visit: `http://localhost/enrollment/pending-requests`

### 3. What You'll See
Each request shows:
- Student name, email, and ID
- Course they want to join
- Their request message (if any)
- **Approve button** (green) ✅
- **Decline button** (red) ❌

---

## For Admins 🔐

Admins see ALL enrollment requests across the system:

### 1. Dashboard Alert (NEW! ✨)
- Blue alert box shows ALL pending requests
- Click **"Review All Requests"** to manage them

### 2. Direct Link
Visit: `http://localhost/enrollment/pending-requests`

---

## Quick Test Steps 🧪

### Test 1: Teacher Invites Student
1. **As Teacher**: Log in (jim@lms.com / jim123)
2. Go to your courses
3. Click "View Students" on a course
4. Click "Enroll Student"
5. Select a student and click "Enroll"
6. You'll see: "Invitation sent successfully!"

7. **As Student**: Log in (aman.eslera@gmail.com / Draine010101)
8. Check your dashboard - you'll see a **yellow alert box**!
9. Click "View Invitations"
10. You'll see the invitation with **Accept** and **Decline** buttons
11. Click Accept
12. Add optional message and confirm
13. You're now enrolled! ✅

### Test 2: Student Requests Enrollment
1. **As Student**: Browse available courses
2. Click "Enroll" on a course you're not in
3. You'll see: "Enrollment request submitted!"

4. **As Teacher**: Check your dashboard
5. You'll see a **blue alert box** for pending requests
6. Click "Review Requests"
7. See the student's request with **Approve** and **Decline** buttons
8. Click Approve
9. Student gets enrolled and notified! ✅

---

## Common Issues ⚠️

**Q: I don't see the accept/decline buttons!**
- Make sure you're going to `/enrollment/my-invitations` (not the dashboard)
- Check the yellow alert box on dashboard for the link
- Make sure the invitation was created (teacher should see "invitation sent" not "enrolled")

**Q: I clicked enroll but student was enrolled immediately**
- The invitation system only works for NEW enrollments
- Make sure the migration was run: `php spark migrate`
- Check if files were updated correctly

**Q: Dashboard doesn't show the alert**
- Refresh the page (Ctrl+F5)
- Make sure you have pending invitations/requests
- Check if EnrollmentInvitationModel is loaded correctly

---

## Database Check 🔍

To verify invitations were created, you can check via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database (lms_eslera)
3. Click on `enrollment_invitations` table
4. You should see records with:
   - `type = 'invitation'` (teacher/admin invited student)
   - `type = 'request'` (student requested enrollment)
   - `status = 'pending'` (not yet accepted/declined)

---

## Test Accounts 🔑

- **Admin**: draine@gmail.com / admin123
- **Teacher**: jim@lms.com / jim123
- **Student 1**: aman.eslera@gmail.com / Draine010101
- **Student 2**: crystalherda@gmail.com / fooxft1
- **Student 3**: hezekiah@gmail.com / student123

---

Now try enrolling a student and you should see the **yellow alert box** on the student dashboard with a button to view invitations where they can accept or decline! 🎉
