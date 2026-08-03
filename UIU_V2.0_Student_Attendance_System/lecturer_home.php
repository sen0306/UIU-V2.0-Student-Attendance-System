<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_SESSION['user_id'];

// Get the lecturer's name
$stmt = $conn->prepare("SELECT lecturer_name FROM lecturer WHERE lecturer_id = ?");
$stmt->bind_param("s", $lecturer_id);
$stmt->execute();
$lecturer = $stmt->get_result()->fetch_assoc();

// Get only the courses assigned to this lecturer
$stmt2 = $conn->prepare("SELECT course_id, course_name FROM course WHERE lecturer_id = ?");
$stmt2->bind_param("s", $lecturer_id);
$stmt2->execute();
$courses = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="lecturer_home.css">
    <title>UIU V2.0 Student Attendance System</title>

</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <h1 class="welcome-text">Welcome, <?php echo $lecturer['lecturer_name']; ?>!</h1>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course ID</th>
                            <th>Course Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses->num_rows > 0): ?>
                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $course['course_id']; ?></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td>
                                        <div class="actions-buttons">
                                            <a href="lecturer_course_sessions.php?id=<?php echo $course['course_id']; ?>" class="btn-view">View</a>
                                            <a href="lecturer_attendance_percentage.php?id=<?php echo $course['course_id']; ?>" class="btn-view">Attendance Percentage</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty_message">No courses assigned yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="signout">
                <a href="logout.php" class="signout-button">Sign Out</a>
            </div>

        </section>
    </main>

</body>
</html>