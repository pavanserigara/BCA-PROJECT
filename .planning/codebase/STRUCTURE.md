# Structure

## Directory Hierarchy
- `admin/`: Administrative dashboard and management modules.
- `teacher/`: Faculty dashboard, attendance, assignments, and marks management.
- `student/`: Student portal for viewing profile, assignments, and results.
- `library/`: Library management system modules.
- `staff/`: Non-teaching staff modules.
- `includes/`: Global PHP includes (database, functions).
- `assets/`: Static files (CSS, Images, JS).
- `database/`: SQL schema files (`cms.sql`).
- `uploads/`: User-uploaded files (profile pics, assignments).
- `exams/`, `fees/`, `reports/`, `timetable/`: Specialized feature directories.

## Key Files
- `index.php`: Entry point (likely redirects to login or dashboard).
- `login.php`: Authentication entry.
- `logout.php`: Session termination.
- `test_db.php`, `test_paths.php`: Utility scripts for environment verification.
- `update_db.php`: Database migration/update script.
