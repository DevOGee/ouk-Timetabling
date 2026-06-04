# OUK Timetabling System - User Manual

## Section 1: Introduction

### 1.1. Purpose of the System

The OUK Timetabling System is a comprehensive web-based application designed to streamline the process of creating, managing, and viewing academic timetables at The Open University of Kenya (OUK). The system provides a centralized platform for administrators, instructors, and students to access up-to-date timetable information, manage course schedules, and handle related academic data.

The key objectives of the system are:
*   **Automation:** To automate the complex and time-consuming process of timetable creation.
*   **Centralization:** To provide a single source of truth for all timetable-related information.
*   **Accessibility:** To ensure that students, instructors, and administrators can easily access the information they need, when they need it.
*   **Efficiency:** To reduce the administrative overhead associated with timetable management.

### 1.2. Target Audience

This user manual is intended for the following users:

*   **System Administrators:** Responsible for the overall management of the system, including user accounts, academic sessions, programmes, and timetables. They have full access to all system features.
*   **Instructors:** Can view their assigned courses and schedules, manage their profile, and receive notifications.
*   **Students:** Can view timetables for their respective programmes and courses, manage their profile, and receive notifications.

### 1.3. Getting Started

To get started with the OUK Timetabling System, you will need a user account.

**For new users:**
1.  Navigate to the application's registration page.
2.  Complete the registration form by providing your full name, a valid email address, and a secure password.
3.  You will receive a verification email. Click the link in the email to activate your account.

**For existing users:**
1.  Navigate to the login page.
2.  Enter your credentials (email and password) or use the Google single sign-on option.

---

## Section 2: User Guide

This section provides a comprehensive guide for regular users (students and instructors) of the OUK Timetabling System.

### 2.1. Account Management

#### 2.1.1. Registration

Creating an account is the first step to using the system.

1.  **Navigate to the Registration Page:** Open your web browser and go to the registration URL provided by the university.
2.  **Fill in the Registration Form:**
    *   **Name:** Enter your full name.
    *   **Email:** Use your official university email address.
    *   **Password:** Choose a strong password (at least 8 characters, with a mix of uppercase, lowercase, numbers, and symbols).
    *   **Confirm Password:** Re-enter your password to confirm.
3.  **Submit the Form:** Click the "Register" button.
4.  **Verify Your Email:** Check your university email inbox for a verification message. Click the verification link to activate your account. If you don't see the email, check your spam or junk folder.

#### 2.1.2. Login

*   **Standard Login:**
    1.  Go to the login page.
    2.  **Email:** Enter your registered university email address.
    3.  **Password:** Enter your password.
    4.  Click the "Login" button.

*   **Google OAuth Login:**
    1.  On the login page, click the "Sign in with Google" button.
    2.  If you are not already logged into your university Google account, you will be prompted to do so.
    3.  Grant the application permission to access your basic profile information.
    4.  You will be securely logged into the system.

#### 2.1.3. Profile Management

Your profile page allows you to keep your personal information up to date.

*   **Accessing Your Profile:** After logging in, click on your name in the top-right corner of the page and select "Profile" from the dropdown menu.
*   **Viewing Your Profile:** The profile page displays your name, email, and any other information you have provided.
*   **Editing Your Profile:**
    1.  Click the "Edit Profile" button.
    2.  Update your information in the provided fields.
    3.  Click "Save Changes" to update your profile.
*   **Changing Your Password:**
    1.  In the "Profile" section, find the "Change Password" option.
    2.  Enter your current password.
    3.  Enter your new password and confirm it.
    4.  Click "Update Password".

#### 2.1.4. Notifications

The notification system keeps you informed about important updates.

*   **Accessing Notifications:** Click the bell icon in the top navigation bar to view your notifications.
*   **Types of Notifications:** You may receive notifications for:
    *   Timetable updates for your enrolled courses.
    *   New course assignments (for instructors).
    *   System-wide announcements.
*   **Managing Notifications:** You can mark notifications as read or delete them.

### 2.2. Viewing Timetables

The core feature for students and instructors is viewing timetables.

1.  **Navigate to the Timetable Page:** From the main menu, select "Timetable".
2.  **Filter Options:** Use the dropdown menus to filter the timetable.
    *   **Programme:** Select your academic programme.
    *   **Academic Year:** Choose the current academic year.
    *   **Semester:** Select the current semester.
3.  **Viewing the Timetable:** The timetable will display the courses, instructors, locations, and times for the selected filters. The view is typically a weekly grid.
4.  **Exporting the Timetable:** You can export the timetable to PDF or Excel format by clicking the corresponding export button on the page.

### 2.3. Instructor Information

This section provides a directory of all instructors.

1.  **Navigate to the Instructors Page:** Select "Instructors" from the main menu.
2.  **Viewing Instructors:** The page lists all instructors with their name, title, and school/department.
3.  **Searching for Instructors:** Use the search bar to find a specific instructor by name.
---

## Section 3: Administrator Guide

This section is for system administrators and covers the features available in the admin panel.

### 3.1. Admin Dashboard Overview

The admin dashboard is the central hub for managing the timetabling system.

*   **Accessing the Dashboard:** After logging in as an administrator, you will be directed to the admin dashboard.
*   **Key Metrics:** The dashboard displays key metrics such as:
    *   Total number of users, instructors, and students.
    *   Number of programmes and course units.
    *   Status of current timetables (e.g., published, draft).
*   **Navigation:** The dashboard provides quick links to all major administrative sections.

### 3.2. User Management

#### 3.2.1. Viewing Users

1.  Navigate to **Users** from the admin menu.
2.  The user list displays all users with their name, email, role, and status (active/inactive).
3.  Use the search bar to find users by name or email.
4.  Click on a user's name to view their detailed profile.

#### 3.2.2. Assigning/Removing Roles

Roles determine a user's level of access.

1.  From the user list, click on the user you wish to manage.
2.  In the user's profile, go to the "Roles" section.
3.  **To assign a role:** Select a role from the dropdown menu and click "Assign Role".
4.  **To remove a role:** Click the "Remove" button next to the role you want to remove.

#### 3.2.3. Importing/Exporting Users

*   **Importing Users:**
    1.  Navigate to **Users > Import**.
    2.  Download the CSV template to ensure your data is in the correct format. The template will have columns for `name`, `email`, `role`, etc.
    3.  Fill the template with user data.
    4.  Upload the completed CSV file.
    5.  The system will process the file and create the user accounts.
*   **Exporting Users:**
    1.  Navigate to **Users**.
    2.  Click the "Export Users" button.
    3.  A CSV file containing all user data will be downloaded.

### 3.3. Timetable Management

#### 3.3.1. Creating and Managing Timetables

1.  Navigate to **Timetables**.
2.  **To create a new timetable:**
    *   Click "Create Timetable".
    *   Select the academic session.
    *   The system will guide you through the process of adding courses, instructors, and scheduling slots.
3.  **To manage existing timetables:**
    *   The timetable list shows all created timetables with their status.
    *   You can **edit**, **view**, or **delete** a timetable using the respective buttons.

#### 3.3.2. Publishing/Unpublishing Timetables

*   **Publishing:** When a timetable is ready, click the "Publish" button. This makes it visible to all students and instructors.
*   **Unpublishing:** If you need to make changes to a published timetable, you must first unpublish it. Click the "Unpublish" button to revert it to a draft state.

### 3.4. Programme Management

#### 3.4.1. Creating and Managing Programmes

1.  Navigate to **Programmes**.
2.  **To create a new programme:**
    *   Click "Add Programme".
    *   Fill in the programme name, code, and description.
    *   Click "Save".
3.  **To manage programmes:**
    *   From the programme list, you can edit or delete programmes.
    *   Click on a programme name to view its details and manage the course units assigned to it.

#### 3.4.2. Bulk Uploading Programmes

1.  Navigate to **Programmes > Bulk Upload**.
2.  Download the CSV template.
3.  Fill the template with the programme details.
4.  Upload the CSV file to import the programmes into the system.

### 3.5. Academic Session Management

1.  Navigate to **Academic Sessions**.
2.  **To create a new session:**
    *   Click "Add Session".
    *   Enter the session name (e.g., "2024/2025 Semester 1"), start date, and end date.
    *   Click "Save".
3.  **Set Current Session:** Mark one session as the "current" session. This will be the default session for timetabling.
4.  **Archive Sessions:** Old sessions can be archived to keep the system clean.

### 3.6. Curriculum Management

1.  Navigate to **Curriculum**.
2.  Select an academic session and a programme to manage its curriculum.
3.  **Map Course Units:** Assign course units to the programme for the selected session.
4.  **Define Structure:** Define the core and elective courses for each year of study.

---

## Section 4: Appendix

### 4.1. Frequently Asked Questions (FAQ)

*   **Q: How do I reset my password if I've forgotten it?**
    *   A: On the login page, click the "Forgot Password?" link. Enter your registered email address, and you will receive an email with instructions on how to reset your password.

*   **Q: Can I view timetables from previous semesters?**
    *   A: Yes, if the administrator has archived them. You may need to select the appropriate academic session from the filter options on the timetable page.

*   **Q: As an administrator, can I impersonate a user to see what they see?**
    *   A: This feature is not available for security reasons. You can test user views by creating a test account with the appropriate role.

### 4.2. Troubleshooting

*   **Issue: I am unable to log in, even with the correct password.**
    *   **Solution:**
        1.  Ensure your account has been activated via the verification email.
        2.  Check if your account has been deactivated by an administrator.
        3.  Clear your browser's cache and cookies, then try again.
        4.  If the problem persists, contact the system administrator.

*   **Issue: The website is not loading or displaying correctly.**
    *   **Solution:**
        1.  Check your internet connection.
        2.  Try a different web browser to see if the issue is browser-specific.
        3.  Disable any browser extensions that might be interfering with the website.
        4.  If the issue continues, it may be a system-wide problem. Please contact the administrator.