# Phase 1 Plan: Identity & Document Vault

## 1. Database & Directory Setup
- [ ] Create `student_documents` table in the database.
- [ ] Initialize upload directories: `uploads/profiles/` and `uploads/documents/`.
- [ ] Ensure `uploads/` has correct write permissions (via `.htaccess` or directory check).

## 2. Profile Picture System
- [ ] **Backend Handler**: Create `includes/handlers/profile_upload.php`.
    - Implement file size (max 2MB) and type (jpg, png, webp) validation.
    - Sanitize filenames (e.g., `profile_[user_id]_[timestamp].webp`).
    - Update `users` table `profile_pic` field.
    - Delete old profile pic if it exists.
- [ ] **Frontend Integration**: Update `profile.php` in `admin/`, `teacher/`, `student/`, and `parent/`.
    - Replace placeholder camera button with a hidden file input and label.
    - Add AJAX logic (JavaScript) to handle image upload on selection.
    - Update the UI image preview immediately upon success.

## 3. Student Document Vault
- [ ] **Main UI**: Create `student/documents.php`.
    - Use the Sci-Fi aesthetic: Grid of document cards (Aadhaar, SSLC, PUC, TC, Photo, Other).
    - Status badges: "Pending Verification", "Verified", "Rejected".
- [ ] **Upload Handler**: Create `student/includes/handlers/document_upload.php`.
    - Support PDF and common image formats.
    - Validate file size (max 5MB).
    - Map uploads to `student_documents` table.
- [ ] **Navigation**: Add "Document Vault" link to student sidebar.

## 4. Admin Verification Interface
- [ ] **List View**: Create `admin/verify-documents.php`.
    - Show list of students with pending document uploads.
    - Filter by department/course.
- [ ] **Detail View**: Add a modal or separate page to view document details.
    - Embedded PDF viewer/Image preview.
    - Approve/Reject buttons with remarks field.
- [ ] **Navigation**: Add "Document Verification" link to admin sidebar under "Students".

## 5. Verification & Security
- [ ] Verify all new file upload handlers have `csrf_guard()`.
- [ ] Ensure `student_id` is validated against `$_SESSION['user_id']` for all student-side operations.
- [ ] Implement `has_role('admin')` for the verification interface.
- [ ] Test the fallback mechanism for missing profile pictures.
