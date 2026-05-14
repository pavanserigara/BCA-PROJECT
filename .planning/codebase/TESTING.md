# Testing

## Current State
- **Automated Tests**: No formal automated test suite (PHPUnit, etc.) detected in the root or directories.
- **Manual Verification**: Testing is likely performed manually by navigating the application and verifying behavior.
- **Utility Scripts**: `test_db.php` and `test_paths.php` are used for basic environment and connectivity checks.

## Recommendations
- Implement PHPUnit for core business logic in `includes/functions.php`.
- Add E2E tests for critical flows (Login, Attendance entry, Assignment upload).
- Use a local staging database for disruptive tests.
