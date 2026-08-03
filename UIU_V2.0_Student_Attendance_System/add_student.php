<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
$course_result = $conn->query("SELECT course_id, course_name FROM course");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student</title>
    <link rel="stylesheet" href="add_student.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_student.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Add New Student</p>
    </div>

    <div class="form-box">
        <form action="add_student_process.php" method="post">

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>

            <label for="student_name">Name</label>
            <input type="text" name="student_name" id="student_name" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>">
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Courses</label>
            <div class="checkbox-group">
                <?php while ($course = $course_result->fetch_assoc()): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="course_ids[]" value="<?php echo $course['course_id']; ?>">
                        <?php echo $course['course_name']; ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Add Student</button>
        </form>
    </div>

</body>
</html>