# Phase 4 Verification

## 🎯 Success Criteria
- [ ] Teachers can upload and categorize study materials by subject.
- [ ] Students can view and download study materials for their current semester.
- [ ] Assignment submission status (Submitted/Graded/Late) is visually clear.
- [ ] Student results page includes a performance visualization chart.
- [ ] Aggregate marks and percentages are calculated correctly in the results portal.

## 🧪 Test Cases

### 1. Material Management
- **Action**: Teacher uploads "Notes_Unit1.pdf" for "C++ Programming".
- **Expected**: File is stored in `uploads/materials/`. Student in the same course can see and download it.

### 2. Assignment Feedback
- **Action**: Teacher grades an assignment with feedback "Great work on the logic!".
- **Expected**: Student sees the feedback comment and their grade instantly in `student/assignments.php`.

### 3. Analytics Validation
- **Action**: Populate 3 exam results for a student. Open `student/results.php`.
- **Expected**: The performance chart displays 3 data points reflecting the scores. Total percentage matches the manual calculation.

## 🛡️ Security Audit
- [ ] File upload validation in `teacher/materials.php` (no PHP/scripts).
- [ ] Subject-scoping in `student/materials.php` (no cross-course access).
- [ ] CSRF protection on all grading and upload forms.
- [ ] Sanitization of all user-entered feedback strings.
