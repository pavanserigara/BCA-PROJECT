# Phase 3: Financials & Identity

## Objective
Establish a robust financial management system for fee collection and structure, implement a dynamic digital ID card generation engine, and initialize logistics management for hostels and transport services.

## Scope
1.  **Fees Management**:
    *   **Structure**: Define mandatory fees by Course and Semester (Tuition, Lab, Exam, etc.).
    *   **Collection**: Securely record student payments with automatic receipt number generation.
    *   **Reporting**: Dashboard for Admins to track revenue and pending dues.
2.  **Digital Identity**:
    *   **Generation**: Admin tool to generate print-ready digital ID cards.
    *   **Access**: Students can view and download their digital ID cards.
3.  **Logistics (Hostel & Transport)**:
    *   **Schema**: Add tables for Hostels, Rooms, Bus Routes, and Assignments.
    *   **Assignment**: Admin interface to allocate rooms and transport routes to students.

## Proposed Changes

### 🗄️ Database Updates (`database/cms.sql`)
*   Add `hostels` table (name, location, capacity).
*   Add `hostel_rooms` table (hostel_id, room_no, capacity, current_occupancy).
*   Add `bus_routes` table (route_name, vehicle_no, driver_name, capacity).
*   Add `logistics_assignments` table (user_id, hostel_room_id NULL, bus_route_id NULL).

### 🏛️ Admin Module
*   `admin/fees-structure.php`: Form to define fee components for specific course/semester combinations.
*   `admin/fees-collect.php`: Enhanced student search and payment recording.
*   `admin/id-cards.php`: A premium UI to generate ID cards. Use CSS grid/flex for a professional layout.
*   `admin/logistics.php`: Manage hostel and transport assignments.

### 🎓 Student Module
*   `student/fees.php`: Updated to show detailed fee breakdown (Total, Paid, Pending) and payment history.
*   `student/id-card.php`: A "Sci-Fi ERP" style digital ID card with profile photo and QR code (mocked).

## Verification Plan

### Automated Tests
*   Verify fee calculation logic (Expected - Paid = Pending).
*   Check database constraints for duplicate fee structure entries.

### Manual UAT (User Acceptance Testing)
1.  **Fee Collection**:
    *   [ ] Post a fee structure for "BCA" Semester 1.
    *   [ ] Record a payment for a student in that course.
    *   [ ] Verify the student's dashboard reflects the payment.
2.  **ID Cards**:
    *   [ ] Generate an ID card for a student.
    *   [ ] Verify profile picture and course details are correctly displayed.
3.  **Logistics**:
    *   [ ] Create a hostel and a bus route.
    *   [ ] Assign a student to both.
    *   [ ] Verify the assignment persists in the database.

## Technical Details
*   **CSS**: Use Tailwind CSS for the ID card layout to ensure it's easily printable and responsive.
*   **PDF Generation**: For receipts/ID cards, implement high-end HTML/CSS templates that can be printed to PDF directly from the browser.
*   **Security**: CSRF protection on all forms (`fees-structure`, `fees-collect`, `logistics-assignment`).
