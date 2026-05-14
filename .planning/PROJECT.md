# VidyaSetu ERP

## What This Is
VidyaSetu is a complete and fully functional College Management System (ERP) designed to digitize and automate all major college operations. It provides a unified platform for Super Admins, College Admins, HODs, Teachers, Students, Parents, and various staff roles to manage academic and administrative tasks.

## Core Value
A modern, secure, scalable, and mobile-friendly College ERP system that digitizes and automates all academic and administrative operations in one unified platform.

## Requirements

### Validated
- ✓ Base role-based architecture (Admin, Teacher, Student, Library, Staff) — v0.1
- ✓ Centralized database connection using PDO — v0.1
- ✓ Tailwind CSS based modern UI — v0.1
- ✓ Session-based authentication — v0.1
- ✓ Phase 1-5 Implementation (Attendance, Timetable, Exams, Fees, Services, Portals) — v0.5

### Active
<!-- Milestone 2: ERP Feature Completion & System Enhancement -->
- [ ] Profile System: Profile picture upload, preview, fallback for all roles.
- [ ] Student Document Vault: Secure upload and management of academic documents (Aadhaar, Marks cards, etc.).
- [ ] Teacher Attendance 2.0: Edit/Correction workflow, monthly reports, export to PDF/Excel.
- [ ] Institutional Audit: Identify and fix missing CRUD, broken routes, and UI inconsistencies.
- [ ] Auto-Completion: Implement missing forms, APIs, and reports based on ERP standards.
- [ ] Security Hardening: Global form validation audit and RBAC verification.

### Out of Scope
- Native Android/iOS apps — Focus on web-based ERP first
- AI-based automation features — Future consideration
- Blockchain integration — Future consideration
- Video conferencing system — Use external tools for now
- Microservices architecture — Maintain modular monolithic PHP
- Multi-college SaaS deployment — Single institution focus initially
- Payment gateway integration — Manual fee records first

## Context
- **Technical Environment**: Core PHP with PDO, MySQL, Tailwind CSS (CDN).
- **Architecture**: Modular role-based structure with AJAX-based interactions for UX.
- **Goal**: Production-ready system for real college usage.

## Constraints
- **Tech Stack**: Must use Core PHP (PDO) and Tailwind CSS.
- **Security**: Must use prepared statements and secure session management.
- **Design**: Must be mobile-responsive and user-friendly for non-technical users.

## Key Decisions
| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Core PHP + PDO | Simplicity, speed of development, and maintainability for this specific project. | — Pending |
| Tailwind CDN | Faster initial development; architecture ready for CLI migration. | — Pending |
| AJAX for UX | Smoother interactions without full page reloads. | — Pending |

---
*Last updated: 2026-05-14 after Project Initialization*
