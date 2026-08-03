<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT course_id, course_name FROM course");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_attendance.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <div class="left-arrow">
                <a href="admin_home.php"><i class="fa-solid fa-angle-left"></i></a>
                <p>Attendance</p>
            </div>

            <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['course_id']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td>
                            <div class="actions-buttons">
                                <a href="admin_course_sessions.php?id=<?php echo $row['course_id']; ?>" class="btn-view">View</a> 
                                <a href="admin_attendance_percentage.php?id=<?php echo $row['course_id']; ?>" class="btn-view">Attendance Percentage</a>
                            </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty_message">No courses found.</td>
                    </tr>
                <?php endif; ?>
                
            </tbody>
            </table>
            </div>

        </section>
    </main>

</body>
</html>