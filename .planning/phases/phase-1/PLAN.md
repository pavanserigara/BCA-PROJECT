# Phase 1: Optimization & Attendance

## Objective
Stabilize the existing foundation, implement robust Attendance Management, and harden system security with CSRF protection.

---

## 1. Security Hardening (CSRF Protection)
### 1.1 Core CSRF Logic
- Implement `generate_csrf_token()` and `verify_csrf_token()` in `includes/functions.php`.
- Store tokens in the PHP session.

### 1.2 Form Integration
- Add CSRF hidden inputs to all POST forms in:
    - `login.php`
    - `admin/` modules (Settings, Users, etc.)
    - `teacher/` modules (Attendance, Marks, Assignments)
    - `student/` modules (Profile, Submissions)

---

## 2. Database Optimization
### 2.1 Schema Fixes
- Add a unique index to the `attendance` table on `(student_id, subject_id, date)` to prevent duplicate entries.
- Audit other tables for missing constraints (e.g., `marks`).

---

## 3. Attendance Management (Complete & Optimize)
### 3.1 Teacher: Attendance Taking
- Refine `teacher/attendance-take.php` with:
    - CSRF protection.
    - Improved UI (better radio button groups, sticky headers).
    - AJAX-based auto-save (optional but preferred for UX).
    - Validation to ensure attendance isn't marked for future dates.

### 3.2 Teacher: Attendance Reports
- Optimize `teacher/attendance-report.php`:
    - Performance audit of SQL queries.
    - Add "Low Attendance" highlighting (red for < 75%).
    - Export to CSV/PDF functionality.

### 3.3 Student: Attendance View
- Refine `student/attendance.php`:
    - Detailed subject-wise breakdown.
    - Visual progress bars for attendance percentage.
    - "Days needed to reach 75%" calculator.

---

## 4. General Optimization
### 4.1 UI Consistency
- Ensure all dashboards use the latest Tailwind classes and components from `teacher/includes/header.php`.
- Standardize "No Data Found" states across all modules.

### 4.2 Code Quality
- Centralize common SQL queries into `includes/functions.php` where appropriate.
- Ensure all forms use consistent error/success handling via `set_flash_message()`.

---

## Verification Plan

### Automated Tests (Manual Check for now)
- [ ] Verify CSRF token rejection on form submission without token.
- [ ] Attempt to mark duplicate attendance for the same day/student (should fail or update).

### Manual UAT
- [ ] **Teacher**: Mark attendance for a class → Verify success message → Check report.
- [ ] **Student**: View attendance → Verify percentage matches teacher's entry.
- [ ] **Mobile**: Test attendance taking UI on a small screen (responsiveness).
