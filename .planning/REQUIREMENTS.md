# Requirements - Milestone 2: ERP Feature Completion & System Enhancement

## 1. Profile System Improvements
- **Goal**: Implement a robust profile management system with photo support.
- **Functional Requirements**:
    - Users (all roles) can upload a profile picture.
    - Real-time image preview before saving.
    - Image editing/cropping capability (optional/bonus) or at least simple replacement.
    - Secure storage in `uploads/profiles/` with sanitized filenames.
    - Default avatar fallback (`default_profile.svg`) if no photo exists.
    - Validation: Max size 2MB, formats (JPG, PNG, WEBP).

## 2. Student Document Vault
- **Goal**: digitize student documentation for institutional records.
- **Functional Requirements**:
    - Students can upload: Aadhaar Card, SSLC Marks Card, PUC Marks Card, Transfer Certificate, Passport Size Photo.
    - Support for PDF and Image formats.
    - File size validation (Max 5MB per file).
    - Student Panel: "My Documents" to view and replace files.
    - Admin Panel: "Verify Documents" view to approve/reject student uploads.
    - Secure file handling (unpredictable filenames, restricted access).

## 3. Teacher Attendance 2.0
- **Goal**: Move beyond simple "take attendance" to a management workflow.
- **Functional Requirements**:
    - **Edit Attendance**: Teachers can correct attendance for the current day or previous days (within a threshold, e.g., 3 days).
    - **Monthly Reports**: Subject-wise and class-wise monthly attendance reports.
    - **Export**: PDF and Excel export for attendance records.
    - **Analytics**: Dashboard widget showing attendance trends.

## 4. Missing Module Implementation
- **Goal**: Full implementation of roles that currently lack portals.
- **Portals to Create**:
    - **Library Portal (`library/`)**: Librarian can manage books, issue/return logs, and overdue fines (separate from admin).
    - **Staff/Accountant Portal (`staff/`)**: Staff can manage non-academic complaints or tasks; Accountant manages fee collection.
- **Functional Requirements**:
    - Dashboard for each role.
    - Redirection logic in `login.php` and `functions.php`.
    - Sidebar/Header integration.

## 5. System-Wide Audit & Auto-Completion
- **Goal**: Ensure all modules meet professional ERP standards.
- **Functional Requirements**:
    - **Search/Filter**: Add advanced filtering to all lists (Students, Faculty, Staff, Books).
    - **Pagination**: Implement consistent pagination for all data-heavy tables.
    - **Export**: Export capability for all major lists.
    - **UI/UX**: Fix broken routes and inconsistent Tailwind classes.
    - **Validation**: Ensure all forms have client-side and server-side validation.

## 6. Security & Optimization
- **Goal**: Production-level hardening.
- **Functional Requirements**:
    - Verify all DB interactions use PDO prepared statements.
    - Global CSRF verification on all forms.
    - Role-based route protection (`has_role()`) on all directory levels.
    - Database query optimization.
    - Responsive design audit.
