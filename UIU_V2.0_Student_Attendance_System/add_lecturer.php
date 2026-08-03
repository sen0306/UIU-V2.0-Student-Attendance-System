<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add_lecturer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_lecturer.php"><i class="fa-solid fa-angle-left"></i></a>
        <p>Add New Lecturer</p>
    </div>

    <div class="form-box">
        <form action="add_lecturer_process.php" method="post">
            <h2>Add Lecturer</h2>
            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>

            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['faculty_id']; ?>">
                        <?php echo $faculty['faculty_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Add Lecturer</button>
        </form>
    </div>
    
</body>
</html>