# Phase 2: Scheduling & Communication

## Objective
Implement dynamic timetable scheduling with conflict detection, enhance the notice board with targeted announcements and file attachments, and build the foundation for leave management.

---

## 1. Academic Scheduling (Timetable)
### 1.1 Security & Conflict Detection
- Add CSRF protection to `admin/timetable.php`.
- **Conflict Detection Engine**:
    - Before inserting a new slot, check if:
        - The teacher is already occupied at that time/day.
        - The room is already occupied at that time/day.
        - The class/semester is already occupied at that time/day.
- Add "Delete Slot" functionality with confirmation.

### 1.2 Teacher & Student Views
- **Teacher View (`teacher/timetable.php`)**:
    - A clean, daily schedule view for the logged-in faculty member.
    - Highlight the current/next lecture.
- **Student View (`student/timetable.php`)**:
    - A daily schedule view based on the student's course and semester.
    - Mobile-responsive card layout.

---

## 2. Institutional Communication (Notice Board)
### 2.1 File Attachments
- Update `notices` table to include an `attachment` column (file path).
- Allow Admin to upload PDFs/Images when posting a notice.
- Implement file download/view on Teacher and Student dashboards.

### 2.2 Targeted Announcements
- Expand `role_target` to include `department_id` or `course_id` targeting.
- Allow HODs/Admins to send notices to specific departments.

---

## 3. Leave Management (New Module)
### 3.1 Database Schema
- Create `leave_requests` table:
    - `id`, `user_id`, `type` (Casual, Sick, etc.), `start_date`, `end_date`, `reason`, `status` (Pending, Approved, Rejected), `approved_by`, `comments`.

### 3.2 Staff/Teacher: Apply for Leave
- Create `teacher/leave-apply.php`:
    - Form to request leave with date range and reason.
    - View history of leave requests.

### 3.3 Admin/HOD: Approval Workflow
- Create `admin/leave-approvals.php`:
    - List of pending requests.
    - Interface to Approve/Reject with comments.

---

## Verification Plan

### Timetable
- [ ] Try to schedule two subjects for the same teacher at the same time (should fail).
- [ ] Try to schedule two subjects for the same room at the same time (should fail).
- [ ] Verify teacher can see their own schedule in their dashboard.

### Notices
- [ ] Post a notice with a PDF attachment → Verify student can download it.
- [ ] Post a notice targeted at a specific department → Verify only students in that department see it.

### Leave Management
- [ ] Submit a leave request → Verify it appears in Admin's approval list.
- [ ] Approve a request → Verify status updates for the requester.
