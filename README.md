The Quiz Management System is a web-based platform designed to manage and conduct quizzes efficiently. It allows administrators to start and end quizzes, add participants, and categorize them into Senior and Junior groups. Each participant is assigned a unique code to access their quiz.

Features
Add and manage participants.

Categorize participants into Senior and Junior groups.

Generate unique access codes for each participant.

Start and end quizzes for specific categories.

Redirect participants to a waiting room until the quiz begins.

Installation
Step 1: Set Up the Environment
Download and install a web server:

WAMP Server (Recommended)

OR XAMPP.

Place the project files:

For WAMP: Copy the project folder to the www directory.

For XAMPP: Copy the project folder to the htdocs directory.

Step 2: Configure the Database
Open your database management tool (e.g., phpMyAdmin).

Import the ini.sql file to set up the database structure and initial data.

Step 3: Add an Administrator
Insert an administrator record into the admin table.

Ensure the password is stored as a hashed value. You can use bcrypt or PHP’s password_hash() function to generate a secure hash.

Usage
Start your web server and open the project in your browser.

Log in as an administrator to manage participants and quizzes.

Add participants and assign them to the appropriate category (Senior or Junior).

Assign each participant a unique access code (automatically generated or manually entered).

Participants use their access codes to log in and access their quiz.

Quiz Flow
Accessing the Quiz:

Participants log in using their unique access codes.

After logging in, they are redirected to a waiting room.

Starting the Quiz:

The administrator starts the quiz for a specific category.

Participants in that category can then proceed to take the quiz.

Ending the Quiz:

The administrator ends the quiz session, completing the process.

Notes
Ensure participants are properly added and assigned to categories through the admin dashboard.

Each participant’s unique code ensures secure access to the quiz and prevents unauthorized entries.

The system organizes quizzes by categories, ensuring seamless management for both administrators and participants.

Contribution
Feel free to contribute to the project by submitting pull requests or reporting issues.

License
MIT License
