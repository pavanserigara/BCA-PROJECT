# Phase 5: Advanced Portals

## Objective
Finalize the Milestone 1 core by implementing the Career Placement Hub, a dedicated Parent Observation Portal, and integrated Student Logistics visualization.

## Scope
1.  **Placement Hub**:
    *   **Recruitment Management**: Admin tool to post, update, and manage job opportunities.
    *   **Application Lifecycle**: Student interface to apply for jobs with resume uploads and status tracking.
2.  **Parent Portal**:
    *   **Stakeholder Access**: Dedicated dashboard for parents to monitor their child's academic health.
    *   **Insight Modules**: Read-only views of student attendance and semester results.
3.  **Student Logistics**:
    *   **Resource Visualization**: Student-side view of assigned hostel rooms and bus routes.

## Proposed Changes

### 🗄️ Database Updates (`database/cms.sql`)
*   **Users**: Add `parent` to the role ENUM.
*   **Placements**: Create `placements` table (company, role, package, etc.) and `placement_applications` table.
*   **Parents**: Create `parents` table (user_id, student_id, relation) to establish the link.

### 🏛️ Admin Module
*   `admin/placements.php`: Upgrade from hardcoded to dynamic CRUD with application tracking.

### 🎓 Student Module
*   `student/placements.php`: Job board for students to view opportunities and submit applications.
*   `student/logistics.php`: Display assigned housing and transport details.

### 👪 Parent Module (New)
*   `parent/dashboard.php`: Child's performance overview.
*   `parent/attendance.php`: Detailed attendance logs for the child.
*   `parent/results.php`: Examination results of the child.

## Verification Plan

### Automated Tests
*   Verify parent-student linking logic (Parent A should NOT see Student B's data).
*   Check placement application status transitions.

### Manual UAT (User Acceptance Testing)
1.  **Placement Cycle**:
    *   [ ] Admin posts a "Google" job.
    *   [ ] Student applies with a mock resume.
    *   [ ] Admin views the application and updates status to "Shortlisted".
2.  **Parent Portal**:
    *   [ ] Create a parent user and link to a student.
    *   [ ] Login as parent and verify child's attendance matches student's portal.
3.  **Logistics**:
    *   [ ] Verify student sees their assigned bus number and hostel room.

## Technical Details
- **Resumes**: Store in `uploads/resumes/` with strictly unique naming.
- **Access Control**: Use `has_role('parent')` and verify `student_id` in session/link for all parent-side queries.
- **UI**: Maintain the high-contrast card aesthetic with "Glassmorphism" accents for the Parent Portal to make it feel premium and reassuring.
