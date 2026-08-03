<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_GET['id'];

// Get this lecturer's current data
$stmt = $conn->prepare("SELECT * FROM lecturer WHERE lecturer_id = ?");
$stmt->bind_param("s", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

// Get all faculties for the dropdown
$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Lecturer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="edit_lecturer.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_lecturer.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Edit Lecturer</p>
    </div>

    <div class="form-box">
        <form action="edit_lecturer_process.php" method="post">

            <input type="hidden" name="lecturer_id" value="<?php echo $lecturer['lecturer_id']; ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $lecturer['lecturer_name']; ?>" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>"
                        <?php if ($faculty['faculty_id'] == $lecturer['faculty_id']) echo 'selected'; ?>>
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="password">New Password (leave blank to keep current password)</label>
            <input type="password" name="password" id="password">

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>