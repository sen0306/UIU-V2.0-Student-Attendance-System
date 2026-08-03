<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get all faculties for the dropdown
$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");

// Get all lecturers for the dropdown
$lecturer_result = $conn->query("SELECT lecturer_id, lecturer_name FROM lecturer");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Course</title>
    <link rel="stylesheet" href="add_course.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_course.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Add New Course</p>
    </div>

    <div class="form-box">
        <form action="add_course_process.php" method="post">

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>

            <label for="course_name">Course Name</label>
            <input type="text" name="course_name" id="course_name" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>">
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="lecturer_id">Lecturer (optional)</label>
            <select name="lecturer_id" id="lecturer_id">
                <option value="">-- Not Assigned --</option>
                <?php while ($lecturer = $lecturer_result->fetch_assoc()): ?>
                    <option value="<?php echo $lecturer['lecturer_id']; ?>">
                        <?php echo $lecturer['lecturer_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Add Course</button>
        </form>
    </div>

</body>
</html>