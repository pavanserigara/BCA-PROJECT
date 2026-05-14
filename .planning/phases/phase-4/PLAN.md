# Phase 4: LMS & Academic Enrichment

## Objective
Establish a centralized Learning Management System (LMS) for study material sharing and enhance the academic evaluation experience through performance visualization and streamlined result management.

## Scope
1.  **Study Materials Module**:
    *   **Repository**: Centralized storage for subject-specific resources (Notes, PPTs, Reference Links).
    *   **Teacher Control**: Ability to upload, categorize, and update materials.
    *   **Student Hub**: Subject-wise discovery and high-speed downloading of resources.
2.  **Assignment Optimization**:
    *   **Grading Workflow**: Enhance teacher feedback mechanisms.
    *   **Deadline Management**: Better visualization of overdue vs. upcoming tasks for students.
3.  **Performance Analytics (Results)**:
    *   **Visualization**: Introduce performance charts on the student results page.
    *   **Aggregation**: Consolidated view of academic progress across semesters.

## Proposed Changes

### 🗄️ Database Updates (`database/cms.sql`)
*   Add `study_materials` table (subject_id, teacher_id, title, description, file_path, type).

### 🏛️ Teacher Module
*   `teacher/materials.php`: Management interface for academic resources.
*   `teacher/assignments.php`: UI refinements for better status tracking.
*   `teacher/submissions-view.php`: Enhanced grading interface with richer feedback options.

### 🎓 Student Module
*   `student/materials.php`: A clean, grid-based resource discovery portal.
*   `student/results.php`: Integration of Chart.js for performance trend analysis.
*   `student/assignments.php`: Improved "Submission Portal" with file type validation and status indicators.

## Verification Plan

### Automated Tests
*   Verify file upload restrictions (size, type) for materials.
*   Ensure student results are correctly scoped to their enrolled subjects only.

### Manual UAT (User Acceptance Testing)
1.  **LMS Workflow**:
    *   [ ] Teacher uploads a PDF for "Data Structures".
    *   [ ] Student downloads the same PDF from their dashboard.
2.  **Grading Cycle**:
    *   [ ] Student submits an assignment.
    *   [ ] Teacher grades it and adds feedback.
    *   [ ] Student views the grade and feedback in their portal.
3.  **Results Dashboard**:
    *   [ ] Teacher enters marks for a mid-term exam.
    *   [ ] Student views the result card and verify the aggregate calculation.

## Technical Details
*   **Charts**: Use Chart.js (CDN) for performance visualization in `student/results.php`.
*   **Files**: Centralize all academic uploads in `uploads/materials/` and `uploads/submissions/`.
*   **Icons**: Standardize on FontAwesome Pro icons for all academic actions.
