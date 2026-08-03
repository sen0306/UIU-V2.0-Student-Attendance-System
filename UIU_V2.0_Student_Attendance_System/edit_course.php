<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'];

// Get this course's current data
$stmt = $conn->prepare("SELECT * FROM course WHERE course_id = ?");
$stmt->bind_param("s", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

// Get all faculties for the dropdown
$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");

// Get all lecturers for the dropdown
$lecturer_result = $conn->query("SELECT lecturer_id, lecturer_name FROM lecturer");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="edit_course.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_course.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Edit Course</p>
    </div>

    <div class="form-box">
        <form action="edit_course_process.php" method="post">

            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">

            <label for="course_name">Course Name</label>
            <input type="text" name="course_name" id="course_name" value="<?php echo $course['course_name']; ?>" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>"
                        <?php if ($faculty['faculty_id'] == $course['faculty_id']) echo 'selected'; ?>>
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="lecturer_id">Lecturer</label>
            <select name="lecturer_id" id="lecturer_id">
                <option value="">-- Not Assigned --</option>
                <?php while ($lecturer = $lecturer_result->fetch_assoc()): ?>
                    <option value="<?php echo $lecturer['lecturer_id']; ?>"
                        <?php if ($lecturer['lecturer_id'] == $course['lecturer_id']) echo 'selected'; ?>>
                        <?php echo $lecturer['lecturer_id']; ?> - <?php echo $lecturer['lecturer_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>