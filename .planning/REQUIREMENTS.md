# REQUIREMENTS.md

## High-Level Vision
Create a production-ready, unified College ERP (VidyaSetu) that digitizes institution-wide operations with a focus on usability, security, and mobile responsiveness.

---

## 1. Academic Management
### 1.1 Attendance Management
- Teachers can take daily/hourly attendance for their subjects.
- Students can view their attendance percentage and history.
- Admins/HODs can generate low attendance reports.

### 1.2 Timetable Scheduling
- Dynamic timetable generation for classes and teachers.
- Conflict detection (teacher/classroom overlap).
- Viewable by Students and Teachers on their respective dashboards.

### 1.3 Assignment Submission
- Teachers can create assignments with deadlines and attachments.
- Students can upload files (PDF/Images) as submissions.
- Teachers can grade and provide feedback online.

### 1.4 Internal Marks Management
- Entry of internal marks (Sessional, Class Tests, Labs).
- Automatic calculation of averages and weighted scores.
- Integration with the Reports module.

---

## 2. Administrative Operations
### 2.1 Fees Management
- Categorization of fees (Tuition, Hostel, Transport, Library).
- Recording manual payments and generating receipts.
- Defaulter tracking and notification.

### 2.2 Leave Management
- Staff/Teachers can apply for leave online.
- HODs/Admins can approve/reject with comments.
- Leave balance tracking.

### 2.3 ID Card Generation
- Automated generation of ID cards based on student/staff profiles.
- Printable PDF format.

### 2.4 Role-Based Access Control (RBAC)
- Fine-grained permissions for Super Admin, HOD, Teacher, Student, etc.
- Menu items and actions restricted based on role.

---

## 3. Communication & Services
### 3.1 Notifications & Announcements
- Global notice board for college-wide announcements.
- Targeted notifications (e.g., to a specific class or department).

### 3.2 Chat/Messaging System
- Internal messaging between Teachers and Students/Admins.
- Real-time or near real-time (AJAX polling) interaction.

### 3.3 Hostel & Transport Management
- Room allocation and hostel attendance.
- Bus route management and student assignment.

---

## 4. Insights & Reporting
### 4.1 Reports & Analytics Dashboard
- Visual representation of student performance, attendance, and fee collection.
- Exportable reports (CSV/PDF) for management.

---

## 5. Technical Requirements (Non-Functional)
- **Security**: SQL Injection prevention (PDO), XSS prevention (Sanitization), CSRF protection (Tokens to be added).
- **Performance**: Optimized SQL queries and AJAX for smooth UX.
- **Responsiveness**: Fully mobile-responsive UI using Tailwind CSS.
- **Maintainability**: Clean, modular code following the existing structure.
