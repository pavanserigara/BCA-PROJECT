# Architecture

## Overview
The project follows a **Modular Role-Based Architecture**. It uses a traditional multi-page application (MPA) approach where each role (Admin, Teacher, Student) has its own dedicated directory and set of pages.

## Design Patterns
- **Singleton-ish DB Connection**: `includes/db.php` provides a global `$pdo` object.
- **Helper Function Library**: `includes/functions.php` acts as a central utility hub.
- **Template Inheritance (Include-based)**: Pages use `header.php` and `footer.php` (often localized in role-specific `includes/` folders) to maintain consistent UI.

## Authentication & Authorization
- **Session-based Auth**: `session_start()` is called in `includes/functions.php`.
- **Role Guards**: Functions like `has_role()` and `require_login()` are used at the top of each page to enforce access control.

## Database Schema
- Relational database (MySQL) with tables for `users`, `settings`, and likely `attendance`, `assignments`, `marks`, etc. (as suggested by file names).

## Client-Server Interaction
- **Forms**: standard HTML form POST/GET.
- **Modern UI**: Frontend is enhanced with Tailwind CSS for a responsive, dashboard-like experience.
