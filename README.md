# 🎓 CTH Academy — Online Learning Platform

> A production-ready educational management system with AI-powered chat, interactive quizzes, course discussions, and admin analytics. Built from scratch with PHP, vanilla JavaScript, and free Google Gemini API.

![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-orange)
![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen)
![AI](https://img.shields.io/badge/AI-Gemini%202.5%20Flash-red)

---

## ✨ Overview

**CTH Academy** is a comprehensive online learning platform designed for educational institutions. It combines modern web technologies with AI-powered features to create an engaging learning experience for students, teachers, and administrators.

Built with **PHP 8.0+**, **vanilla JavaScript**, **MariaDB**, and **Google's free Gemini API**, this platform demonstrates enterprise-level web development practices including security, performance optimization, and responsive design.

---

## 🎯 Key Features

### 🎓 **For Students**
- 📚 Browse and enroll in courses across multiple categories
- ✅ Take quizzes with real-time scoring (one attempt per quiz)
- 🏆 Compete on the leaderboard with other students
- 🤖 AI chat assistant for personalized learning help
- 💬 Ask questions in course discussions
- 📜 Earn certificates for high performance (75%+ score)
- 👤 Manage profile with picture upload
- 🌙 Dark mode for comfortable studying

### 👨‍🏫 **For Teachers**
- 📝 Create courses with rich content
- ✅ Build quizzes with multiple-choice questions
- 👥 Monitor student progress and performance
- 💬 Answer student questions in discussions
- 📊 View class analytics
- 🔔 Receive notifications for student questions
- 📋 Manage course materials and resources

### 🛡️ **For Admins**
- 📊 Comprehensive dashboard with platform statistics
- 👥 User management (approve/delete accounts)
- 📈 Real-time analytics with charts
- 🎯 Monitor platform activity logs
- 📚 Oversee all courses and quizzes
- 🔧 System configuration and maintenance

---

## 🛠️ Tech Stack

### **Backend**
- **Language**: PHP 8.0+
- **Database**: MySQL/MariaDB with PDO
- **API Pattern**: RESTful JSON APIs
- **Authentication**: Session-based with role-based access control
- **AI Integration**: Google Gemini 2.5 Flash API (free)

### **Frontend**
- **HTML5** — Semantic markup with accessibility
- **CSS3** — Custom animations, gradients, responsive design
- **Vanilla JavaScript** — No frameworks, pure ES6+
- **Dark Mode** — CSS variables for theme switching
- **Charts**: Chart.js for analytics visualization

### **Infrastructure**
- **Web Server**: Nginx or Apache with PHP-FPM
- **Server OS**: Ubuntu 20.04+
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Version Control**: Git

---

## 🏗️ Architecture

```
cth-academy/
├── config/
│   ├── db.php              # Database connection
│   └── log_functions.php   # Logging utilities
├── public/
│   ├── index.html          # Landing page
│   ├── login.html          # Login page
│   ├── signup.html         # Registration
│   ├── uploads/            # User uploads (profiles, files)
│   ├── css/
│   │   ├── style_professional.css  # Main styles
│   │   └── darkmode.css    # Dark mode styles
│   └── js/
│       ├── auth.js         # Authentication logic
│       ├── darkmode.js     # Dark mode toggle
│       └── contact.js      # Contact form
├── backend/
│   ├── admin/
│   │   ├── admin_dashboard.php     # Admin panel
│   │   ├── admin_login.php         # Admin auth
│   │   ├── get_dashboard_data.php  # Analytics
│   │   ├── get_users.php           # User listing
│   │   ├── update_user_status.php  # User management
│   │   └── approve_user.php        # User approval
│   ├── quiz/
│   │   ├── create_quiz.php         # Quiz creation
│   │   ├── create_quiz_submit.php  # Save quiz & questions
│   │   ├── get_quiz_questions.php  # Load quiz questions
│   │   ├── get_student_quizzes.php # Student quiz list
│   │   ├── submit_quiz.php         # Submit answers
│   │   ├── check_attempt.php       # One-attempt check
│   │   ├── add_question.php        # Add single question
│   │   └── get_leaderboard.php     # Leaderboard rankings
│   ├── profile/
│   │   ├── get_profile.php         # User profile data
│   │   ├── update_profile.php      # Update bio/location
│   │   ├── upload_profile_picture.php  # Profile photo
│   │   └── view_certificate.php    # Certificate display
│   ├── ai/
│   │   ├── ai_chat.php             # AI chat with context
│   │   └── get_chat_history.php    # Chat history
│   └── discussions/
│       ├── get_discussions.php     # Course discussions
│       ├── get_replies.php         # Discussion replies
│       ├── post_question.php       # Post new question
│       └── post_reply.php          # Post reply
├── views/
│   ├── enseignant.html    # Teacher dashboard
│   ├── etudiant.html      # Student dashboard
│   ├── admin_dashboard.php # Admin panel
│   ├── profile.html       # Profile page
│   ├── leaderboard.html   # Leaderboard
│   ├── create_quiz.html   # Quiz creator
│   └── discussion_ui.html # Discussion forum
└── database/
    └── schema.sql        # Database structure
```

---

## 📋 Database Schema

### Core Tables

| Table | Purpose |
|-------|---------|
| **users** | Students, teachers, admins with auth data |
| **courses** | Course content with PDF support |
| **quizzes** | Quiz metadata linked to courses |
| **questions** | Multiple-choice questions in quizzes |
| **student_quizzes** | Student quiz attempts and scores |
| **course_discussions** | Q&A forum for each course |
| **discussion_replies** | Replies to discussion questions |
| **ai_chat_history** | Chat history with AI assistant |
| **notifications** | User notifications and alerts |
| **user_achievements** | Badges and achievements |
| **activity_logs** | Platform activity tracking |

---

## 🚀 Getting Started

### **Prerequisites**
```
PHP 8.0+
MySQL 5.7+ or MariaDB 10.3+
Nginx or Apache with PHP-FPM
Composer (optional)
curl (for API calls)
```

### **Installation**

1. **Clone repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/cth-academy.git
   cd cth-academy
   ```

2. **Create database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configure database connection**
   ```bash
   # Edit config/db.php with your credentials
   nano config/db.php
   ```
   ```php
   $host = 'localhost';
   $db = 'cth_academy';
   $user = 'php_user';
   $pass = 'secure_password';
   ```

4. **Create uploads directory**
   ```bash
   mkdir -p public/uploads/profiles
   mkdir -p public/uploads/certificates
   chmod -R 755 public/uploads
   chown -R www-data:www-data public/uploads
   ```

5. **Configure Gemini API** (Free)
   - Get free API key from [Google AI Studio](https://aistudio.google.com/app/apikey)
   - Add to `backend/ai/ai_chat.php`:
   ```php
   $api_key = 'YOUR_GEMINI_API_KEY_HERE';
   ```

6. **Setup Nginx** (if using Nginx)
   ```nginx
   server {
       listen 80;
       server_name cth-academy.local;
       root /var/www/cth-academy/public;
       
       location / {
           try_files $uri $uri/ /index.html;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/run/php/php8.0-fpm.sock;
           fastcgi_index index.php;
           include fastcgi_params;
       }
   }
   ```

7. **Access the platform**
   ```
   http://localhost
   ```

---

## 📖 User Guide

### **Student Workflow**

1. **Registration & Login**
   ```html
   Email: student@example.com
   Password: SecurePass123
   Role: Étudiant
   ```

2. **Browse & Enroll in Courses**
   - Dashboard → Select course category
   - View course details and syllabus
   - Take associated quizzes

3. **Take Quiz (One Attempt)**
   ```
   Select quiz → Answer all questions → Submit
   Score calculated immediately → View results
   (Cannot retake - enforced at database level)
   ```

4. **Ask Questions in Discussions**
   - Go to course discussion forum
   - Click "Ask a Question"
   - Get responses from teacher/peers

5. **Use AI Chat Assistant**
   - Click "Get Help" in any course
   - Ask for hints, explanations, or practice questions
   - AI provides personalized responses based on:
     - Your profile and statistics
     - Course content and PDFs
     - Previous chat history

6. **Earn Certificate**
   ```
   Quiz Score >= 75% → Certificate awarded
   Click "View Certificate" → Beautiful PDF
   Print or download for portfolio
   ```

7. **Check Leaderboard**
   - See ranking by total quiz score
   - Average percentage calculation
   - Real-time updates every 30 seconds

### **Teacher Workflow**

1. **Login as Enseignant**
   ```
   Role: Enseignant (Teacher)
   ```

2. **Create Course**
   - Dashboard → Add Course
   - Set title, category, description
   - (Optional) Upload PDF materials

3. **Create Quiz**
   ```
   Go to: Create Quiz Page
   - Select course
   - Enter quiz title
   - Add questions (click "+ Add Question")
   - Fill: Question text + 4 options + correct answer
   - Submit
   ```

4. **Monitor Students**
   - View enrolled students
   - Check quiz scores
   - See discussion questions

5. **Answer Questions**
   - Go to course discussion
   - Reply to student questions
   - Mark answer as "Solved"
   - Send notifications to students

### **Admin Workflow**

1. **Login as Admin**
   - Special admin login page
   - Default: admin@cth-academy.com

2. **Dashboard Overview**
   ```
   View:
   - Total students/teachers
   - Course count
   - Quiz completion rate
   - Recent user registrations
   ```

3. **User Management**
   - Approve pending student accounts
   - Delete inactive users
   - View activity logs

4. **Analytics**
   - Chart.js visualization of completion rates
   - Student progression tracking
   - Quiz performance metrics

---

## 🔐 Authentication & Security

### **Role-Based Access Control (RBAC)**

| Feature | Student | Teacher | Admin |
|---------|:-------:|:-------:|:-----:|
| Browse Courses | ✅ | ✅ | ✅ |
| Create Courses | ❌ | ✅ | ✅ |
| Take Quizzes | ✅ | ✅ | ✅ |
| Create Quizzes | ❌ | ✅ | ✅ |
| Admin Panel | ❌ | ❌ | ✅ |
| Manage Users | ❌ | ❌ | ✅ |
| View Leaderboard | ✅ | ✅ | ✅ |
| Chat with AI | ✅ | ✅ | ✅ |

### **Security Measures**
- ✅ Password hashing (bcrypt)
- ✅ Session-based authentication
- ✅ SQL injection prevention (prepared statements)
- ✅ CSRF protection via POST validation
- ✅ XSS prevention (htmlspecialchars)
- ✅ File upload validation (MIME type checking)
- ✅ One-attempt quiz enforcement at database level
- ✅ Role-based endpoint validation

---

## 🛣️ API Endpoints

### **Authentication**
```
GET  /              → Home page
GET  /login.html    → Login form
POST /backend/auth/login.php      → Process login
GET  /signup.html   → Registration form
POST /backend/auth/signup.php     → Create account
GET  /backend/auth/logout.php     → Logout
```

### **Quiz Management**
```
GET  /backend/quiz/get_student_quizzes.php          → List all quizzes
GET  /backend/quiz/get_quiz_questions.php?quiz_id=X → Get questions
GET  /backend/quiz/check_attempt.php?quiz_id=X      → Check if attempted
POST /backend/quiz/submit_quiz.php                  → Submit quiz answers
GET  /backend/quiz/get_leaderboard.php              → Top students ranking
```

### **Course Discussions**
```
GET  /backend/discussions/get_discussions.php?course_id=X → All questions
GET  /backend/discussions/get_replies.php?discussion_id=X → Replies
POST /backend/discussions/post_question.php               → Ask question
POST /backend/discussions/post_reply.php                  → Post reply
```

### **AI Chat**
```
POST /backend/ai/ai_chat.php                   → Send message to AI
GET  /backend/ai/get_chat_history.php?course_id=X → Chat history
```

### **Profile**
```
GET  /backend/profile/get_profile.php          → User profile
POST /backend/profile/update_profile.php       → Update bio/info
POST /backend/profile/upload_profile_picture.php → Profile photo
GET  /backend/profile/view_certificate.php?quiz_id=X → View certificate
```

### **Admin**
```
GET  /backend/admin/admin_dashboard.php        → Admin panel
GET  /backend/admin/get_dashboard_data.php     → Analytics data
GET  /backend/admin/get_users.php              → User list
POST /backend/admin/update_user_status.php     → Approve/Delete users
```

### **Leaderboard**
```
GET  /backend/quiz/get_leaderboard.php         → Top 10 students
```

---

## 🤖 AI Chat Assistant

### **Features**
- **Context-Aware**: Uses student profile, course content, stats
- **PDF Integration**: Extracts and references course PDFs
- **Personalized**: Knows student name, progress, history
- **Multiple Modes**:
  - 💡 **Hint Mode** — Guides without spoiling answers
  - 📚 **Explanation Mode** — Clear concept explanations
  - 🎯 **Practice Mode** — Generates practice questions
  - ❓ **Question Mode** — General Q&A

### **Example: AI Context**
When a student asks a question about "JavaScript basics" in the Web Development course:

```
AI receives:
- Student profile (name, role, email, bio)
- Course materials (title, category, PDF content)
- Student statistics (quizzes taken, average score, certificates)
- Quiz topics from the course
- Previous conversation history
- Message type (hint, explanation, practice, question)

AI responses are:
- Personalized to student's level
- Grounded in course content
- Encouraging and helpful
- Never revealing quiz answers
```

### **Free API**
- Uses Google Gemini 2.5 Flash (FREE tier)
- No subscription required
- ~1000 requests/minute limit
- Perfect for educational use

---

## 📊 Quiz System

### **Features**
- ✅ Multiple-choice questions
- ✅ One-attempt enforcement
- ✅ Instant scoring (X/Total)
- ✅ Percentage calculation
- ✅ Score saved to leaderboard
- ✅ Certificates for 75%+ scores

### **How One-Attempt Works**
```php
// When student submits quiz:
1. Check if student_id + quiz_id exists in student_quizzes
2. If yes → Return error "Quiz already attempted"
3. If no → Save score and timestamp
4. Calculate results immediately
5. Display certificate option if eligible
```

### **Scoring Formula**
```
Score = (Correct Answers / Total Questions)
Percentage = (Score * 100)
Certificate Eligible = (Percentage >= 75%)
Leaderboard Rank = Sorted by Total Score DESC
```

---

## 💬 Discussion Forum

### **Workflow**
1. Student posts question in course discussion
2. Question appears in forum immediately
3. Teacher or peers can reply
4. Teacher marks answer as "Solved"
5. Student gets notification
6. Helpful count tracks useful replies

### **Features**
- 🎯 Teacher responses highlighted
- ✅ Mark answer as solved
- 👍 Helpful voting system
- 🔔 Notifications for new replies
- ⭐ Teacher badge on responses

---

## 📜 Certificate System

### **Eligibility**
```
Score >= 75% (of total questions)
Example: 15/20 questions = 75% ✅ Eligible
         14/20 questions = 70% ❌ Not eligible
```

### **Features**
- 🎓 Professional certificate design
- 📊 Shows score and percentage
- 🏆 Performance tier badge
  - 90%+ = Outstanding Performance 🏆
  - 80%+ = Excellent Work ⭐
  - 75%+ = Great Job ✨
- 🖨️ Print-ready format
- 📥 Download as PDF

---

## 📈 Analytics & Leaderboard

### **Leaderboard Metrics**
```
Rank  Student Name  Quizzes Taken  Total Score  Average %
1️⃣   Ahmed Ali          15           1350        90%
2️⃣   Fatima Hassan      14           1260        90%
3️⃣   Youssef Mohamed    12           1080        90%
...
```

### **Admin Dashboard**
- Total students/teachers/courses
- Quiz completion rate (%)
- Completion status chart (Completed vs Pending)
- Recent user registrations
- Activity logs with timestamps

---

## 🎨 Customization

### **Dark Mode**
Edit `public/css/darkmode.css`:
```css
:root {
    --bg-primary: #1a1a2e;
    --bg-secondary: #16213e;
    --text-primary: #ffffff;
    --accent: #4b7bec;
}
```

### **Color Scheme**
Edit `public/css/style_professional.css`:
```css
/* Main brand color */
--primary: #4b7bec;

/* Accent colors */
--success: #28a745;
--danger: #dc3545;
--warning: #ffc107;
--info: #17a2b8;
```

### **Add New Course Category**
```php
// Edit backend/quiz/create_quiz.php
$categories = ['Web Development', 'Programming', 'AI', 'NEW_CATEGORY'];
```

### **Modify Quiz Scoring**
```php
// Edit backend/quiz/submit_quiz.php
$passing_score = 0.75; // Change to different threshold
```

---

## 🔧 Deployment

### **Development**
```bash
cd cth-academy
php -S localhost:8000
# Access: http://localhost:8000
```

### **Production (Ubuntu)**
```bash
# Install dependencies
sudo apt-get update
sudo apt-get install nginx php8.0-fpm mysql-server php8.0-mysql

# Configure Nginx
sudo nano /etc/nginx/sites-available/cth-academy
# (Use config from above)

# Start services
sudo systemctl start nginx
sudo systemctl start php8.0-fpm
sudo systemctl start mysql

# Enable on boot
sudo systemctl enable nginx php8.0-fpm mysql
```

### **Environment Variables** (Optional)
Create `.env` file:
```
DB_HOST=localhost
DB_NAME=cth_academy
DB_USER=php_user
DB_PASS=secure_password
GEMINI_API_KEY=your_api_key_here
```

---

## 🧪 Testing

### **Test Accounts**

**Admin**
```
Email: admin@cth-academy.com
Password: admin@123
```

**Teacher**
```
Email: teacher@cth-academy.com
Password: teacher123
Role: Enseignant
```

**Student**
```
Email: student@cth-academy.com
Password: student123
Role: Étudiant
```

### **Testing Scenarios**

1. **Quiz One-Attempt Test**
   - Student takes quiz and submits
   - Try to retake → Should see "Quiz already attempted"

2. **Certificate Test**
   - Complete quiz with 75%+ score
   - Navigate to profile → Certificates
   - Click "View Certificate" → Beautiful PDF

3. **AI Chat Test**
   - Go to course → Click "Get Help"
   - Ask a question → AI responds with course context
   - Check if it mentions course title/topics

4. **Admin Approval**
   - Create student account
   - Check admin dashboard
   - Account should be "Pending"
   - Click Approve → Student can login

5. **Discussion Forum**
   - Student posts question
   - Teacher logs in → Replies
   - Check notification

---

## 📊 Database Queries

### **Get Top Students**
```sql
SELECT 
    u.nom, u.prenom,
    COUNT(DISTINCT sq.quiz_id) as quizzes_taken,
    SUM(sq.score) as total_score,
    ROUND(AVG(sq.score * 100.0 / (
        SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id
    )), 1) as avg_percentage
FROM users u
INNER JOIN student_quizzes sq ON u.id = sq.student_id
WHERE u.role = 'etudiant'
GROUP BY u.id
ORDER BY total_score DESC
LIMIT 10;
```

### **Get Student Progress**
```sql
SELECT 
    c.title as course,
    q.title as quiz,
    sq.score, 
    COUNT(*) as total_questions,
    (sq.score * 100.0 / COUNT(*)) as percentage
FROM student_quizzes sq
JOIN quizzes q ON sq.quiz_id = q.id
JOIN courses c ON q.course_id = c.id
JOIN questions qu ON q.id = qu.quiz_id
WHERE sq.student_id = ?
GROUP BY sq.quiz_id;
```

---

## 🐛 Known Issues & Troubleshooting

### **Common Issues**

| Issue | Solution |
|-------|----------|
| **"Upload directory not writable"** | `chmod -R 755 public/uploads` |
| **"Database connection failed"** | Check credentials in `config/db.php` |
| **"AI chat returns error"** | Verify Gemini API key and internet connection |
| **"Quiz score not saving"** | Check `student_quizzes` table permissions |
| **"Dark mode not working"** | Clear browser cache and cookies |

### **Enable Error Logging**
```php
// Add to config/db.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## 🚀 Future Enhancements

- 🔐 Two-factor authentication
- 📱 Mobile app (React Native)
- ☁️ Cloud storage integration (AWS S3)
- 📧 Email notifications
- 🎥 Video hosting integration
- 📊 Advanced analytics dashboard
- 🌐 Multi-language support
- ♿ WCAG accessibility improvements
- 🔄 Real-time notifications (WebSockets)
- 📝 Plagiarism detection for assignments

---

## 🤝 Contributing

### **How to Contribute**

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** changes: `git commit -m "Add amazing feature"`
4. **Push** to branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

### **Code Style**
- PHP: PSR-12 standard
- JavaScript: ES6+ with camelCase
- HTML: Semantic tags
- CSS: Mobile-first responsive design
- Database: Normalized schema with foreign keys

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 Fedi Mhadhbi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```

---

## 📞 Support & Contact

### **Creator**
**Fedi Mhadhbi** — Full-Stack Web Developer

### **Get in Touch**
- 🐙 GitHub: [@Fedi-Mhadhbi](https://github.com/Fedi-Mhadhbi)
- 💼 LinkedIn: [Fedi Mhadhbi](https://linkedin.com/in/fedi-mhadhbi)
- 📧 Email: [contact@example.com](mailto:contact@example.com)

### **Report Issues**
Found a bug? [Open an issue on GitHub](https://github.com/Fedi-Mhadhbi/cth-academy/issues)

### **Feature Requests**
Have a great idea? [Create a discussion](https://github.com/Fedi-Mhadhbi/cth-academy/discussions)

---

## 🌟 Acknowledgments

- **Google Gemini API** for free AI integration
- **Chart.js** for beautiful data visualization
- **Inspiration** from modern EdTech platforms
- **Thanks to** all students and educators using the platform

---

## 📈 Project Statistics

| Metric | Value |
|--------|-------|
| **Lines of Code** | 5,000+ |
| **PHP Files** | 20+ |
| **Database Tables** | 11 |
| **API Endpoints** | 25+ |
| **Supported Roles** | 3 (Student, Teacher, Admin) |
| **Course Categories** | 3+ customizable |
| **Development Time** | Several weeks |
| **Status** | ✅ Production Ready |

---

## 🎯 Vision

CTH Academy aims to democratize quality education by providing an **accessible, affordable, and engaging** online learning platform. Our mission is to empower students worldwide to learn at their own pace with support from experienced teachers and AI-powered assistance.

### Core Values
- 🎓 **Accessibility** — Learning for everyone
- 💡 **Quality** — High-standard course content
- 🤝 **Community** — Peer learning and support
- 🚀 **Innovation** — AI-powered personalization
- 🔒 **Security** — Student data protection

---

<div align="center">

## ⭐ If you found this project helpful, please give it a star!

[⭐ Star on GitHub](https://github.com/Fedi-Mhadhbi/cth-academy) • [📋 Report Issue](https://github.com/Fedi-Mhadhbi/cth-academy/issues) • [💬 Discussions](https://github.com/Fedi-Mhadhbi/cth-academy/discussions)

---

**Made with ❤️ by Fedi Mhadhbi**

"Education is the most powerful weapon you can use to change the world." — Nelson Mandela

</div>

---

## 🔗 Related Projects

- [EduPlatform](https://github.com/Fedi-Mhadhbi/eduplatform) — Arabic education platform for Tunisian students
- [More projects coming soon...](https://github.com/Fedi-Mhadhbi)

---

**Last Updated**: 2024  
**Version**: 1.0.0  
**Status**: Production Ready ✅
