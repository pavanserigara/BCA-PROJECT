# Academic Project Report Generation Guide

This guide provides instructions and structural specifications for generating a publication-ready, evaluation-compliant Mini Project Report conforming to university regulations (specifically Mangalore University VI Semester BCA standards). It is designed to guide another AI agent to recreate or scale a similar document of at least 40 pages, incorporating local formatting and structural nuances derived from regional project report templates.

---

## 1. General Formatting & Typography Rules

To compile properly into academic print form, the document must be structured to meet these specifications:
- **Font & Size:** Body text in Times New Roman, 11pt. Chapter headings in 14pt (Bold, uppercase). Section headings in 12pt (Bold).
- **Line Spacing:** Exactly 1.5 lines.
- **Margins:** 
  - Left Margin: 1.5 inches (allowing space for spiral or hardcover binding).
  - Right, Top, and Bottom Margins: 1.0 inch each.
- **Target Deliverable Scope:** The final rendered document must span between 35 and 50 pages (minimum target of 40 pages of highly detailed technical prose).
- **Compliance Rule:** Avoid placeholders (such as `[Insert Name Here]` or `TODO`). Every field must be populated with realistic data (e.g., student register numbers, guide names, real course names, and a real host college).

---

## 2. Document Structure Blueprint

The report must consist of the following sections in exact order:

```
[Document Root]
 ├── Front Matter
 │    ├── Outer Cover / Inner Cover Page
 │    ├── Official Certificate of Completion
 │    ├── Candidate Declaration
 │    ├── Acknowledgement (including principal, guide, HOD, lab admins, peers)
 │    └── Table of Contents
 └── Technical Chapters
      ├── Chapter 1: Introduction & Core Hypothesis
      │    ├── 1.1 Introduction and Overview
      │    ├── 1.2 Title of the Project
      │    ├── 1.3 Scope and Boundaries
      │    ├── 1.4 Objectives of the Platform
      │    ├── 1.5 System Features and User Capabilities
      │    ├── 1.6 Platform Stack Profile (Tools/Platforms Used)
      │    ├── 1.7 Testing Tools Overview
      │    └── 1.8 Expected Outcome
      ├── Chapter 2: Software Requirements Specification (SRS)
      │    ├── 2.1 Purpose of the Specification
      │    ├── 2.2 Scope of the Deliverable
      │    ├── 2.3 User Classes and Characteristics
      │    ├── 2.4 Assumptions and Dependencies
      │    ├── 2.5 Hardware and Software Requirements
      │    └── 2.6 Functional Requirements Matrix (Table columns: Functionality, Description, User Type, Input, Output)
      ├── Chapter 3: System Logical Flow & Architecture (Introduction to System Design, Context Diagram, Level 1 DFD, Level 2 DFD)
      ├── Chapter 4: Physical Database Design
      │    ├── 4.1 RDBMS Context and Integrity Constraints
      │    ├── 4.2 Physical Table Definitions (including all user columns and constraints)
      │    ├── 4.3 Entity-Relationship (ER) Diagram Symbols Mapping (Table showing Shapes and Meanings)
      │    └── 4.4 ER Diagram Schema Layout
      ├── Chapter 5: Source Code Implementation Listing (db connection, functions, login, signup, admin dashboards)
      ├── Chapter 6: User Interface (UI) Wireframes (Login, admin portal, teacher sheet, student dashboard)
      ├── Chapter 7: Comprehensive Quality Assurance & Testing
      │    ├── 7.1 Testing Introduction & Methodologies
      │    ├── 7.2 Unit Testing Reports (Login, Signup, and Password Reset tables)
      │    ├── 7.3 Integration Testing Reports
      │    ├── 7.4 General Button Test Cases (Register, Login, Logout buttons)
      │    ├── 7.5 Structural (White Box) and Interface (Black Box) Testing
      │    └── 7.6 System Testing Summary
      └── Chapter 8: Conclusions, Outcomes & Future Work (Summary, Future enhancements, APA Bibliography with ChatGPT/W3Schools/YouTube)
```

---

## 3. Section Blueprint Checklist

### CHAPTER 1: INTRODUCTION & CORE HYPOTHESIS
- **Background & Need:** Describe the shift from manual administrative methods (paper logs, data silos) to digital systems in Mangalore University affiliated colleges.
- **Objectives:** Map 4 distinct objectives including reach, availability, catalogue robustness, and communication tools.
- **Tools/Platforms Used:** Document hardware parameters and software items (HTML, CSS, PHP, MySQL, VS Code).
- **Testing Tools & Outcomes:** Explicitly document manual verification, inputs tests, and expected administrative metrics.

### CHAPTER 2: SOFTWARE REQUIREMENTS SPECIFICATION
- **Assumptions:** Explicitly list that configurations exist, users have credentials, and administrators handle queries safely.
- **Requirements Table:** Must have exactly 5 columns: `Functionality`, `Description`, `User Type`, `Input`, and `Output`.
- **System Requirements:** Detail hardware (Monitor, Keyboard, RAM, Processor) and software (OS, IDE, Languages, local environment) separately.

### CHAPTER 3: SYSTEM LOGICAL FLOW & ARCHITECTURE
- **System Design Definition:** Intro definition explaining design processes.
- **DFD Matrix:** Level 0 (Context), Level 1 (Process flow), and Level 2 (Module details).

### CHAPTER 4: PHYSICAL DATABASE DESIGN
- **Physical Tables:** Schema definitions for 12 primary tables.
- **ER Symbols Table:** Map symbols like Rectangle (Entity), Double Rectangle (Weak Entity), Diamond (Relationship), Double Diamond (Identifying Relationship), Oval (Attribute), Oval with Underlined Text (Key Attribute), Double Oval (Multivalued Attribute), and cardinality notations.

### CHAPTER 5: SOURCE CODE IMPLEMENTATION LISTING
- List key scripts: `db.php`, `functions.php` (security filters, CSRF tokens, role-based redirects), `login.php`, `signup.php`, and `admin/dashboard.php`.

### CHAPTER 7: COMPREHENSIVE QUALITY ASSURANCE & TESTING
- **Methodology Definitions:** Provide explicit academic descriptions for Unit, Integration, White Box, Black Box, and System testing.
- **Unit Test Case Tables:** Generate 3 distinct tables:
  1. **Login Page Unit Tests** (testing correct inputs, bad passwords, empty states, and SQL injection).
  2. **Signup Page Unit Tests** (testing role boundaries, empty states, and duplicate usernames).
  3. **Password Reset Page Unit Tests** (testing empty inputs, mismatched passwords, and correct resets).
- **Button Test Matrix:** Action cases for Register, Login, and Logout buttons showing expected vs. actual outcomes.

### CHAPTER 8: CONCLUSIONS, OUTCOMES & FUTURE WORK
- **Bibliography:** Document references (PHP.net, W3Schools, YouTube, ChatGPT, and OWASP) following the local citation formatting pattern.
