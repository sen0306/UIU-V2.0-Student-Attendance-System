<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM faculty WHERE faculty_id = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$faculty = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Faculty</title>
    <link rel="stylesheet" href="edit_faculty.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_faculty.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Edit Faculty</p>
    </div>

    <div class="form-box">
        <form action="edit_faculty_process.php" method="post">

            <input type="hidden" name="faculty_id" value="<?php echo $faculty['faculty_id']; ?>">

            <label for="faculty_name">Faculty Name</label>
            <input type="text" name="faculty_name" id="faculty_name" value="<?php echo $faculty['faculty_name']; ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>