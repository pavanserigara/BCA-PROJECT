# Phase 1 Verification

## 1. Security (CSRF)
- [ ] `includes/functions.php` has CSRF generation/verification functions.
- [ ] `login.php` rejected without token.
- [ ] `attendance-take.php` rejected without token.

## 2. Database
- [ ] `attendance` table has unique index on `(student_id, subject_id, date)`.
- [ ] Attempting duplicate manual INSERT fails with SQL error or handled by PHP.

## 3. Attendance Logic
- [ ] Teacher can mark attendance.
- [ ] Teacher can update previously marked attendance.
- [ ] Teacher cannot mark attendance for tomorrow.
- [ ] Student dashboard shows correct percentage.
- [ ] Low attendance (<75%) is visually flagged.

## 4. UX/UI
- [ ] Mobile view for `attendance-take.php` is usable (no horizontal scroll, buttons large enough).
- [ ] Flash messages display correctly for success/error.
