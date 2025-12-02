# Soft Delete Quick Reference

## Quick Commands

### Run Migration
```bash
php spark migrate
```

### Rollback Migration (if needed)
```bash
php spark migrate:rollback
```

---

## Common Code Patterns

### ✅ CORRECT - Using UserModel

```php
// Get all active users
use App\Models\UserModel;
$userModel = new UserModel();
$users = $userModel->getActiveUsers();

// Get user by ID (only if active)
$user = $userModel->getUserById($userId);

// Get user with relations
$userWithData = $userModel->getUserWithRelations($userId);

// Soft delete a user
$userModel->softDeleteUser($userId);

// Create a user
$userModel->createUser([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'role' => 'student'
]);

// Update a user
$userModel->updateUser($userId, [
    'name' => 'Updated Name'
]);
```

### ✅ CORRECT - Query Builder with Filter

```php
// When you must use query builder
$db = \Config\Database::connect();
$users = $db->table('users')
    ->where('is_deleted', 0)
    ->get()->getResultArray();

// Joins with users table - ALWAYS add is_deleted filter
$courses = $db->table('courses')
    ->select('courses.*, users.name as teacher_name')
    ->join('users', 'users.id = courses.teacher_id AND users.is_deleted = 0')
    ->get()->getResultArray();
```

### ❌ WRONG - No Filter

```php
// DON'T DO THIS - Shows deleted users
$db = \Config\Database::connect();
$users = $db->table('users')->get()->getResultArray();

// DON'T DO THIS - Shows deleted teachers
$courses = $db->table('courses')
    ->join('users', 'users.id = courses.teacher_id')
    ->get()->getResultArray();
```

---

## UserModel Method Cheat Sheet

| Method | Returns | Description |
|--------|---------|-------------|
| `getActiveUsers()` | array | All active users (is_deleted = 0) |
| `getUserById($id)` | array/null | Single active user or null |
| `getUserWithRelations($id)` | array/null | User + courses/enrollments |
| `softDeleteUser($id)` | bool | Sets is_deleted = 1 |
| `restoreUser($id)` | bool | Sets is_deleted = 0 |
| `createUser($data)` | int/bool | Insert ID or false |
| `updateUser($id, $data)` | bool | True/false |
| `countActiveUsers($role)` | int | Count of active users |
| `getActiveUsersByRole($role)` | array | Users filtered by role |
| `emailExists($email, $excludeId)` | bool | Check email availability |

---

## Join Pattern Examples

### Courses → Users (Teachers)
```php
->join('users', 'users.id = courses.teacher_id AND users.is_deleted = 0')
```

### Enrollments → Users (Students)
```php
->join('users', 'users.id = enrollments.user_id AND users.is_deleted = 0')
```

### Any table → Users
```php
->join('users', 'users.id = {table}.user_id AND users.is_deleted = 0')
```

---

## Testing Checklist

- [ ] Run migration: `php spark migrate`
- [ ] Verify `is_deleted` column exists in database
- [ ] Test soft delete functionality
- [ ] Verify deleted users disappear from UI
- [ ] Test that deleted users cannot login
- [ ] Check dashboard statistics update correctly
- [ ] Verify course enrollments exclude deleted users
- [ ] Test that teacher names don't show if teacher deleted

---

## Database Schema

```sql
-- Added to users table
is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Soft delete flag: 0 = active, 1 = deleted'

-- Index for performance
INDEX idx_is_deleted (is_deleted)
```

---

## Common Queries

### Count Active Users by Role
```php
$totalStudents = $db->table('users')
    ->where('role', 'student')
    ->where('is_deleted', 0)
    ->countAllResults();
```

### Get Deleted Users (Admin Recovery Feature)
```php
$deletedUsers = $db->table('users')
    ->where('is_deleted', 1)
    ->get()->getResultArray();
```

### Check if User is Deleted
```php
$user = $db->table('users')
    ->select('is_deleted')
    ->where('id', $userId)
    ->get()->getRowArray();

if ($user && $user['is_deleted'] == 1) {
    echo "User is soft-deleted";
}
```

---

## File Locations

| File | Location |
|------|----------|
| Migration | `app/Database/Migrations/2025-11-14-000001_AddIsDeletedToUsersTable.php` |
| UserModel | `app/Models/UserModel.php` |
| Users Controller | `app/Controllers/Users.php` |
| Auth Controller | `app/Controllers/Auth.php` |
| Course Controller | `app/Controllers/Course.php` |
| EnrollmentModel | `app/Models/EnrollmentModel.php` |
| Full Documentation | `SOFT_DELETE_IMPLEMENTATION.md` |

---

## Troubleshooting

### Users still showing after delete?
- Clear cache: `php spark cache:clear`
- Check if `is_deleted` column exists
- Verify using UserModel methods

### Login still works for deleted users?
- Check Auth controller has `->where('is_deleted', 0)`
- Clear session data

### Dashboard counts wrong?
- Verify all count queries include `->where('is_deleted', 0)`
- Check Auth::dashboard() method

### Teacher names not showing?
- Verify join includes: `AND users.is_deleted = 0`
- Check if teacher was soft-deleted

---

**Quick Help:** See `SOFT_DELETE_IMPLEMENTATION.md` for full documentation
