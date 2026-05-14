# Phase 5 Verification

## 🎯 Success Criteria
- [ ] Admins can create and manage dynamic job postings.
- [ ] Students can apply for jobs and track their application status.
- [ ] Parents can securely view their child's attendance and academic results.
- [ ] Students can see their assigned hostel and transport logistics.
- [ ] All parent-side data access is strictly restricted to their linked child.

## 🧪 Test Cases

### 1. Placement Workflow
- **Action**: Create a job for "Microsoft", then login as a student and apply.
- **Expected**: Application appears in `admin/placements.php` under the "Microsoft" listing.

### 2. Parent Security
- **Action**: Login as a parent. Try to change the `student_id` in a URL or query (if applicable).
- **Expected**: System restricts access or only shows data for the linked `student_id`.

### 3. Logistics View
- **Action**: Assign Student X to Bus Route 10 and Room A-101.
- **Expected**: Student X sees these specific details in `student/logistics.php`.

## 🛡️ Security Audit
- [ ] `csrf_guard()` on placement applications and job posting.
- [ ] Rigid check for `relation` and `student_id` in `parents` table for all parent queries.
- [ ] File validation for resume uploads (PDF only recommended).
- [ ] Role-based middleware enforcement for the `/parent/` directory.
