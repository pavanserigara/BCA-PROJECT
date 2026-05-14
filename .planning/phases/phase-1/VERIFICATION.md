# Verification Plan: Phase 1 - Identity & Document Vault

## 1. Unit & Functional Tests
- **Profile Picture Upload**:
    - [ ] Test successful upload for all roles (Admin, Teacher, Student, Parent).
    - [ ] Verify image replaces the old one in `uploads/profiles/`.
    - [ ] Verify database `profile_pic` column is updated.
    - [ ] Test invalid file type (e.g., .txt) - should be rejected.
    - [ ] Test file too large (> 2MB) - should be rejected.
- **Document Vault**:
    - [ ] Student can upload each required document type.
    - [ ] Verify documents are saved in `uploads/documents/`.
    - [ ] Verify `student_documents` table records the upload with `pending` status.
    - [ ] Test PDF vs Image upload handling.
- **Admin Verification**:
    - [ ] Admin sees list of pending documents.
    - [ ] Admin can view the document preview.
    - [ ] Admin can Approve - status changes to `verified`.
    - [ ] Admin can Reject with remarks - status changes to `rejected`.

## 2. Security Verification
- [ ] Attempt CSRF attack on document upload handler - should fail.
- [ ] Attempt to upload to another student's ID (ID manipulation) - should fail.
- [ ] Attempt to access `admin/verify-documents.php` as a student - should be redirected to login.
- [ ] Check if uploaded files are renamed to prevent path traversal or script execution.

## 3. UI/UX Verification
- [ ] Verify real-time image preview in profile page works without full reload.
- [ ] Check mobile responsiveness of the Document Vault grid.
- [ ] Verify success/error flash messages are displayed correctly.
- [ ] Ensure the default avatar fallback works for users with no photo.
