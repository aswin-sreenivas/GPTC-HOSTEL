# 🏠 HostelHub Pro – Hostel Management System

A comprehensive web-based Hostel Management System developed using PHP and MySQL. The platform digitizes hostel administration by managing student accommodation, room allocation, attendance, fees, complaints, visitor records, leave requests, mess management, and parent access through dedicated dashboards.

---

## 📖 Overview

HostelHub Pro is designed to simplify hostel operations for educational institutions. The system provides role-based access for Administrators, Hostel Heads, Students, and Parents, ensuring efficient management of hostel facilities and student records.

---

## ✨ Features

### 👨‍🎓 Student Features
- Student Login & Dashboard
- Profile Management
- Attendance Tracking
- Fee Status Monitoring
- Leave Request Submission
- Complaint Registration
- Mess Menu Viewing
- Food Rating & Feedback

### 👨‍👩‍👦 Parent Features
- Parent Login
- Student Attendance Monitoring
- Fee Status Tracking
- Complaint Monitoring
- Student Profile Access

### 👨‍💼 Hostel Head Features
- Student Management
- Attendance Monitoring
- Complaint Management
- Room Management
- Fee Tracking
- Hostel Dashboard

### 🔐 Admin Features
- Admin Dashboard
- Student Management
- Room Allocation
- Room Management
- Fee Management
- Attendance Management
- Complaint Resolution
- Visitor Management
- Leave Approval System
- Mess Menu Management
- Food Feedback Monitoring
- Payment Verification
- Block Management

---

## 🛠️ Technologies Used

| Technology | Purpose |
|------------|----------|
| HTML5 | Frontend Structure |
| CSS3 | Styling |
| JavaScript | Client-Side Functionality |
| PHP | Backend Development |
| MySQL | Database Management |
| Apache | Web Server (XAMPP/WAMP) |

---

## 📂 Project Structure

```text
HostelHub-Pro/
│
├── admin/
│   ├── admin_dashboard.php
│   ├── students.php
│   ├── rooms.php
│   ├── attendance.php
│   ├── complaints.php
│   ├── fees.php
│   ├── leave_management.php
│   ├── visitors.php
│   ├── mess_menu.php
│   └── payment_verifications.php
│
├── student/
│   ├── student_dashboard.php
│   ├── profile.php
│   ├── attendance.php
│   ├── fees.php
│   ├── complaints.php
│   ├── leave.php
│   ├── mess_menu.php
│   └── rate_food.php
│
├── parent/
│   ├── parent_dashboard.php
│   ├── profile.php
│   ├── attendance.php
│   ├── fees.php
│   └── complaints.php
│
├── head/
│   ├── dashboard.php
│   ├── students.php
│   ├── attendance.php
│   ├── complaints.php
│   ├── fees.php
│   ├── rooms.php
│   └── room_details.php
│
├── auth/
│   ├── login.php
│   └── logout.php
│
├── config/
│   └── db.php
│
├── uploads/
├── assets/
├── index.php
│
└── hostelhub_pro.sql
```

---

## ⚙️ Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/hostelhub-pro.git
```

### Step 2: Move Project Folder

For XAMPP:

```text
C:\xampp\htdocs\
```

For WAMP:

```text
C:\wamp64\www\
```

### Step 3: Create Database

```sql
CREATE DATABASE hostelhub_pro;
```

### Step 4: Import Database

Import:

```text
hostelhub_pro.sql
```

### Step 5: Configure Database Connection

Open:

```php
config/db.php
```

Update database credentials if necessary:

```php
<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "hostelhub_pro";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### Step 6: Run Application

Open browser:

```text
http://localhost/GPTC HOSTEL/
```

---

## 🔑 System Modules

### Student Management
- Student Registration
- Profile Management
- Room Allocation
- Attendance Tracking

### Hostel Management
- Room Allocation
- Room Availability Monitoring
- Hostel Block Management
- Occupancy Tracking

### Fee Management
- Fee Collection
- Payment Verification
- Fee Status Monitoring
- Fee Reports

### Leave Management
- Leave Applications
- Approval Workflow
- Leave Tracking

### Complaint Management
- Student Complaints
- Complaint Resolution
- Status Updates

### Visitor Management
- Visitor Entry Records
- Visitor Checkout Tracking
- Visitor Monitoring

### Mess Management
- Daily Mess Menu
- Food Feedback
- Student Ratings

### Parent Portal
- Attendance Tracking
- Fee Monitoring
- Complaint Tracking

---

## 💾 Database

Database Name:

```text
hostelhub_pro
```

Possible Main Tables:

```sql
students
parents
admins
hostel_heads
rooms
attendance
fees
payments
leave_requests
complaints
visitors
mess_menu
food_feedback
```

---

## 🚀 Future Enhancements

- QR-Based Attendance System
- Mobile Application
- SMS Notifications
- Email Alerts
- Hostel Analytics Dashboard
- Biometric Integration
- Online Fee Payment Gateway
- Room Availability Reports
- Hostel ID Card Generation

---

## 🎓 Academic Purpose

This project was developed as part of academic learning and demonstrates:

- PHP Web Development
- MySQL Database Integration
- Role-Based Access Control
- Authentication & Authorization
- CRUD Operations
- Hostel Management Automation
- Multi-User Web Applications

---

## 👨‍💻 Developer

### Aswin Sreenivas

Diploma in Computer Engineering

#### Connect

GitHub:
https://github.com/yourusername

Portfolio:
https://yourportfolio.com

---

## 📜 License

This project is developed for educational and learning purposes.

---

⭐ If you found this project useful, consider giving it a star on GitHub.
