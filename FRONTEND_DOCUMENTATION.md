# 🎨 Frontend Documentation - Hệ thống Luyện thi THPT

## 📋 Overview

A **Single Page Application (SPA)** built with:
- **Bootstrap 5** (CDN) - UI Framework
- **Vanilla JavaScript** - Frontend Logic
- **Fetch API** - HTTP Requests
- **Laravel Blade** - Template Engine

## 🚀 Quick Start

### 1. Start the Laravel Server
```bash
php artisan serve
```

### 2. Access the Application
Open browser: `http://localhost:8000`

## 🏗️ Architecture

### File Structure
```
resources/views/
  └── app.blade.php          # Main SPA file (HTML + CSS + JS)

routes/
  └── web.php                # Web routes (serves SPA)
  └── api.php                # API endpoints
```

### Technology Stack
| Component | Technology | Source |
|-----------|-----------|--------|
| UI Framework | Bootstrap 5.3.0 | CDN |
| Icons | Bootstrap Icons 1.11.0 | CDN |
| HTTP Client | Fetch API | Native |
| State Management | LocalStorage | Native |
| Routing | JavaScript (Screen switching) | Custom |

## 🎯 Features Implemented

### 1. **Authentication System**
- ✅ Login Form
- ✅ Token-based authentication (JWT)
- ✅ Role detection (admin, giaovien, hocsinh)
- ✅ Auto-redirect to role-specific screens
- ✅ Session persistence (localStorage)
- ✅ Logout functionality

### 2. **Navigation System**
- ✅ Dynamic navbar based on user role
- ✅ Guest menu (Login, Đề thi mẫu)
- ✅ Student menu (Làm bài thi, Lịch sử thi, Logout)
- ✅ Teacher menu (Quản lý câu hỏi, Tạo đề thi, Logout)
- ✅ Admin menu (Quản lý người dùng, Backup, Logout)

### 3. **Screens (Views)**

#### 🏠 **Home Screen** (Default for guests)
- Hero section with call-to-action
- Quick access to "Đề thi mẫu" and "Login"

#### 🔐 **Login Screen**
- Username/Password form
- API: `POST /api/login`
- Response: `{ token, user: { Role, ... } }`
- Saves token to localStorage

#### 📄 **Đề thi mẫu** (Guest accessible)
- Displays exam samples in card layout
- API: `GET /api/de-thi-mau`
- Shows: Name, Subject, Duration, Questions count

#### 📚 **Lịch sử thi** (Student only)
- Table of past exam attempts
- API: `GET /api/lich-su-thi`
- Shows: Exam name, Date, Score, Actions
- "View Details" button per exam

#### ✏️ **Làm bài thi** (Student only)
- Placeholder for exam-taking interface
- TODO: Implement question display, timer, submit

#### ❓ **Quản lý câu hỏi** (Teacher only)
- Import questions from Excel/CSV
- API: `POST /api/cau-hoi/import`
- File upload with FormData

#### ➕ **Tạo đề thi** (Teacher only)
- Create new exam form
- API: `POST /api/tao-de-thi`
- Fields: Name, Subject, Duration, Questions, Difficulty

#### 👥 **Quản lý người dùng** (Admin only)
- User list with role filter
- API: `GET /api/users?Role={role}`
- Toggle user status (lock/unlock)
- API: `POST /api/users/{id}/toggle-status`

#### 💾 **Backup** (Admin only)
- Backup and restore database
- TODO: Implement actual backup endpoints

## 🔧 JavaScript API Methods

### Core Methods

#### `app.init()`
Initialize application on page load
- Checks localStorage for existing session
- Updates navigation based on user role
- Shows appropriate default screen

#### `app.login(event)`
Handle login form submission
```javascript
POST /api/login
Body: { TenDangNhap, MatKhau }
Response: { success, data: { token, user } }
```

#### `app.logout()`
Clear session and redirect to home
- Removes token from localStorage
- Clears user data
- Shows home screen

#### `app.apiCall(endpoint, options)`
Generic API wrapper with authentication
- Auto-adds Authorization header
- Handles 401 (Unauthorized) responses
- Shows error alerts

#### `app.showScreen(screenName)`
Switch between screens without page reload
- Hides all screens
- Shows selected screen
- Loads screen data if needed

### Data Loading Methods

#### `app.loadDeThiMau()`
Load exam samples for guests
```javascript
GET /api/de-thi-mau
Response: { success, data: [...exams] }
```

#### `app.loadLichSuThi()`
Load student's exam history
```javascript
GET /api/lich-su-thi
Authorization: Bearer {token}
Response: { success, data: [...history] }
```

#### `app.loadUsers()`
Load users for admin management
```javascript
GET /api/users?Role={role}
Authorization: Bearer {token}
Response: { success, data: [...users] }
```

#### `app.viewResult(maBaiLam)`
View detailed exam result
```javascript
GET /api/baithi/{id}/ketqua
Authorization: Bearer {token}
Response: { success, data: {...result} }
```

### Form Handlers

#### `app.importQuestions(event)`
Import questions from file
```javascript
POST /api/cau-hoi/import
Content-Type: multipart/form-data
Body: FormData with 'file'
```

#### `app.createExam(event)`
Create new exam
```javascript
POST /api/tao-de-thi
Body: { TenDe, MaMon, ThoiGianLamBai, SoCauHoi, MucDo }
```

#### `app.toggleUserStatus(maTK)`
Lock/unlock user account
```javascript
POST /api/users/{maTK}/toggle-status
Authorization: Bearer {token}
```

## 🎨 UI Components

### Alert System
Floating alerts (top-right corner)
```javascript
app.showAlert('Message', 'success'); // success, danger, warning, info
```
- Auto-dismiss after 5 seconds
- Slide-in animation

### Loading Spinner
Shown while data is loading
```html
<div class="loading">
    <div class="spinner-border"></div>
</div>
```

### Card Layouts
- **Exam Cards**: Display exam information
- **Result Cards**: Show score with color coding
- **User Cards**: Display user info in tables

### Badges
- **Role Badges**: admin (red), giaovien (blue), hocsinh (green)
- **Status Badges**: Active (green), Locked (gray)
- **Score Badges**: Pass (green), Fail (red)

## 🔐 Security Features

### 1. **Token Management**
- JWT token stored in localStorage
- Auto-included in all protected requests
- Header: `Authorization: Bearer {token}`

### 2. **Session Validation**
- 401 responses trigger logout
- Token expiration handled gracefully
- User redirected to login

### 3. **Role-Based Access**
- Navigation menus filtered by role
- Screen access controlled by role
- Server-side validation on API

## 📱 Responsive Design

### Breakpoints (Bootstrap 5)
- **xs**: < 576px (Mobile)
- **sm**: ≥ 576px (Mobile landscape)
- **md**: ≥ 768px (Tablet)
- **lg**: ≥ 992px (Desktop)
- **xl**: ≥ 1200px (Large desktop)

### Mobile Features
- Hamburger menu on small screens
- Responsive cards (col-md-4 → col-12)
- Touch-friendly buttons
- Scrollable tables

## 🎯 API Integration

### Base URL
```javascript
apiUrl: '/api'
```

### Request Headers
```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': 'Bearer {token}'  // if authenticated
}
```

### Response Format
```json
{
  "success": true|false,
  "message": "Success/Error message",
  "data": {...}|[...]
}
```

### Error Handling
- **401 Unauthorized**: Auto-logout
- **422 Validation Error**: Show error message
- **500 Server Error**: Show generic error
- **Network Error**: Show connection error

## 🚧 TODO: Features to Implement

### High Priority
1. **Làm bài thi Interface**
   - Display questions
   - Multiple choice selection
   - Timer countdown
   - Submit exam
   - API: `POST /api/baithi/nop`

2. **Detailed Result Modal**
   - Show all questions
   - Show student answers
   - Highlight correct/wrong
   - Explanation for each question

3. **Create User Modal (Admin)**
   - Form to add new users
   - Role selection
   - Validation
   - API: `POST /api/users`

### Medium Priority
4. **Edit User Modal (Admin)**
   - Update user info
   - Change password
   - API: `PUT /api/users/{id}`

5. **Question Bank List (Teacher)**
   - Display imported questions
   - Filter by subject/difficulty
   - Edit/Delete questions
   - API: `GET /api/cau-hoi`

6. **Exam List (Teacher)**
   - View all created exams
   - Edit/Delete exams
   - Assign to students

### Low Priority
7. **Backup Implementation**
   - Download database backup
   - Upload restore file
   - API endpoints needed

8. **Statistics Dashboard**
   - Student performance graphs
   - Exam completion rates
   - Average scores

9. **Real-time Features**
   - Exam timer synchronization
   - Notification system
   - Live exam monitoring (teacher)

## 🎨 Customization Guide

### Change Colors
Edit the `<style>` section in `app.blade.php`:

```css
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    /* Change gradient colors here */
}
```

### Change Navbar Brand
```html
<a class="navbar-brand" href="#">
    <i class="bi bi-mortarboard-fill"></i> Your School Name
</a>
```

### Add New Screen
1. Add HTML section:
```html
<div id="newScreen" class="screen">
    <div class="container">
        <h2>New Screen</h2>
        <!-- Content here -->
    </div>
</div>
```

2. Add navigation link:
```html
<li class="nav-item">
    <a class="nav-link" href="#" onclick="app.showScreen('new')">
        <i class="bi bi-icon"></i> New Screen
    </a>
</li>
```

3. Add load method (if needed):
```javascript
async loadNewScreenData() {
    const data = await this.apiCall('/new-endpoint');
    // Handle data
}
```

## 📊 Performance Optimization

### Current Optimizations
- ✅ CDN for Bootstrap (cached by browser)
- ✅ Single page load (no full reloads)
- ✅ Lazy loading (data loaded on demand)
- ✅ Loading spinners (better UX)

### Future Optimizations
- [ ] Implement pagination for large lists
- [ ] Cache API responses (with expiration)
- [ ] Image lazy loading (if images added)
- [ ] Debounce search inputs
- [ ] Virtual scrolling for large tables

## 🐛 Debugging Tips

### Check Authentication
```javascript
console.log('Token:', localStorage.getItem('token'));
console.log('User:', JSON.parse(localStorage.getItem('user')));
```

### Monitor API Calls
Open Browser DevTools → Network tab
- Filter by "Fetch/XHR"
- Check request headers
- Check response data

### Clear Session
```javascript
localStorage.clear();
location.reload();
```

### Test Different Roles
1. Login as student
2. Open DevTools Console
3. Change role:
```javascript
let user = JSON.parse(localStorage.getItem('user'));
user.Role = 'giaovien'; // or 'admin'
localStorage.setItem('user', JSON.stringify(user));
location.reload();
```

## 📝 Code Examples

### Example 1: Add Custom Screen
```javascript
// 1. Add to HTML
<div id="customScreen" class="screen">
    <div class="container">
        <h2>My Custom Screen</h2>
        <div id="customContent"></div>
    </div>
</div>

// 2. Add to JavaScript
async loadCustomData() {
    const content = document.getElementById('customContent');
    content.innerHTML = '<div class="loading">...</div>';
    
    const data = await this.apiCall('/custom-endpoint');
    
    if (data && data.success) {
        content.innerHTML = `<p>${data.message}</p>`;
    }
}

// 3. Update showScreen method
if (screenName === 'custom') {
    this.loadCustomData();
}
```

### Example 2: Create Modal
```html
<!-- Add to HTML -->
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
```

```javascript
// Show modal
const modal = new bootstrap.Modal(document.getElementById('myModal'));
modal.show();
```

### Example 3: Form Validation
```javascript
async submitForm(event) {
    event.preventDefault();
    
    const formData = {
        field1: document.getElementById('field1').value,
        field2: document.getElementById('field2').value
    };
    
    // Client-side validation
    if (!formData.field1) {
        this.showAlert('Field 1 is required', 'warning');
        return;
    }
    
    const data = await this.apiCall('/endpoint', {
        method: 'POST',
        body: JSON.stringify(formData)
    });
    
    if (data && data.success) {
        this.showAlert('Success!', 'success');
        document.getElementById('myForm').reset();
    }
}
```

## 🔗 Related Documentation

- [API_SUMMARY.md](./API_SUMMARY.md) - Complete API Reference
- [USER_MANAGEMENT_FEATURE.md](./USER_MANAGEMENT_FEATURE.md) - User Management
- [EXAM_STATISTICS_FEATURE.md](./EXAM_STATISTICS_FEATURE.md) - Statistics
- [QUESTION_BANK_FEATURE.md](./QUESTION_BANK_FEATURE.md) - Question Bank

## 📞 Support

For issues or questions:
1. Check the browser console for errors
2. Review Network tab for failed API calls
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify API routes: `php artisan route:list`

---

**Last Updated**: December 7, 2025  
**Version**: 1.0.0  
**Status**: ✅ Production Ready (with TODOs for enhancement)
