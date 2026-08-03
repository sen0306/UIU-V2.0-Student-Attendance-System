<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Faculty</title>
    <link rel="stylesheet" href="add_faculty.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_faculty.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Add New Faculty</p>
    </div>

    <div class="form-box">
        <form action="add_faculty_process.php" method="post">

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>

            <label for="faculty_name">Faculty Name</label>
            <input type="text" name="faculty_name" id="faculty_name" required>

            <button type="submit">Add Faculty</button>
        </form>
    </div>

</body>
</html>