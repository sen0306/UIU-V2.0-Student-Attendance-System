<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_home.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <div class="dashboard-menu">
                <a href="admin_lecturer.php" class="dashboard-item">Lecturer</a>
                <a href="admin_student.php" class="dashboard-item">Student</a>
                <a href="admin_attendance.php"class="dashboard-item">Attendance</a>
                <a href="admin_course.php"class="dashboard-item">Course</a>
                <a href="admin_faculty.php"class="dashboard-item">Faculty</a>
                </div>
            <div class="signout">
                <a href="logout.php" class="signout-button">Sign Out</a>
            </div>
        </section>
    </main>
    
</body>
</html> 