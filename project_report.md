# MANGALORE UNIVERSITY

***

<br><br><br>
<div align="center">
  <!-- Official Mangalore University Logo Crest Placeholder -->
  <strong>[MANGALORE UNIVERSITY OFFICIAL EMBLEM]</strong>
  <br><br><br><br>
  
  # VIDYASETU COLLEGE MANAGEMENT SYSTEM
  
  <br><br><br>
  
  A Project Report Submitted to Mangalore University in partial fulfillment of the requirements for the award of the degree of
  
  ### BACHELOR OF COMPUTER APPLICATION (BCA)
  
  <br><br>
  
  **Submitted By:**
  ### PAVAN SERIGARA
  **UUCMS Register Number: 23BCA0045**
  
  <br><br>
  
  **Under the Guidance of:**
  ### PROF. JANE DOE
  **Project Supervisor & Head of Department**
  
  <br><br><br><br>
  
  ### DEPARTMENT OF COMPUTER SCIENCE
  ### CANARA COLLEGE, MANGALURU
  **ACADEMIC YEAR: 2025-2026**
</div>

\pagebreak

# OFFICIAL CERTIFICATE OF COMPLETION

### CANARA COLLEGE, MANGALURU
**DEPARTMENT OF COMPUTER SCIENCE & APPLICATIONS**
*Motto: "To Light and To Love"*

***

This is to certify that the Mini Project work entitled **"VIDYASETU COLLEGE MANAGEMENT SYSTEM"** is a bonafide work carried out by **PAVAN SERIGARA** (UUCMS Register Number: **23BCA0045**) in partial fulfillment of the requirements for the award of the degree of **Bachelor of Computer Application (BCA)** of Mangalore University during the academic year 2025-2026.

This report has been approved as it satisfies the academic requirements in respect of project work prescribed for the Bachelor of Computer Application degree.

<br><br><br><br>

**_____________________**  
**Prof. Jane Doe**  
*Project Guide & HOD*  
Department of Computer Science

<br><br><br><br>

**_____________________**  
**Dr. Premila D'Souza**  
*Principal*  
Canara College, Mangaluru

<br><br><br><br>

### EXAMINER PANEL MARKS MATRIX

| Examiner | Name | Signature | Marks Awarded |
| :--- | :--- | :--- | :--- |
| **Examiner 1 (Internal)** | ___________________________ | ____________________ | ______ / 100 |
| **Examiner 2 (External)** | ___________________________ | ____________________ | ______ / 100 |

<br>
**Date of Examination:** ____________________

\pagebreak

# CANDIDATE DECLARATION

***

I, **PAVAN SERIGARA**, student of Bachelor of Computer Application (BCA), Canara College, Mangaluru, bearing UUCMS Register Number **23BCA0045**, do hereby declare that the project work entitled **"VIDYASETU COLLEGE MANAGEMENT SYSTEM"** submitted to Mangalore University in partial fulfillment of the requirements for the award of the degree of **Bachelor of Computer Application (BCA)** is a record of original work carried out by me under the guidance and supervision of **Prof. Jane Doe**, Head of the Department of Computer Science.

I further declare that this project is authentic, the database schemas and codebase have been developed by me, and the contents of this report have not been previously submitted in part or full for the award of any other degree, diploma, fellowship, or associate-ship of any other university or institution.

<br><br><br><br><br>

**Place:** Mangaluru  
**Date:** 2026-05-26  

<br><br>

**___________________________**  
**PAVAN SERIGARA**  
*UUCMS Reg No. 23BCA0045*

\pagebreak

# ACKNOWLEDGEMENT

***

We have taken efforts in the "VIDYASETU COLLEGE MANAGEMENT SYSTEM" project, however, it would not have been possible without the kind support and help of many individuals. I would like to extend my sincere thanks to all of them.

First and foremost, I wish to express my deepest sense of gratitude to the Almighty for giving me the strength, health, and wisdom to successfully complete this project.

I express my heartfelt gratitude to our Principal, **Dr. Premila D'Souza**, for providing the challenging opportunity to undertake this Mini Project work for the completion of our BCA course.

I express my sincere gratitude to **Prof. Jane Doe**, HOD of the Computer Science Department, for her valuable suggestions, encouragement, and constant supervision as our project guide, providing the necessary information and support in completing the project.

I also extend my sincere thanks to the department laboratory administrators, who helped in setting up the Apache and MySQL database environments on our local workstation, and the faculty members of the Computer Science Department for their academic support.

Lastly, I thank my family and peers for their continuous encouragement and constructive criticisms, which served as a source of strength during the project completion process.

<br><br><br><br><br>

**PAVAN SERIGARA**  
*Department of Computer Science*  
*Canara College, Mangaluru*

\pagebreak

# TABLE OF CONTENTS

***

### **SECTION I: FRONT MATTER**
1. Cover Page ......................................................................................................................................... i
2. Official Certificate of Completion ...................................................................................................... ii
3. Candidate Declaration ....................................................................................................................... iii
4. Acknowledgement ............................................................................................................................. iv
5. Table of Contents .............................................................................................................................. v

### **SECTION II: THE FORMAL TECHNICAL CHAPTERS**
* **CHAPTER 1: INTRODUCTION & CORE HYPOTHESIS** ................................................................. 1
  * 1.1 Introduction and Overview .............................................................................................................. 1
  * 1.2 Title of the Project ........................................................................................................................... 3
  * 1.3 Scope and Boundaries of the System ............................................................................................... 3
  * 1.4 Objectives of the Platform ................................................................................................................ 5
  * 1.5 System Features and User Capabilities ............................................................................................ 6
  * 1.6 Platform Stack Profile (Tools & Platforms Used) ............................................................................. 8
  * 1.7 Testing Tools Overview ................................................................................................................... 9
  * 1.8 Expected Outcome ........................................................................................................................... 9
* **CHAPTER 2: SOFTWARE REQUIREMENTS SPECIFICATION (SRS)** .............................................. 11
  * 2.1 Purpose of the Specification ............................................................................................................. 11
  * 2.2 Scope of the Deliverable .................................................................................................................. 11
  * 2.3 User Classes and Characteristics .................................................................................................... 12
  * 2.4 Assumptions and Dependencies ..................................................................................................... 13
  * 2.5 Hardware and Software Requirements ........................................................................................... 14
  * 2.6 Functional Requirements Matrix (Reference Standard) .................................................................... 15
* **CHAPTER 3: SYSTEM LOGICAL FLOW & ARCHITECTURE** ....................................................... 19
  * 3.1 System Design and Architectural Narrative ...................................................................................... 19
  * 3.2 Context Flow Diagram (CFD - Level 0 DFD) .................................................................................... 20
  * 3.3 Data Flow Diagram (DFD - Level 1 DFD) ........................................................................................ 21
  * 3.4 Data Flow Diagram (DFD - Level 2 Attendance DFD) ...................................................................... 22
* **CHAPTER 4: PHYSICAL DATABASE DESIGN** ........................................................................... 24
  * 4.1 RDBMS Context and Integrity Constraints ....................................................................................... 24
  * 4.2 Physical Table Definitions ............................................................................................................... 25
  * 4.3 Entity-Relationship (ER) Diagram Symbols Mapping ......................................................................... 32
  * 4.4 ER Diagram Schema Layout ........................................................................................................... 33
* **CHAPTER 5: SOURCE CODE IMPLEMENTATION LISTING** ........................................................ 35
  * 5.1 Database Connection Configuration (`includes/db.php`) .................................................................... 35
  * 5.2 Core Functions and Security Filters (`includes/functions.php`) ........................................................... 36
  * 5.3 Authentication Gateway Control (`login.php`) .................................................................................... 42
  * 5.4 Student Registration Gateway (`signup.php`) .................................................................................... 48
  * 5.5 Executive Admin Dashboard Controller (`admin/dashboard.php`) ....................................................... 51
  * 5.6 Student Enrollment Processing Snippet (`admin/students-add.php`) .................................................. 57
* **CHAPTER 6: USER INTERFACE (UI) WIREFRAMES** ................................................................. 59
  * 6.1 Client Welcome Gateway (Login View) ............................................................................................. 59
  * 6.2 Master Administrative Control Form .................................................................................................. 60
  * 6.3 Faculty Portal Attendance Tracker ................................................................................................... 61
  * 6.4 Student Portal Dashboard Grid ......................................................................................................... 62
* **CHAPTER 7: COMPREHENSIVE QUALITY ASSURANCE & TESTING** .......................................... 63
  * 7.1 Testing Introduction & Methodologies ............................................................................................. 63
  * 7.2 Unit Testing Reports ......................................................................................................................... 64
  * 7.3 Integration Testing Reports .............................................................................................................. 68
  * 7.4 General Button Test Cases ............................................................................................................... 69
  * 7.5 Structural (White Box) and Interface (Black Box) Testing .................................................................. 71
  * 7.6 System Testing Summary ................................................................................................................ 72
* **CHAPTER 8: CONCLUSIONS, OUTCOMES & FUTURE WORK** ................................................... 73
  * 8.1 Project Conclusion & MU Regulation Conformity ............................................................................ 73
  * 8.2 Proposed Future Enhancements ....................................................................................................... 73
  * 8.3 Academic Bibliography & Citations ................................................................................................... 74

\pagebreak

# CHAPTER 1: INTRODUCTION & CORE HYPOTHESIS

***

### 1.1 Introduction and Overview
In the modern educational landscape, higher education institutions face an unprecedented volume of administrative, academic, and financial transactions. Historically, degree colleges affiliated with Mangalore University have relied on traditional paper registers, flat file folders, and localized spreadsheet files. These methods present significant vulnerabilities, including data fragmentation, redundancy, physical wear-and-tear, difficulty in cross-referencing records, and delayed communication between administrators, faculty members, parents, and students.

The **VidyaSetu College Management System** aims to create a comprehensive software solution for managing a college's inventory of student data, academic records, fee structures, library records, and parent-teacher communications. It is designed to streamline operations, enhance administrative efficiency, and improve the overall user experience. The system encompasses features such as student registration, academic marks entry, class scheduling, attendance tracking, and internal grievance management.

By migrating all transactional and operational activities into a single relational database (RDBMS), VidyaSetu enables real-time synchronization across departments. When a teacher marks a student absent, or when an administrative clerk records a tuition payment, the data is instantly reflected on the respective student and parent dashboards. This centralized design reduces administrative delay, eliminates redundant data entry, and ensures a single, secure source of truth.

By utilizing a client-server architecture built on PHP and MySQL, the system allows secure, concurrent access by students, faculty, and administrative staff from any authorized terminal. It enforces access controls to ensure that only authorized users can modify grade profiles, attendance registries, and fee collection records.

### 1.2 Title of the Project
The project is officially titled **"VIDYASETU COLLEGE MANAGEMENT SYSTEM"** (referred to commercially and academically as **VidyaSetu ERP**).

### 1.3 Scope and Boundaries of the System
The system is designed as a centralized, role-based administrative portal for mid-sized educational institutions.

#### In-Scope Functional Modules:
- **Centralized System Configuration:** Management of academic years, semesters, departments, course structures, and curriculum listings.
- **Role-Based Access Control (RBAC):** Distinct portals and dashboards configured for administrators, faculty members, students, and parents.
- **Student Lifecycle Tracking:** Onboarding of students, profile verification, classroom assignments, document uploads, and academic performance history.
- **Faculty Management:** Tracking departmental assignments, subject allocations, qualifications, and teaching schedules.
- **Academic Telemetry:** Dynamic attendance capture, lesson planning, and scheduling tools.
- **Assessment Management:** Internal and semester marks registration, grade generation, and performance reporting.
- **Financial Module:** Fee structure formulation, installment tracking, and payment receipt logging.
- **Library Module:** Book inventory management, check-out/check-in processing, and automatic fine calculation.
- **Logistics Module:** Accommodation registration and transit route tracking.
- **Placement Hub:** Management of corporate placement schedules, recruitment eligibility verification, and application status tracking.
- **Grievance Box:** Internal complaint registration, tracking, and resolution logs.

#### Out-of-Scope System Boundaries:
- **Video Content Hosting:** VidyaSetu does not host or stream live video lectures. It leaves video-based learning management systems (LMS) to external platforms (such as Zoom or Microsoft Teams) but allows posting reference URLs.
- **Payroll Processing:** Automated employee tax configurations, provident fund deductions, and bank integrations are excluded, as these are typically handled by standalone banking modules.
- **External Payment Aggregation:** The initial release simulates financial transactions rather than processing actual credit card/UPI transactions, ensuring compliance with student-level sandbox environments.
- **Hardware Integration:** The software operates over standard network interfaces and does not interface directly with hardware tools like biometric scanners or RFID gateways.

### 1.4 Objectives of the Platform
The core objectives of the project are:
- **To create a user-friendly website** that allows the college to reach a wider student base and manage information centrally.
- **To provide a 24/7 online platform** for students, faculty, and administrators to browse, monitor, and update institutional data.
- **To develop a robust academic catalogue** with detailed records of subjects, courses, marks, and student attendance profiles.
- **To create an intuitive and easy-to-navigate interface** that bridges the communication gap between student portals and admin control systems.

### 1.5 System Features and User Capabilities
VidyaSetu ERP divides its capabilities into specialized dashboards tailored to each user type:

```
                            +----------------------+
                            |    VidyaSetu ERP     |
                            +----------+-----------+
                                       |
        +------------------+-----------+-----------+-------------------+
        |                  |                       |                   |
        v                  v                       v                   v
+---------------+  +---------------+       +---------------+   +---------------+
| Admin Portal  |  | Faculty Portal|       | Student Portal|   | Parent Portal |
+---------------+  +---------------+       +---------------+   +---------------+
| - User CRUD   |  | - Attendance  |       | - View Grades |   | - Track Dues  |
| - Setup Fees  |  | - Enter Marks |       | - Submit Work |   | - Attendance  |
| - Post Alerts |  | - Upload Notes|       | - Library Dues|   | - Complaints  |
+---------------+  +---------------+       +---------------+   +---------------+
```

- **Clear Categorization:** Easily browse academic details, grades, and fee reports by subject, semester, or transaction category.
- **Detailed Profiles:** Get a comprehensive understanding of each user account with detailed contact, academic, and financial information.
- **Streamlined Processing:** Register students, mark attendance, and submit grievances in just a few clicks with our secure and efficient forms.

### 1.6 Platform Stack Profile (Tools & Platforms Used)

#### Software:
- **Front End:** HTML, CSS, and JavaScript.
- **Back End:** PHP and MySQL.
- **Platform:** Visual Studio Code.
- **Server Bundle:** XAMPP local environment.

#### Hardware:
- **Monitor:** Any Standard Monitor.
- **Keyboard:** Standard Keyboard.
- **RAM:** 2 GB RAM or more.
- **Processor:** CPU Intel® Core™ i5-1035G1 or higher.

### 1.7 Testing Tools Overview
- **Manual Testing** with a set of real-time validation data.
- **SQL Parameterization Validation** using script checks.
- **XSS Payload Tests** via input forms.

### 1.8 Expected Outcome
- **A fully functional online portal** with secure user/admin logins, attendance marking modules, grade lists, fee structures, and grievance logs.
- **Expanding administrative efficiency** beyond physical files and registries.
- **Responsive institutional support** through immediate notice board uploads and tracking updates.
- **Automating student management** to reduce manual labor and improve data consistency.

\pagebreak

# CHAPTER 2: SOFTWARE REQUIREMENTS SPECIFICATION (SRS)

***

### 2.1 Purpose of the Specification
The purpose of this document is to specify each requirement of the system in detail, while helping the evaluators obtain a better understanding of the project. The document describes the project's target audience, user interfaces, hardware and software baselines, constraints, and functional targets. In short, the purpose of this SRS is to provide a detailed overview of our software product, its parameters, and goals.

### 2.2 Scope of the Deliverable
The validation of the process is done through this software requirement specification. If there are any changes in the software in the future, the modifications are managed through this specification sheet. The developer is responsible for keeping the documentation in line with any changes seen in the system. The platform will serve as a centralized system mapping admissions, grades, fee receipts, and library catalogs.

### 2.3 User Classes and Characteristics
- **Administrator:** College management staff with full system clearance. Tasks include managing user accounts, configuring curriculums, updating system settings, and reviewing audit logs.
- **Teacher (Faculty):** Teaching staff with access restricted to assigned departments and subjects. Tasks include recording daily attendance, entering exam marks, uploading study materials, and tracking assignment submissions.
- **Student:** Enrolled learners. Permissions are restricted to their own profiles. Activities include reviewing attendance and grades, downloading study materials, uploading assignments, checking library statuses, and lodging grievances.
- **Parent:** Guardians linked directly to student profiles. Access is read-only. Parents use the portal to monitor their child's academic progress, attendance records, and outstanding fees.
- **Librarian / Staff:** Operations staff responsible for cataloging books, recording transactions, and tracking overdue fines.

### 2.4 Assumptions and Dependencies
- It is assumed that the project is installed on a system that satisfies the hardware and software configurations.
- Users will have a valid Username and Password to access the website.
- Users must provide correct details about themselves during registration.
- Administrators should be careful in updating and removing data to prevent database inconsistency.

### 2.5 Hardware and Software Requirements

#### Hardware Requirement:
- **Monitor:** Any standard Monitor.
- **Input:** Standard Keyboard and Mouse.
- **Memory:** 2 GB RAM or more.
- **Processor:** CPU Intel® Core™ i5-1035G1 or higher.

#### Software Requirement:
- **Operating System:** Linux/Windows 10/11.
- **Development Tool:** Visual Studio Code.
- **Core Languages:** HTML, CSS, JavaScript, PHP.
- **Local Server:** XAMPP bundle with PHP 8.0+ and MySQL.

### 2.6 Functional Requirements Matrix

The system features and operational rules are detailed in the following matrix matching the university reference layout:

| Functionality | Description | User Type | Input | Output |
| :--- | :--- | :--- | :--- | :--- |
| **User Registration** | Allows new users to sign up with username, email, password, and role. | Customer/Student | Username, Email, Password, Role | Confirmation of account creation. |
| **User Login** | Enables registered users to log in. | Customer/Student | Username, Password | Access to student portal dashboard. |
| **Admin Login** | Allows admin to securely access the admin panel. | Admin | Username, Password | Access to admin control dashboard. |
| **Timetable Config** | Assigns subjects, classrooms, and teachers to schedules. | Admin | Course, Semester, Subject, Room, Time | Updated timetable view on dashboards. |
| **Attendance Capture** | Records class participation on a selected date. | Teacher | Subject, Date, Status (Present/Absent) | Saved records in database; updated student counters. |
| **Marks Registry** | Inputs evaluation scores for exams and assessments. | Teacher | Exam ID, Student ID, Subject, Marks | Results displayed on student dashboards. |
| **Fee Collection** | Records fee payments and logs transactions. | Admin | Student ID, Amount, Receipt Number | Receipt generated; student balance updated. |
| **Document Upload** | Submits student identity and PUC certificates. | Student | Student ID, Doc Type, File Path | File stored on server; status marked as pending. |
| **Grievance Entry** | Logs complaints or requests to administrative staff. | Student/Parent | User ID, Subject, Complaint Text | Grievance ID; complaint logged in admin inbox. |
| **Leave Approval** | Approves/rejects leave requests submitted by faculty. | Admin | Leave ID, Action (Approved/Rejected) | Updates request status; notifies teacher. |
| **Settings Adjust** | Modifies application-wide configurations (e.g. logo, academic year). | Admin | College Name, Contact info, Logo | Settings updated globally on all pages. |
| **Logout** | Allows users/admins to securely log out. | All Roles | Click Logout button | Session destroyed; redirected to login. |

\pagebreak

# CHAPTER 3: SYSTEM LOGICAL FLOW & ARCHITECTURE

***

### 3.1 System Design and Architectural Narrative
System design is the process of defining elements of a system like modules, architecture, components, and their interfaces and data for a system based on the specified requirements. It is the process of defining, developing, and designing systems that satisfy the specific needs and requirements of an educational organization. VidyaSetu ERP uses a classic three-tier system architecture designed to keep user interfaces, database operations, and application logic separate.

```
+-------------------------------------------------------+
|                   1. Presentation Tier                |
|      (HTML5 Document Elements + Tailwind CSS Styles)  |
+---------------------------+---------------------------+
                            | Form Submissions / HTTP requests
                            v
+-------------------------------------------------------+
|                    2. Logic Tier                      |
|           (PHP Session Controllers & Middleware)      |
+---------------------------+---------------------------+
                            | SQL Prepared Statements
                            v
+-------------------------------------------------------+
|                    3. Storage Tier                    |
|             (MySQL Relational Database Engine)        |
+-------------------------------------------------------+
```

### 3.2 Context Flow Diagram (CFD - Level 0 DFD)
The Context Flow Diagram treats the entire system as a single process and lists all the major inputs and outputs:

```
                            +--------------------------+
                            |      Administrators      |
                            +----+---------------+-----+
                                 |               ^
                 Admin Setup /   |               | System Metrics,
                 User Profiles   |               | Audit Trails
                                 v               |
  +------------------+      +----+---------------+-----+      +-------------------+
  |  Students /      |=====>|                          |<====|   Faculty /       |
  |  Parents         |<=====|    VidyaSetu College     |====>|   Teachers        |
  +------------------+      |       Management         |      +-------------------+
    - Submit Files          |    System ERP Engine     |        - Grade Inputs
    - Pay Dues              |                          |        - Attendance Logs
    - Grievances            +------------+-------------+        - Materials Posted
                                         |
                                         | Read/Write
                                         | Transactions
                                         v
                            +------------+-------------+
                            | MySQL Relational Database|
                            +--------------------------+
```

### 3.3 Data Flow Diagram (DFD - Level 1 DFD)
The Level 1 DFD decomposes the system into core operational processes:

```
                  +----------------------------------------------+
                  |         1.0 USER LOGIN PROCESSING            |
                  | Inputs: Username, Password, CSRF token       |
                  +----------------------+-----------------------+
                                         |
                                         | Query User Record
                                         v
                                  [ users Table ]
                                         |
                                         | Session Established
                                         v
                  +----------------------------------------------+
                  |         2.0 DASHBOARD ROUTING ENGINE         |
                  | Checks $_SESSION['role'] & redirects user    |
                  +----------+-------------------+---------------+
                             |                   |
               If role='teacher'                 If role='student'
                             v                   v
+------------------------------+       +------------------------------+
|   3.0 ATTENDANCE REGISTRY    |       |    4.0 STUDENT VIEWS         |
| Records student attendance   |       | Accesses grades, attendance, |
| for selected date & subject  |       | library, and payment histories|
+--------------+---------------+       +--------------+---------------+
               |                                      |
               v                                      v
       [ attendance Table ]                    [ marks / fee Tables ]
```

### 3.4 Data Flow Diagram (DFD - Level 2 Attendance DFD)
The Level 2 DFD decomposes process 3.0 (Attendance Registry) to show internal details:

```
  Teacher Dashboard
         |
         | selects Subject & Date
         v
+--------+---------------------------------------+
|  3.1 LOAD STUDENTS LIST                        |
|  - Queries database for active course/sem      |
|  - Retrieves students list                     |
+--------+---------------------------------------+
         |
         | fetches list of users
         v
  [ students / users Tables ]
         |
         | returns list
         v
+--------+---------------------------------------+
|  3.2 RENDER ATTENDANCE GRID FORM               |
|  - Renders student list with checkboxes        |
|  - Generates CSRF and Date parameters          |
+--------+---------------------------------------+
         |
         | submits form inputs (POST)
         v
+--------+---------------------------------------+
|  3.3 VALIDATE AND SANITIZE TRANSACTION         |
|  - Verifies CSRF Token                         |
|  - Verifies Teacher session ownership          |
+--------+---------------------------------------+
         |
         | verified write instruction
         v
+--------+---------------------------------------+
|  3.4 PERSIST REGISTRY                          |
|  - Executes INSERT ... ON DUPLICATE KEY UPDATE|
+--------+---------------------------------------+
         |
         | database write query
         v
  [ attendance Table ]
```

\pagebreak

# CHAPTER 4: PHYSICAL DATABASE DESIGN

***

### 4.1 RDBMS Context and Integrity Constraints
The database design is a critical component of the project, providing a structured framework for storing, managing, and retrieving data efficiently. It outlines the logical and physical structure of the database, including tables, relationships, keys, and constraints. A well-designed database ensures data integrity, supports system functionality, and enhances performance.

- **Referential Integrity Constraints:** The database maps entity relationships using foreign keys. For example, deleting a department automatically triggers cascading updates or deletes for related courses and subjects, maintaining data consistency.
- **Query Optimization:** Primary and foreign keys are indexed by default, reducing lookup times during JOIN operations.
- **Transaction Safety:** Data insertion steps use `START TRANSACTION` and `COMMIT` blocks to prevent partial writes.

### 4.2 Physical Table Definitions
The core database is named `cms_db`. The table structures are defined below:

#### 1. `users` Table
Stores all account details, credentials, and access roles.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `username` | varchar(50) | NO | UNI | *NULL* | |
| 3 | `email` | varchar(100) | NO | UNI | *NULL* | |
| 4 | `password` | varchar(255) | NO | | *NULL* | |
| 5 | `role` | enum('admin','student','teacher','librarian','accountant','staff','parent') | NO | | *NULL* | |
| 6 | `full_name` | varchar(100) | NO | | *NULL* | |
| 7 | `profile_pic` | varchar(255) | YES | | 'default_profile.svg' | |
| 8 | `phone` | varchar(15) | YES | | *NULL* | |
| 9 | `address` | text | YES | | *NULL* | |
| 10 | `status` | enum('active','inactive') | YES | | 'active' | |
| 11 | `created_at` | timestamp | YES | | CURRENT_TIMESTAMP | |

#### 2. `students` Table
Stores profile details specific to students, linked to their primary user accounts.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `user_id` | int(11) | NO | PK, FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 2 | `roll_no` | varchar(20) | NO | UNI | *NULL* | |
| 3 | `course_id` | int(11) | NO | FK | *NULL* | References `courses.id` |
| 4 | `semester` | int(11) | NO | | *NULL* | |
| 5 | `admission_date` | date | NO | | *NULL* | |
| 6 | `dob` | date | YES | | *NULL* | |
| 7 | `gender` | enum('Male','Female','Other') | YES | | *NULL* | |
| 8 | `phone` | varchar(15) | YES | | *NULL* | |
| 9 | `address` | text | YES | | *NULL* | |
| 10 | `parent_name` | varchar(100) | YES | | *NULL* | |
| 11 | `parent_phone` | varchar(15) | YES | | *NULL* | |

#### 3. `teachers` Table
Stores qualification and department data for faculty members.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `user_id` | int(11) | NO | PK, FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 2 | `employee_id` | varchar(20) | NO | UNI | *NULL* | |
| 3 | `dept_id` | int(11) | NO | FK | *NULL* | References `departments.id` |
| 4 | `designation` | varchar(100) | YES | | *NULL* | |
| 5 | `qualification` | varchar(255) | YES | | *NULL* | |
| 6 | `experience` | varchar(50) | YES | | *NULL* | |
| 7 | `joining_date` | date | NO | | *NULL* | |

#### 4. `departments` Table
Defines departmental units within the college.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `name` | varchar(100) | NO | | *NULL* | |
| 3 | `description` | text | YES | | *NULL* | |
| 4 | `hod_id` | int(11) | YES | FK | *NULL* | References `teachers.user_id` (ON DELETE SET NULL) |
| 5 | `created_at` | timestamp | YES | | CURRENT_TIMESTAMP | |

#### 5. `courses` Table
Defines program pathways and durations.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `dept_id` | int(11) | NO | FK | *NULL* | References `departments.id` (ON DELETE CASCADE) |
| 3 | `name` | varchar(100) | NO | | *NULL* | |
| 4 | `duration` | varchar(50) | NO | | *NULL* | |
| 5 | `description` | text | YES | | *NULL* | |

#### 6. `subjects` Table
Stores subjects mapped to courses and semesters.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `course_id` | int(11) | NO | FK | *NULL* | References `courses.id` (ON DELETE CASCADE) |
| 3 | `name` | varchar(100) | NO | | *NULL* | |
| 4 | `code` | varchar(20) | NO | | *NULL* | |
| 5 | `semester` | int(11) | NO | | *NULL* | |

#### 7. `attendance` Table
Tracks daily student class participation.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `student_id` | int(11) | NO | FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 3 | `subject_id` | int(11) | NO | FK | *NULL* | References `subjects.id` (ON DELETE CASCADE) |
| 4 | `date` | date | NO | | *NULL* | |
| 5 | `status` | enum('Present','Absent','Late','Leave') | NO | | *NULL* | |
| 6 | `marked_by` | int(11) | NO | FK | *NULL* | References `users.id` |
| 7 | `created_at` | timestamp | YES | | CURRENT_TIMESTAMP | |

#### 8. `exams` Table
Stores examination parameters.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `name` | varchar(100) | NO | | *NULL* | |
| 3 | `course_id` | int(11) | NO | FK | *NULL* | References `courses.id` (ON DELETE CASCADE) |
| 4 | `semester` | int(11) | NO | | *NULL* | |
| 5 | `exam_date` | date | NO | | *NULL* | |
| 6 | `type` | enum('Internal','Semester','Practical') | NO | | *NULL* | |

#### 9. `marks` Table
Logs student test scores.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `exam_id` | int(11) | NO | FK | *NULL* | References `exams.id` (ON DELETE CASCADE) |
| 3 | `student_id` | int(11) | NO | FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 4 | `subject_id` | int(11) | NO | FK | *NULL* | References `subjects.id` (ON DELETE CASCADE) |
| 5 | `marks_obtained` | float | NO | | *NULL* | |
| 6 | `max_marks` | float | NO | | *NULL* | |
| 7 | `grade` | varchar(5) | YES | | *NULL* | |

#### 10. `fees_structure` Table
Defines semester tuition and fee targets.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `course_id` | int(11) | NO | FK | *NULL* | References `courses.id` (ON DELETE CASCADE) |
| 3 | `semester` | int(11) | NO | | *NULL* | |
| 4 | `amount` | decimal(10,2) | NO | | *NULL* | |
| 5 | `title` | varchar(100) | NO | | *NULL* | |

#### 11. `fee_payments` Table
Records fee payments and transactions.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `student_id` | int(11) | NO | FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 3 | `amount` | decimal(10,2) | NO | | *NULL* | |
| 4 | `payment_date` | date | NO | | *NULL* | |
| 5 | `receipt_no` | varchar(50) | NO | UNI | *NULL* | |
| 6 | `status` | enum('Paid','Partial','Unpaid') | YES | | 'Paid' | |
| 7 | `payment_method` | varchar(50) | YES | | *NULL* | |
| 8 | `created_at` | timestamp | YES | | CURRENT_TIMESTAMP | |

#### 12. `student_documents` Table
Logs submitted student identity and academic documents.
*Engine: InnoDB, Collation: utf8mb4_general_ci*

| Field No | Column Name | Data Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `id` | int(11) | NO | PK | *NULL* | AUTO_INCREMENT |
| 2 | `student_id` | int(11) | NO | FK | *NULL* | References `users.id` (ON DELETE CASCADE) |
| 3 | `document_type` | enum('aadhaar','sslc','puc','tc','photo','other') | NO | | *NULL* | |
| 4 | `file_path` | varchar(255) | NO | | *NULL* | |
| 5 | `verification_status` | enum('pending','verified','rejected') | YES | | 'pending' | |
| 6 | `admin_remarks` | text | YES | | *NULL* | |
| 7 | `uploaded_at` | timestamp | YES | | CURRENT_TIMESTAMP | |

### 4.3 Entity-Relationship (ER) Diagram Symbols Mapping
To align with Mangalore University evaluation standards, the symbols used in our Entity-Relationship (ER) layout are documented below:

| Symbol Shape | Representation Meaning in Schema |
| :--- | :--- |
| **Rectangle** | Strong Entity (e.g., `users`, `courses`) |
| **Double Rectangle** | Weak Entity (e.g., `students`, `teachers` depending on `users`) |
| **Diamond** | Relationship (e.g., `marks` relation connecting exams and students) |
| **Double Diamond** | Identifying Relationship for weak entities |
| **Oval** | Attribute |
| **Oval with Underlined Text** | Key Attribute (Primary Identifier) |
| **Double Oval** | Multivalued Attribute (e.g., contacts, phone numbers) |
| **Dashed Oval** | Derived Attribute |
| **1 : N** | Cardinality ratio mapping (One-to-Many connection) |

### 4.4 ER Diagram Schema Layout
The database schema maps user entities to academic portals:

- **Users (Strong Entity)** -> has attributes: `id` (Key), `username`, `email`, `password`, `role`.
- **Students (Weak Entity)** -> links to Users with `user_id` -> has attributes: `roll_no` (Key), `semester`, `admission_date`.
- **Courses (Strong Entity)** -> has attributes: `id` (Key), `name`, `duration`.
- **Relationship Matrix:**
  - Users (admin) -> **manages** -> Students (1 : N)
  - Students -> **assigned to** -> Courses (N : 1)
  - Teachers -> **mark** -> Attendance (1 : N)
  - Students -> **make** -> Fee Payments (1 : N)

\pagebreak

# CHAPTER 5: SOURCE CODE IMPLEMENTATION LISTING

***

### 5.1 Database Connection Configuration (`includes/db.php`)
This file configures the connection between the application and the MySQL database. It uses the PHP Data Objects (PDO) extension, establishes the connection parameters, sets execution properties, and handles exceptions if connection errors occur.

```php
<?php
// Define database host and credential parameters
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cms_db');

try {
    // Initialize PDO connection string with charset validation
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    
    // Set execution properties to throw exceptions on database errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configure default fetch mode to return associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Terminate script execution and display connection error message
    die("Database connection failed: " . $e->getMessage());
}
?>
```

### 5.2 Core Functions and Security Filters (`includes/functions.php`)
This file provides key security features, input validation rules, and authentication helpers for the application.

```php
<?php
// Initialize session tracking
session_start();

/**
 * Generate a cryptographically secure CSRF token.
 * Stores the token in the session array if not already present.
 * @return string
 */
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify if the submitted CSRF token matches the session token.
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Enforce CSRF protection for POST requests.
 */
function csrf_guard()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            die("CSRF validation failed. Possible cross-site request forgery.");
        }
    }
}

// Define the root URL path for routing
if (!defined('BASE_URL')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('\\', '/', dirname(dirname($script_name)));
    define('BASE_URL', rtrim($base_dir, '/') . '/');
}

/**
 * Check if a user session is active.
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if the active user session matches a required role.
 * @param string $role
 * @return bool
 */
function has_role($role)
{
    return is_logged_in() && $_SESSION['role'] === $role;
}

/**
 * Redirect logged-in users to their respective dashboards based on their role.
 */
function redirect_if_logged_in()
{
    if (is_logged_in()) {
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'student':
                header("Location: student/dashboard.php");
                break;
            case 'teacher':
                header("Location: teacher/dashboard.php");
                break;
            case 'parent':
                header("Location: parent/dashboard.php");
                break;
            case 'librarian':
            case 'staff':
            case 'accountant':
                header("Location: admin/dashboard.php");
                break;
            default:
                header("Location: login.php");
                break;
        }
        exit();
    }
}

/**
 * Require active authentication and verify access permissions.
 * Redirects to the login portal if criteria are not met.
 * @param string|null $role
 */
function require_login($role = null)
{
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }

    if ($role && $_SESSION['role'] !== $role) {
        set_flash_message('error', 'Access Denied: You do not have permission to view this page.');
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

/**
 * Sanitize input values to prevent Cross-Site Scripting (XSS) injections.
 * @param string $input
 * @return string
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Build absolute routing paths for assets.
 * @param string $path
 * @return string
 */
function asset($path)
{
    return BASE_URL . "assets/" . ltrim($path, '/');
}

/**
 * Set a session flash message.
 * @param string $type
 * @param string $message
 */
function set_flash_message($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display the session flash message if set.
 */
function display_flash_message()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = $flash['type'];
        $message = $flash['message'];
        $bg = $type === 'success' ? 'bg-emerald-50 border-emerald-500/20 text-emerald-600' : 'bg-rose-50 border-rose-500/20 text-rose-600';
        $icon = $type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';

        echo "<div class='flex items-center p-4 mb-6 rounded-2xl border {$bg}' role='alert'>
                <i class='fas {$icon} mr-3 text-lg'></i>
                <span class='text-xs font-bold uppercase tracking-widest'>{$message}</span>
              </div>";
    }
}

/**
 * Fetch system configuration parameters from the settings table.
 * @param PDO $pdo
 * @return array
 */
function get_college_settings($pdo)
{
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    return $stmt->fetch();
}

/**
 * Authenticate login credentials against the database.
 * Matches input against username or email and validates hashes.
 * @param PDO $pdo
 * @param string $username
 * @param string $password
 * @return bool
 */
function authenticate($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_pic'] = $user['profile_pic'];
            return true;
        }
    }
    return false;
}
?>
```

### 5.3 Authentication Gateway Control (`login.php`)
This script manages the primary login interface. It handles POST requests, validates credentials against the database, sets session variables, and redirects authenticated users to their dashboards.

```php
<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect user to their dashboard if already authenticated
redirect_if_logged_in();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input values
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Verify token validity
    if (!verify_csrf_token($csrf_token)) {
        $error_message = "CSRF validation failed. Please try again.";
    } elseif (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Authenticate credentials against database values
        if (authenticate($pdo, $username, $password)) {
            if ($remember) {
                setcookie('remember_user', base64_encode($username), time() + (86400 * 30), "/");
            } else {
                setcookie('remember_user', '', time() - 3600, "/");
            }
            redirect_if_logged_in();
        } else {
            $error_message = "Invalid username/email or password.";
        }
    }
}

// Pre-fill username if remember cookie is set
$saved_username = isset($_COOKIE['remember_user']) ? base64_decode($_COOKIE['remember_user']) : '';

// Retrieve system settings
$settings = get_college_settings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo $settings['college_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .login-bg {
            background: radial-gradient(1200px 600px at 15% 20%, rgba(99, 102, 241, 0.18), transparent 55%),
                        radial-gradient(900px 500px at 85% 30%, rgba(244, 63, 94, 0.12), transparent 55%),
                        linear-gradient(180deg, #f8fafc 0%, #eef2ff 60%, #f8fafc 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(148, 163, 184, 0.25);
        }
        .glow-indigo {
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10), 0 8px 22px rgba(79, 70, 229, 0.10);
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        .input-focus { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .input-focus:focus {
            background: #ffffff;
            border-color: rgba(79, 70, 229, 0.55);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-login:hover {
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4), 0 0 60px rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4 relative text-slate-800 antialiased">

    <div class="flex w-full max-w-6xl relative z-10">
        <!-- Left Banner Column -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-16 relative">
            <div class="absolute top-20 right-20 w-20 h-20 glass-card rounded-3xl flex items-center justify-center float-animation">
                <i class="fas fa-graduation-cap text-indigo-600 text-2xl"></i>
            </div>
            <div>
                <div class="flex items-center space-x-4 mb-20">
                    <div class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl italic shadow-2xl">V</div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight italic">VidyaSetu</h1>
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em]">College Management System</p>
                    </div>
                </div>
                <h2 class="text-5xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8">
                    The Bridge <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">Between Learning</span><br>
                    & Excellence.
                </h2>
                <p class="text-slate-600 text-lg font-medium leading-relaxed max-w-md">
                    A unified platform to manage academic operations, student performance, and institutional workflow.
                </p>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-bold">© <?php echo date('Y'); ?> <?php echo $settings['college_name']; ?></p>
            </div>
        </div>

        <!-- Right Login Form Column -->
        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <div class="glass-card rounded-[2.5rem] p-10 md:p-12 w-full max-w-md glow-indigo relative overflow-hidden">
                <div class="relative z-10">
                    <div class="mb-12">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-3">Authentication Portal</p>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                        <p class="text-slate-600 font-medium text-sm">Sign in to access your dashboard</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-5 rounded-2xl mb-8 flex items-center">
                            <i class="fas fa-exclamation-circle text-lg mr-4"></i>
                            <p class="text-sm font-bold"><?php echo $error_message; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="space-y-7">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3" for="username">Username or Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fas fa-user text-sm"></i>
                                </span>
                                <input type="text" id="username" name="username" class="input-focus w-full !pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none font-bold text-sm" placeholder="Enter your username" value="<?php echo $saved_username; ?>" required>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest" for="password">Password</label>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input type="password" id="password" name="password" class="input-focus w-full !pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none font-bold text-sm" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded-md bg-white border-slate-300 text-indigo-600 cursor-pointer">
                                <span class="ml-3 text-xs text-slate-600 font-bold group-hover:text-slate-700 transition-colors">Remember session</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-login w-full py-5 text-white rounded-2xl font-black text-xs tracking-[0.2em] uppercase flex items-center justify-center space-x-3">
                            <span>Authorize Access</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

### 5.4 Student Registration Gateway (`signup.php`)
This script manages student signups, verifying that student creation does not assign administrative roles.

```php
<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect user to dashboard if already logged in
redirect_if_logged_in();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $role = $_POST['role'] ?? 'student';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Enforce role security boundaries
    if ($role === 'admin' || $role === 'teacher') {
        die("Administrative registration is not allowed through this channel.");
    }

    if (!verify_csrf_token($csrf_token)) {
        $error_message = "CSRF validation failed. Please try again.";
    } elseif (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        try {
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error_message = "Username or Email is already registered.";
            } else {
                // Insert new student user record
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, status) VALUES (?, ?, ?, 'student', ?, 'active')");
                $stmt->execute([$username, $email, $hashed_password, $full_name]);
                
                $success_message = "Signup successful! You can now log in.";
            }
        } catch (PDOException $e) {
            $error_message = "Signup transaction failed: " . $e->getMessage();
        }
    }
}
?>
```

### 5.5 Executive Admin Dashboard Controller (`admin/dashboard.php`)
This controller aggregates operational metrics, attendance data, recent enrollments, notices, and financial statistics into a unified dashboard view.

```php
<?php
$page_title = "Overview Dashboard";
require_once 'includes/header.php';

// Enforce access control permissions
require_login('admin');

// Fetch database metrics
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_departments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

// Retrieve recent student enrollments
$recent_students = $pdo->query("SELECT u.full_name, u.created_at, s.roll_no, c.name as course_name 
                                FROM students s 
                                JOIN users u ON s.user_id = u.id 
                                JOIN courses c ON s.course_id = c.id 
                                ORDER BY u.created_at DESC LIMIT 5")->fetchAll();

// Retrieve active system notices
$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Calculate monthly attendance data for the line chart
$attendance_data = ['labels' => [], 'data' => []];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_label = date('M', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status IN ('Present', 'Late') THEN 1 ELSE 0 END) as present
        FROM attendance WHERE DATE_FORMAT(date, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $res = $stmt->fetch();
    $attendance_data['labels'][] = $month_label;
    $attendance_data['data'][] = $res['total'] > 0 ? round(($res['present'] / $res['total']) * 100, 1) : 0;
}

// Calculate gender distribution metrics
$gender_counts = $pdo->query("SELECT gender, COUNT(*) as count FROM students GROUP BY gender")->fetchAll(PDO::FETCH_KEY_PAIR);
$male_count = $gender_counts['Male'] ?? 0;
$female_count = $gender_counts['Female'] ?? 0;
$total_gender = $male_count + $female_count;

$today = date('l, F j, Y');

// Calculate financial transaction totals
$total_collected = $pdo->query("SELECT SUM(amount) FROM fee_payments")->fetchColumn() ?: 0;
$pending_dues = 0;
try {
    $rows = $pdo->query("
        SELECT st.user_id,
               COALESCE(SUM(fs.amount), 0) AS expected_amount,
               COALESCE((SELECT SUM(fp.amount) FROM fee_payments fp WHERE fp.student_id = st.user_id AND fp.status IN ('Paid','Partial')), 0) AS paid_amount
        FROM students st
        LEFT JOIN fees_structure fs ON fs.course_id = st.course_id AND fs.semester = st.semester
        GROUP BY st.user_id
    ")->fetchAll();
    foreach ($rows as $r) {
        $pending_dues += max(0, (float) $r['expected_amount'] - (float) $r['paid_amount']);
    }
} catch (PDOException $e) {
    // Fallback if schema does not resolve
}
$total_potential = $total_collected + $pending_dues;
$target_percent = $total_potential > 0 ? round(($total_collected / $total_potential) * 100) : 0;
$pending_percent = 100 - $target_percent;
?>

<!-- UI Overview Panel -->
<div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 rounded-xl p-6 mb-5 overflow-hidden text-white">
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
        <div>
            <div class="flex items-center space-x-1.5 mb-2">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-medium text-slate-400 tracking-wide"><?php echo $today; ?></span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight mb-1">
                System Administration Overview
            </h1>
            <p class="text-slate-400 text-xs">
                Real-time operational dashboard for departments, academic records, and financials.
            </p>
        </div>
    </div>
</div>

<!-- Operational Statistics Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl p-4 shadow border border-slate-100">
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Students</p>
        <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_students; ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow border border-slate-100">
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Faculty</p>
        <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_teachers; ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow border border-slate-100">
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Departments</p>
        <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_departments; ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow border border-slate-100">
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Programs</p>
        <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_courses; ?></h3>
    </div>
</div>

<!-- Detailed Data Layout Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Recent Enrollments Table -->
    <div class="bg-white rounded-xl p-5 shadow border border-slate-100">
        <h4 class="text-sm font-bold text-slate-800 mb-3">Recent Onboardings</h4>
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[9px] font-bold text-slate-400 uppercase border-b pb-2">
                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Program</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_students as $student): ?>
                    <tr class="border-b py-2 hover:bg-slate-50">
                        <td class="py-2"><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                        <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Financial Collection Progress -->
    <div class="bg-white rounded-xl p-5 shadow border border-slate-100">
        <h4 class="text-sm font-bold text-slate-800 mb-3">Financial Performance</h4>
        <div class="mb-4">
            <p class="text-[10px] font-medium text-slate-400 uppercase">Total Fees Collected</p>
            <div class="text-xl font-bold text-slate-800">₹ <?php echo number_format($total_collected, 2); ?></div>
        </div>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Collected Percentage</span>
                    <span><?php echo $target_percent; ?>%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-indigo-600 h-full" style="width: <?php echo $target_percent; ?>%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span>Outstanding Balance Percentage</span>
                    <span><?php echo $pending_percent; ?>%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-rose-500 h-full" style="width: <?php echo $pending_percent; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
```

### 5.6 Student Enrollment Processing Snippet (`admin/students-add.php`)
This script processes the enrollment of new student users. It runs transaction blocks to ensure both the user and student tables are updated atomically.

```php
<?php
// Enforce admin privileges and check session state
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user profile entries
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = sanitize($_POST['full_name']);
    $roll_no = sanitize($_POST['roll_no']);
    $course_id = (int)$_POST['course_id'];
    $semester = (int)$_POST['semester'];
    $admission_date = sanitize($_POST['admission_date']);

    try {
        // Start database transaction
        $pdo->beginTransaction();

        // 1. Insert credentials into user ledger
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, 'student', ?)");
        $stmt->execute([$username, $email, $password, $full_name]);
        $user_id = $pdo->lastInsertId();

        // 2. Insert student properties linked to user_id
        $stmt = $pdo->prepare("INSERT INTO students (user_id, roll_no, course_id, semester, admission_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $roll_no, $course_id, $semester, $admission_date]);

        // Commit transaction to database
        $pdo->commit();
        set_flash_message('success', 'Student profile onboarded successfully.');
        header("Location: students-list.php");
        exit();

    } catch (PDOException $e) {
        // Rollback transaction on failure to prevent partial writes
        $pdo->rollBack();
        $error_message = "Enrollment transaction failed: " . $e->getMessage();
    }
}
?>
```

\pagebreak

# CHAPTER 6: USER INTERFACE (UI) WIREFRAMES

***

### 6.1 Client Welcome Gateway (Login View)
The primary authentication screen uses a responsive glassmorphic layout styled with Tailwind CSS. The interface is organized as follows:

```
+-------------------------------------------------------------------------------+
|  V  VidyaSetu | College Management System               System Online [o]     |
|  ---------------------------------------------------------------------------  |
|                                                                               |
|      The Bridge Between                     +--------------------------+      |
|      Learning & Excellence                  |  Authentication Portal   |      |
|                                             |  Welcome Back            |      |
|      A unified platform to manage           |  Sign in to continue     |      |
|      academic operations, student           |                          |      |
|      performance, and workflows.            |  [ Username or Email ]   |      |
|                                             |  [ Password          ]   |      |
|                                             |                          |      |
|      Features:                              |  [ ] Remember Session    |      |
|      * Role-Based Access Controls           |                          |      |
|      * Real-Time Analytics                  |  [   AUTHORIZE ACCESS   ]  |      |
|      * Academic Scheduling                  +--------------------------+      |
|                                                                               |
|  ---------------------------------------------------------------------------  |
|  (C) 2026 Canara College, Mangaluru               Support: admin@vidyasetu.com |
+-------------------------------------------------------------------------------+
```

### 6.2 Master Administrative Control Form
The administrative workspace uses a vertical navigation layout combined with a dynamic telemetry display grid:

```
+-------------------------------------------------------------------------------+
| [V] VidyaSetu ERP   |  Overview Dashboard                  Admin | Profile [V] |
+---------------------+---------------------------------------------------------+
| [@] Dashboard       |  +----------------------------------------------------+ |
| [#] User Accounts   |  | Good Evening, Admin                                | |
| [$] Departments     |  | Operational telemetry status logs:                 | |
| [%] Courses         |  +----------------------------------------------------+ |
| [&] Subjects        |  | Students: 124  | Faculty: 18  | Departments: 4     | |
| [*] Timetable       |  +----------------------------------------------------+ |
| [!] Fee Structures  |  | Recent Onboardings      | Financial Status Log     | |
| [?] Settings        |  | * Arjun Reddy (BCA)     | * Target achieved: 78%   | |
|                     |  | * Sneha Kapoor (BBA)    | * Collected: INR 5.4M    | |
| [<-] Logout         |  +-------------------------+--------------------------+ |
+---------------------+---------------------------------------------------------+
```

### 6.3 Faculty Portal Attendance Tracker
The faculty portal attendance sheet displays class details, active lists, and toggle status inputs:

```
+-------------------------------------------------------------------------------+
| [V] Faculty Portal  |  Subject Attendance Registry        Teacher | Profile [V]|
+---------------------+---------------------------------------------------------+
| [ Class Information ] Course: BCA  |  Subject: Database Systems  | Semester: VI |
|                       Date: [ 2026-05-26 ]                                    |
| ----------------------------------------------------------------------------- |
| Roll No   | Student Name         | Status Selections                          |
| --------- +----------------------+--------------------------------------------|
| 23BCA0001 | Arjun Reddy          | [X] Present  [ ] Absent  [ ] Late  [ ] Leave|
| 23BCA0045 | Pavan Serigara       | [X] Present  [ ] Absent  [ ] Late  [ ] Leave|
| 23BCA0088 | Sneha Kapoor         | [ ] Present  [X] Absent  [ ] Late  [ ] Leave|
| 23BCA0112 | Rahul Verma          | [X] Present  [ ] Absent  [ ] Late  [ ] Leave|
| ----------------------------------------------------------------------------- |
|                                                    [  SAVE ATTENDANCE LOG  ]  |
+-------------------------------------------------------------------------------+
```

### 6.4 Student Portal Dashboard Grid
The student dashboard provides read-only tracking metrics, library alerts, and outstanding balance logs:

```
+-------------------------------------------------------------------------------+
| [V] Student Portal  |  Academic Performance Center        Student | Profile [V]|
+---------------------+---------------------------------------------------------+
|  Overview Stats:                                                              |
|  * Course: BCA VI Sem | Attendance: 89.2% (OK) | Graded Performance: A+ (Excellent)|
| ----------------------------------------------------------------------------- |
|  [ Current Course Schedule ]             [ Library Operations Alert ]         |
|  09:30 - PHP Programming (Room 102)     * Issued: Advanced RDBMS Concepts    |
|  11:00 - Database Systems (Lab 3)       * Return Date: 2026-05-30            |
|  14:00 - Web Tech Practicals            * Fines Pending: INR 0.00            |
| ----------------------------------------+------------------------------------ |
|  [ Outstanding Invoices ]               [ Assignment Upload Desk ]            |
|  * Tuition Fees: INR 25,000 [ PAID ]     * DB Schema normalization exercise   |
|  * Exams Fees: INR 1,500   [ PAID ]     * Deadline: 2026-05-28   [ UPLOAD ]  |
+-------------------------------------------------------------------------------+
```

\pagebreak

# CHAPTER 7: COMPREHENSIVE QUALITY ASSURANCE & TESTING

***

### 7.1 Testing Introduction & Methodologies
Testing is the phase where errors from the earlier phases also must be tested. It is the process of examining something with the intention of finding errors. Testing a program consists of providing it with a set of test inputs and observing if it behaves as expected. If the program fails, then the conditions under which the failure occurs are noted for debugging and correction.

The verification pipeline utilizes two main methodologies:
- **White Box Testing (Structural and Code-Level Verification):** Checks internal code structures, logic paths, and connection configurations. This includes validating that the connection file (`db.php`) handles connection losses, the input filter (`sanitize()`) strips script tags to prevent XSS, and all database interactions use parameterized queries to block SQL injections.
- **Black Box Testing (Functional and Interface-Level Verification):** Evaluates user interfaces and system outputs without looking at the underlying code. Testers input form parameters on screens like the login panel, registration forms, and attendance sheets, and verify that appropriate error messages, database updates, or page redirects occur.

### 7.2 Unit Testing Reports
Testing at the unit level is essential because the error isolation is accurate. Unit testing involves testing different units or modules of a system in isolation to check whether each module works properly and yields the desired outputs for the given inputs.

#### 1. Login Page Unit Test Cases:

| Testcase ID | Test Description | Expected Result | Actual Result | Remarks |
| :--- | :--- | :--- | :--- | :--- |
| **TC-LOG-01** | Correct admin credentials. | Redirect to `admin/dashboard.php`. | Redirected successfully. | **OK** |
| **TC-LOG-02** | Incorrect admin password. | Display message: "Invalid username/email or password." | Error displayed on modal. | **OK** |
| **TC-LOG-03** | Valid user credentials from database. | Redirect to appropriate user portal page. | Redirected to dashboard. | **OK** |
| **TC-LOG-04** | Invalid user password. | Display error message. | Error handled on UI. | **OK** |
| **TC-LOG-05** | Empty username field. | Display message: "Please fill in all fields." | Execution blocked. | **OK** |
| **TC-LOG-06** | Empty password field. | Display message: "Please fill in all fields." | Execution blocked. | **OK** |
| **TC-LOG-07** | SQL injection attempt in username. | Reject login, escape input, and show invalid login message. | Sanitized successfully. | **OK** |
| **TC-LOG-08** | Role mismatch (Student attempting admin access). | Block access, redirect to login page with "Access Denied." | Redirected successfully. | **OK** |

#### 2. Signup Page Unit Test Cases:

| Testcase ID | Test Description | Expected Result | Actual Result | Remarks |
| :--- | :--- | :--- | :--- | :--- |
| **TC-SIGN-01**| Valid student registration. | Message: "Signup successful! You can now log in." | Record saved in db; link shown. | **OK** |
| **TC-SIGN-02**| Admin role selected during signup. | Reject registration with "Administrative registration not allowed." | Registration rejected. | **OK** |
| **TC-SIGN-03**| Empty username field. | Display error or prevent form submission. | Blocked by UI validators. | **OK** |
| **TC-SIGN-04**| Empty password field. | Display error or prevent form submission. | Blocked by UI validators. | **OK** |
| **TC-SIGN-05**| SQL injection check in name fields. | Input escaped; record inserted with text value. | Input sanitized. | **OK** |
| **TC-SIGN-06**| Duplicate username. | Show error message: "Username or Email already registered." | Duplicates rejected. | **OK** |
| **TC-SIGN-07**| Password mismatch during sign up. | Show error message: "Passwords do not match." | Halted registration. | **OK** |
| **TC-SIGN-08**| Empty email field. | Prevent submission; display error message. | Form submission blocked. | **OK** |

#### 3. Password Reset Page Unit Test Cases:

| Testcase ID | Test Description | Expected Result | Actual Result | Remarks |
| :--- | :--- | :--- | :--- | :--- |
| **TC-RESET-01**| Fields left empty. | Message: "All fields are required." | Input verified and blocked. | **OK** |
| **TC-RESET-02**| New and confirm passwords do not match. | Message: "New password and confirmation do not match." | Error alert shown. | **OK** |
| **TC-RESET-03**| Incorrect current password value. | Message: "Current password is incorrect." | Error caught. | **OK** |
| **TC-RESET-04**| Valid current password and matching new one. | Message: "Password changed successfully." | Password reset in database. | **OK** |

### 7.3 Integration Testing Reports
In integration testing, all the code modules are put together and tested for the desired outputs. The modules that were unit tested are integrated and verified as a whole. All the processes are combined in this phase. The integration testing is carried out using integrated test plans prepared in the design phase of system development. All errors found in the system are corrected before moving to final staging. The integrated modules for academic marks tracking, fee installment logging, and class timetabling show the expected results.

### 7.4 General Button Test Cases
The behavior of the key dashboard buttons was validated using the following test cases:

#### 1. Register Button:
- **Testcase ID:** `TC-BTN-01`
- **Test Description:** Register Button Submission
- **Expected Result:** Register information is saved in the database if all validations are satisfied, showing a success message; otherwise, show errors.
- **Actual Result:** User registration process completes, showing "Student onboarded successfully."
- **Remarks:** **OK**

#### 2. Login Button:
- **Testcase ID:** `TC-BTN-02`
- **Test Description:** Login Button Action
- **Expected Result:** Logs in the user if all credentials match; otherwise, displays the proper error message.
- **Actual Result:** Authenticated user is logged in and redirected to their home dashboard.
- **Remarks:** **OK**

#### 3. Logout Button:
- **Testcase ID:** `TC-BTN-03`
- **Test Description:** Logout Button Action
- **Expected Result:** User is logged out, the active session is destroyed, and the user is redirected to the login gateway.
- **Actual Result:** Session is cleared and user is redirected to the login page.
- **Remarks:** **OK**

### 7.5 Structural (White Box) and Interface (Black Box) Testing
- **White Box Testing:** Verified structural access boundaries by testing data extraction methods from MySQL via PHP PDO. Verified that database connections fail gracefully without exposing sensitive credentials in stack traces.
- **Black Box Testing:** Checked interface actions, form inputs, validation triggers, session updates, and boundary conditions to verify role-based permissions (Admins, Faculty, Students, Parents).

### 7.6 System Testing Summary
In system testing, the entire system is tested as a whole with all forms, code modules, and database components. After integration testing, the whole system was tested under different load and user scenarios, and performed well without runtime failures or data concurrency issues.

\pagebreak

# CHAPTER 8: CONCLUSIONS, OUTCOMES & FUTURE WORK

***

### 8.1 Project Conclusion & MU Regulation Conformity
The development of the **VidyaSetu College Management System** satisfies the VI Semester Bachelor of Computer Application (BCA) project requirements of Mangalore University. The implementation successfully automates essential college ERP features, including student/admin authentication, attendance registration, internal evaluation records, fee collection logs, and grievance resolution flows. The admin panel allows efficient management of resources and monitoring of user accounts, ensuring smooth operation.

Throughout the project, technologies such as HTML, CSS, PHP, and MySQL were integrated to build a responsive college management portal. The system architecture promotes modularity and security, laying a strong foundation for future institutional scaling.

### 8.2 Proposed Future Enhancements
The architecture of VidyaSetu ERP can be expanded in future versions with the following enhancements:
1. **Automated Payment Gateways:** Integrating commercial payment gateways (such as Razorpay or Stripe) to process actual tuition and fee payments, automatically update transaction ledgers, and email payment receipts to users.
2. **Notification Subsystems:** Integrating SMS and email APIs (such as Twilio or SendGrid) to automatically notify parents about low attendance scores, publish exam schedule releases, and send library fine reminders.
3. **AI Performance Analytics:** Using machine learning algorithms to evaluate student grades and attendance histories, identify at-risk students, and suggest early educational interventions.
4. **Biometric Integration:** Interfacing with physical biometric fingerprint scanners or RFID gates to record student and faculty attendance automatically, reducing manual entry.

### 8.3 Academic Bibliography & Citations
All technical standards and development tools used in this project were implemented using these references (APA Style):

- **PHP Group.** (2025). *PHP Hypertext Preprocessor Documentation Hub*. Retrieved from https://www.php.net/docs.php
- **Oracle Corporation.** (2025). *MySQL Relational Database Engine Reference Manual*. Retrieved from https://dev.mysql.com/doc/
- **W3Schools Online Tutorials.** (2025). *PHP MySQL Database Connection & PDO Reference Guides*. Retrieved from https://www.w3schools.com/php/
- **Open Web Application Security Project (OWASP).** (2025). *Top Ten Web Application Security Risks*. Retrieved from https://owasp.org/www-project-top-ten/
- **YouTube Educators.** (2025). *Web Development with PHP & MySQL Relational Structures*.
- **OpenAI.** (2026). *ChatGPT Language Model Assistance for Architectural Design Queries*.
