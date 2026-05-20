# Project Record: VidyaSetu College Management System

---

# Chapter 1: Synopsis

## 1.1 Introduction
VidyaSetu is a comprehensive web-based College Management System (CMS) designed to streamline various administrative, academic, and logistical processes within an educational institution. The system acts as a digital bridge between students, faculty, and administration, ensuring smooth day-to-day operations.

## 1.2 Project Category
- **Category:** Web-Based Application
- **Domain:** Education / Institutional Management
- **Architecture:** Client-Server Monolithic Architecture

## 1.3 Problem Statement
Educational institutions often rely on fragmented, paper-based, or outdated standalone software for managing student data, attendance, examinations, fees, and library records. This leads to data silos, inefficiencies, delayed communication, and administrative overhead. A centralized system is critically needed to integrate all these processes into a single, cohesive, and easily accessible platform.

## 1.4 Objectives
- To automate and digitize all core college operations, including student admissions, attendance tracking, and fee management.
- To provide role-based, secure portals for Administrators, Teachers, Students, and other staff members.
- To improve transparency and communication through integrated notice boards, messaging, and a grievance system.

## 1.5 Proposed System
The proposed system, VidyaSetu ERP, is a centralized platform that consolidates all modules of college management into a unified web interface. By utilizing role-based access control, users are directed to customized dashboards that provide the exact tools and information they need. The backend database securely stores and links student records, academic data, library transactions, and financial data in a normalized structure.

## 1.6 Methodology / Working
- **Data Acquisition:** Data is inputted through secure online forms by administrators (for initial setup and admissions), teachers (for attendance and marks), and students (for assignments and complaints).
- **Processing & Logic:** The backend PHP scripts process these inputs, validating data against business rules (e.g., role verification, fee calculation, overdue library fines), and interact with the MySQL database.
- **Rendering & State:** The frontend renders dynamic HTML views stylized with Tailwind CSS. Sessions are maintained via PHP session variables to ensure secure, stateful interactions across the different dashboards.

## 1.7 Hardware Requirements
- **Processor:** Intel Core i3 or equivalent (or higher)
- **RAM:** Minimum 4 GB (8 GB Recommended)
- **Storage:** Minimum 20 GB of free Hard Disk Space
- **Network:** Active internet connection required

## 1.8 Software Requirements
- **Operating System:** Windows, Linux, or macOS
- **Backend Language:** PHP 8+
- **Frontend Technologies:** HTML5, CSS3 (Tailwind CSS), JavaScript
- **Web Server:** Apache or Nginx
- **Database:** MySQL

## 1.9 Modules and Features of Modules
- **Admin Module**
  - User and Role Management (Students, Faculty, Staff)
  - Department, Course, and Subject Management
  - Fees Structure Configuration and Tracking
  - System Wide Settings and Notice Board Broadcasting
- **Teacher Module**
  - Attendance Tracking and Subject Management
  - Marks Entry and Assignment Creation/Grading
  - Internal Messaging and Notice Viewing
- **Student Module**
  - Personal Profile and Academic Results Viewing
  - Assignment Submission and Attendance Tracking
  - Fee Payment Status and Library Issue Tracking
- **Library Module**
  - Book Cataloging and Inventory Management
  - Issue/Return Tracking and Overdue Fine Calculation

## 1.10 Module Description
- **Admin Module:** The core control panel used by college administrators. It has the highest level of access, allowing for the configuration of academic years, management of all user accounts, oversight of financial transactions, and generation of comprehensive reports.
- **Teacher Module:** A portal designed for teaching staff to manage their daily academic responsibilities, such as recording attendance, updating exam marks, giving assignments, and communicating with their students.
- **Student Module:** A self-service portal for enrolled students. It provides them with real-time access to their academic progress, timetable, fee dues, library records, and allows them to submit assignments or raise grievances.

## 1.11 Database Tables
- `users`: Stores all user accounts, hashed passwords, and their specific roles (admin, student, teacher, etc.).
- `students`: Contains detailed personal, academic, and enrollment information specific to students.
- `attendance`: Records day-to-day student attendance marked by teachers for specific subjects.
- `marks`: Stores the academic performance and grades of students across various exams and internal assessments.
- `fees_structure` & `fee_payments`: Defines the cost of courses and logs individual payment transactions and dates.
- `library_books` & `issued_books`: Catalogs the library's inventory and tracks which books are currently borrowed by whom.

## 1.12 Advantages
- **Centralized Data:** Eliminates data redundancy and inconsistencies by keeping a single source of truth for all college records.
- **Improved Efficiency:** Drastically reduces manual paperwork, saving time for both administrative staff and teachers.
- **Enhanced Communication:** Provides integrated platforms (notices, messages) ensuring that all stakeholders are promptly informed.

## 1.13 Applications
- Degree Colleges and Universities
- Engineering and Technical Institutes
- K-12 Schools (with minor structural modifications)

## 1.14 Limitations
- Requires a stable and consistent internet connection for real-time access and updates.
- Initial setup and data migration from legacy paper-based systems can be time-consuming.

## 1.15 Future Scope
- **Mobile Application:** Developing dedicated Android and iOS apps for students and teachers for easier access on the go.
- **Payment Gateway Integration:** Integrating real-time online payment gateways (like Stripe or Razorpay) for seamless fee collection.
- **AI Analytics:** Implementing predictive analytics to track student performance trends and identify at-risk students early.

## 1.16 Conclusion
VidyaSetu CMS provides a robust, scalable, and user-friendly digital infrastructure for educational institutions. By automating tedious administrative tasks and centralizing academic data, it empowers educators to focus more on teaching and provides students with a seamless, informative academic experience.

---

# Chapter 2: Software Requirement Specification (SRS)

## 2.1 Introduction

### 2.1.1 Purpose
The purpose of this SRS document is to define the specific software requirements for the VidyaSetu College Management System. It serves as a blueprint for the development team and provides stakeholders with a clear understanding of the system's capabilities, constraints, and operational environment.

### 2.1.2 Scope
VidyaSetu encompasses user management, academic structuring, attendance, examinations, fee tracking, library management, and internal communication. It does NOT currently cover external alumni networking, human resource payroll processing, or advanced LMS video hosting.

### 2.1.3 Definitions, Acronyms, and Abbreviations
- **CMS:** College Management System
- **ERP:** Enterprise Resource Planning
- **RBAC:** Role-Based Access Control

### 2.1.4 Overview
The rest of this SRS details the technical stack, overall system behavior, specific module functions, and non-functional requirements such as performance and security expectations.

## 2.2 Language and Tools

### 2.2.1 Frontend
- **HTML5/Tailwind CSS:** Used for structuring and stylizing the user interface with a modern, responsive design.
- **JavaScript/AJAX:** Used for client-side validation and asynchronous data fetching to improve user experience.

### 2.2.2 Backend
- **PHP:** The core server-side scripting language handling business logic, authentication, and database interactions.

### 2.2.3 Database
- **MySQL:** The relational database management system used to persistently store and organize all system data.

## 2.3 Overall Description

### 2.3.1 Product Functions
- Secure Multi-Role Authentication and Authorization
- Centralized Academic Record Maintenance
- Automated Attendance and Examination Processing
- Financial Fee Tracking and Library Inventory Management

### 2.3.2 User Characteristics
- **Administrator:** Requires moderate technical proficiency. Responsible for system configuration, user onboarding, and overall supervision. Full system permissions.
- **Teacher:** Requires basic technical proficiency. Responsible for entering academic data, attendance, and managing classes. Permissions restricted to assigned subjects and students.
- **Student:** Requires basic technical proficiency. Uses the system mainly to view data and submit requests/assignments. Read-only access to most records, with write access limited to their specific submissions and profiles.

### 2.3.3 General Constraints
- The system must comply with institutional data privacy policies regarding student records.
- It is designed to run on modern web browsers (Chrome, Firefox, Safari, Edge) and relies on JavaScript being enabled on the client side.

### 2.3.4 Assumptions and Dependencies
- It is assumed that the deployment server has PHP and MySQL configured correctly.
- The system depends on users having valid, unique email addresses or identification numbers for account creation and login.

## 2.4 Specific Requirements

### 2.4.1 External Interface Requirements

#### 2.4.1.1 User Interfaces
The UI must follow a consistent, responsive, "Premium Glassmorphic" design standard (as defined by project updates). It must feature distinct layouts for Admin, Teacher, and Student dashboards while maintaining a unified visual language using Tailwind CSS. Forms must include clear validation feedback.

#### 2.4.1.2 Hardware Interfaces
The software operates over standard network interfaces and does not require direct interaction with specialized hardware, aside from the standard server environment hosting the application.

#### 2.4.1.3 Software Interfaces
- The application will interface with a MySQL database server over TCP/IP (usually localhost in deployment).
- It will utilize PHP's PDO extension for secure database connectivity.

#### 2.4.1.4 Communication Interfaces
- All communication between the client browser and the server will occur via HTTP/HTTPS.
- AJAX requests will utilize JSON for data exchange formatting.

### 2.4.2 Functional Requirements

#### 2.4.2.1 Module Description
- **Authentication Module:** Must securely authenticate users against the `users` table, hashing passwords appropriately, and initializing session state with the correct role attributes.
- **Attendance Module:** Must allow a teacher to select a date, course, and subject, and present a list of enrolled students. The system must accurately save present/absent statuses to the `attendance` table and prevent duplicate entries for the same day and subject.

### 2.4.3 Performance Requirements

#### 2.4.3.1 Static Requirements
- The database schema must be normalized (typically to 3NF) to minimize redundancy and prevent data anomalies.
- Uploaded files (assignments, profile pictures) must be constrained by size limits (e.g., max 5MB) and stored in organized directories (`uploads/`).

#### 2.4.3.2 Dynamic Requirements
- Page load times should ideally be under 2 seconds on standard broadband connections.
- The system must support concurrent access by expected user loads (e.g., all students checking results simultaneously) without connection timeouts or database locking errors (utilizing optimized connection management like the Singleton pattern).

### 2.4.4 Design Constraints
- Tailwind CSS must be used for styling, avoiding inline CSS where possible.
- Security best practices must be followed, specifically mitigating SQL injection (via Prepared Statements) and Cross-Site Scripting (XSS) (via output sanitization).
