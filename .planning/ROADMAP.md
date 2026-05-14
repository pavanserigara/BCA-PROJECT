# ROADMAP.md - Milestone 2: ERP Feature Completion & System Enhancement

## Phase 1: Identity & Document Vault
*Focus: Enhancing student records and user profiles.*
- [ ] Implement Profile Picture system (Upload, Preview, Default fallback) for all roles.
- [ ] Build Student Document Vault (Aadhaar, Marks cards upload).
- [ ] Create Admin Verification interface for student documents.

## Phase 2: Academic Management 2.0
*Focus: Advanced attendance tracking and reporting.*
- [ ] Implement Attendance Edit/Correction workflow for teachers.
- [ ] Build Monthly Attendance Report generator (Subject-wise/Class-wise).
- [ ] Add PDF/Excel export capability for attendance records.
- [ ] Implement Attendance Analytics Dashboard for teachers.

## Phase 3: Module Expansion
*Focus: Completing institutional roles.*
- [ ] Implement Librarian Portal (`library/`) with full book and log management.
- [ ] Implement Staff/Accountant Portal (`staff/`) for non-academic management.
- [ ] Update `login.php` and `includes/functions.php` for new role routing.
- [ ] Integrate new portals into the global navigation system.

## Phase 4: Audit & Auto-Completion
*Focus: Professional polishing and production readiness.*
- [ ] Implement advanced Search and Filtering for all major data tables.
- [ ] Add Pagination to all list views.
- [ ] Implement generic Export (CSV/PDF) for Student and Faculty lists.
- [ ] Audit and fix broken routes and UI inconsistencies.

## Phase 5: Security & Optimization
*Focus: Hardening the system for deployment.*
- [ ] Global audit of PDO usage to ensure 100% prepared statement coverage.
- [ ] Verify CSRF protection and role-based access on all new files.
- [ ] Optimize database queries and add necessary indexes.
- [ ] Final Responsive Design audit and polishing.
