<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
$course_result = $conn->query("SELECT course_id, course_name FROM course");

// Get the list of course IDs this student is currently enrolled in
$stmt2 = $conn->prepare("SELECT course_id FROM student_course WHERE student_id = ?");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$enrolled_result = $stmt2->get_result();
$enrolled_course_ids = [];
while ($row = $enrolled_result->fetch_assoc()) {
    $enrolled_course_ids[] = $row['course_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="edit_student.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_student.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Edit Student</p>
    </div>

    <div class="form-box">
        <form action="edit_student_process.php" method="post">

            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">

            <label for="student_name">Name</label>
            <input type="text" name="student_name" id="student_name" value="<?php echo $student['student_name']; ?>" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>"
                        <?php if ($faculty['faculty_id'] == $student['faculty_id']) echo 'selected'; ?>>
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Courses</label>
            <div class="checkbox-group">
                <?php while ($course = $course_result->fetch_assoc()): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="course_ids[]" value="<?php echo $course['course_id']; ?>"
                            <?php if (in_array($course['course_id'], $enrolled_course_ids)) echo 'checked'; ?>>
                        <?php echo $course['course_name']; ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <label for="password">New Password (leave blank to keep current password)</label>
            <input type="password" name="password" id="password">

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>