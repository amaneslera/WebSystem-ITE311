# Lab 9: Search Functionality Testing Guide

## 📊 Database Status
✅ **24 courses** have been seeded and are ready for testing!

### Course Categories Available:
- **Computer Science (CS)**: 10 courses (CS101-CS403)
- **Information Technology (IT)**: 10 courses (IT101-IT401)
- **Legacy Courses**: 4 courses (WEB202, DB301, SYS401, SYS402)

---

## 🧪 Step 7: Complete Testing Instructions

### **Prerequisites:**
1. ✅ Database has 24 courses
2. ✅ Search routes configured (`/courses/search`)
3. ✅ Client-side and server-side search implemented
4. ✅ jQuery loaded on the page

---

## 🔍 Test 1: Access the Search Pages

### **Option A: Dashboard Search (Student View)**
```
URL: http://localhost/ITE311-ESLERA/public/dashboard
Login: hezekiah@gmail.com / student123
```
1. Login as student
2. Scroll to **"Available Courses"** section
3. You should see the search interface at the top

### **Option B: Dedicated Search Page**
```
URL: http://localhost/ITE311-ESLERA/public/courses/search
```
1. Navigate directly to courses search page
2. Search interface loads with all courses

---

## ⚡ Test 2: Client-Side Filtering (Instant Search)

**What to Test:** Real-time filtering without server requests

### Test 2.1: Basic Search
1. **In the search box, type:** `"Data"`
2. **Expected Result:**
   - Courses filter INSTANTLY (no delay)
   - You should see:
     - ✓ **Data Structures and Algorithms** (CS102)
     - ✓ **Database Management Systems** (CS301)
   - Results counter updates automatically

### Test 2.2: Course Code Search
1. **Type:** `"CS"`
2. **Expected Result:**
   - Shows all Computer Science courses (CS101-CS403)
   - Should see: ~10 courses

### Test 2.3: Year Level Search
1. **Type:** `"3rd Year"`
2. **Expected Result:**
   - Filters to 3rd year courses only
   - Should see courses like:
     - CS301, CS302, CS303, CS304
     - IT301, IT302, IT303

### Test 2.4: Technology Keyword Search
1. **Type:** `"Python"`
2. **Expected Result:**
   - Shows "Introduction to Programming" (mentions Python)
   
2. **Type:** `"React"`
3. **Expected Result:**
   - Shows "Full Stack Web Development" and "Mobile App Development"

### Test 2.5: Instructor Name Search (if implemented)
1. **Type:** `"Jim"`
2. **Expected Result:**
   - Shows courses taught by Jim Jamero

### Test 2.6: Clear Functionality
1. After searching, **click "Clear" button**
2. **Expected Result:**
   - All courses reappear
   - Search box clears
   - Counter resets

---

## 🌐 Test 3: Server-Side Search (AJAX)

**What to Test:** Database queries via AJAX without page reload

### Test 3.1: Form Submission
1. **Select "Server-Side Search"** from dropdown (if available)
2. **Type:** `"Web"`
3. **Press Enter** or click Search button
4. **Expected Result:**
   - Loading indicator appears briefly
   - AJAX request to `/courses/search`
   - Results load without page refresh
   - Should show:
     - ✓ Web Development Fundamentals (IT101)
     - ✓ Full Stack Web Development (IT201)
     - ✓ Web Technologies (WEB202)

### Test 3.2: AJAX Search Button
1. **Type:** `"Security"`
2. **Click "AJAX Search" button**
3. **Expected Result:**
   - Server query executes
   - Shows "Cybersecurity and Ethical Hacking" (IT401)
   - No page reload

### Test 3.3: Complex Search Terms
1. **Search for:** `"Machine learning"`
2. **Expected Result:**
   - Finds "Artificial Intelligence" course
   
2. **Search for:** `"Mobile"`
3. **Expected Result:**
   - Finds "Mobile App Development" (CS402)

### Test 3.4: Special Characters
1. **Search for:** `"UI/UX"`
2. **Expected Result:**
   - Finds "UI/UX Design" course (IT203)
   - No errors with special characters

---

## 🚫 Test 4: Empty Results Handling

### Test 4.1: No Matches
1. **Search for:** `"Quantum Physics"`
2. **Expected Result:**
   - Shows message: "No courses found matching 'Quantum Physics'"
   - No broken layout
   - Clear button still works

### Test 4.2: Very Short Search
1. **Search for:** `"Z"`
2. **Expected Result:**
   - Either shows no results or filters correctly
   - No JavaScript errors

### Test 4.3: Empty Search
1. **Clear search box completely**
2. **Submit empty search**
3. **Expected Result:**
   - Shows all courses OR
   - Shows "Enter a search term" message

---

## ✅ Test 5: Success Messages

### Test 5.1: Results Counter
1. **Search for:** `"IT"`
2. **Expected Result:**
   - Success message: "Found X courses matching 'IT'"
   - Counter shows accurate number (should be ~10)

### Test 5.2: Single Result
1. **Search for:** `"Blockchain"`
2. **Expected Result:**
   - Message: "Found 1 course matching 'Blockchain'"
   - Shows "Blockchain and Cryptocurrency" (CS403)

---

## 🎨 Test 6: UI/UX Validation

### Test 6.1: Responsive Design
1. **Resize browser window** to mobile size
2. **Test search functionality**
3. **Expected Result:**
   - Search box remains usable
   - Results display properly on small screens
   - Buttons don't overlap

### Test 6.2: Visual Feedback
1. **During server search**, watch for:
   - ✓ Loading spinner/indicator
   - ✓ Button disabled state
   - ✓ Status messages

### Test 6.3: Card Layout
1. **After search results load**, verify:
   - ✓ Cards display in grid (3 columns on desktop)
   - ✓ Course title, code, instructor visible
   - ✓ "View Course" and "Enroll" buttons work

---

## 🔧 Test 7: Enrollment Integration

### Test 7.1: Enroll from Search Results
1. **Login as student**
2. **Search for:** `"AI"`
3. **Click "Enroll" button** on a course
4. **Expected Result:**
   - AJAX enrollment request
   - Success notification
   - Button updates to "Enrolled"

### Test 7.2: Already Enrolled Course
1. **Search for course you're already enrolled in**
2. **Expected Result:**
   - Button shows "Enrolled" (disabled)
   - Can't enroll again

---

## 📊 Test 8: Performance Testing

### Test 8.1: Client-Side Speed
1. **Type rapidly:** `"abcdef"` (one char at a time)
2. **Expected Result:**
   - Filtering happens instantly for each keystroke
   - No lag or delay
   - Smooth user experience

### Test 8.2: Server-Side Response Time
1. **Submit server search**
2. **Observe network tab** (F12 → Network)
3. **Expected Result:**
   - Response time < 500ms
   - Status 200 (success)
   - JSON data returned

---

## 🐛 Test 9: Error Handling

### Test 9.1: Network Error Simulation
1. **Disconnect internet** (if testing remotely)
2. **Try server-side search**
3. **Expected Result:**
   - Error message displays
   - Doesn't break the page
   - User can retry

### Test 9.2: SQL Injection Prevention
1. **Search for:** `'; DROP TABLE courses; --`
2. **Expected Result:**
   - No courses found (safe handling)
   - No database errors
   - Application still works

---

## 📝 Test 10: Cross-Browser Testing

Test the search functionality in multiple browsers:
- ✅ Chrome
- ✅ Firefox  
- ✅ Edge
- ✅ Safari (if available)

**Verify:**
- Client-side search works in all browsers
- Server-side AJAX works in all browsers
- No console errors in any browser

---

## 🎯 Expected Search Results Cheat Sheet

| Search Term | Expected Courses | Count |
|-------------|-----------------|-------|
| `"CS"` | All CS courses | ~10 |
| `"IT"` | All IT courses | ~10 |
| `"Web"` | Web-related courses | 3 |
| `"Data"` | Data Structures, Database | 2 |
| `"1st Year"` | First year courses | ~4 |
| `"3rd Year"` | Third year courses | ~6 |
| `"Programming"` | Programming courses | 3+ |
| `"AI"` | Artificial Intelligence | 1 |
| `"Mobile"` | Mobile App Development | 1 |
| `"Security"` | Cybersecurity | 1 |
| `"Cloud"` | Cloud Computing | 1 |
| `"Network"` | Network courses | 2 |

---

## ✅ Success Criteria Checklist

- [ ] Client-side search filters instantly (no delay)
- [ ] Server-side search works via AJAX (no page reload)
- [ ] Empty search shows appropriate message
- [ ] Successful search shows results counter
- [ ] No results shows "no courses found" message
- [ ] Clear button resets search
- [ ] Course cards display correctly
- [ ] Enrollment buttons work from search results
- [ ] Responsive design works on mobile
- [ ] No JavaScript errors in console
- [ ] Both search methods work independently

---

## 🚀 Quick Test Commands

**Open in Browser:**
```
Student Dashboard: http://localhost/ITE311-ESLERA/public/dashboard
Search Page: http://localhost/ITE311-ESLERA/public/courses/search
```

**Check Database:**
```powershell
php spark db:table courses
```

**View Search Routes:**
```powershell
php spark routes | Select-String "search"
```

---

## 💡 Tips for Testing

1. **Open Browser Console** (F12) to see AJAX requests
2. **Network Tab** shows server communication
3. **Test as different roles** (student, teacher, admin)
4. **Try edge cases** (empty, very long searches)
5. **Check mobile responsiveness**
6. **Verify page doesn't reload** during searches

---

## 🎉 Testing Complete!

If all tests pass, your Lab 9 implementation is **production-ready**!

**Report any issues found during testing so they can be fixed.**
