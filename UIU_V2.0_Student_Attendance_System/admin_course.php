<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT course.course_id, course.course_name, faculty.faculty_name, lecturer.lecturer_id, lecturer.lecturer_name
                        FROM course
                        LEFT JOIN faculty ON course.faculty_id = faculty.faculty_id
                        LEFT JOIN lecturer ON course.lecturer_id = lecturer.lecturer_id"); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_course.css">
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
                <p>Course</p>
            </div>

            <div class="table-container">
                
                <?php if (isset($_GET['error']) && $_GET['error'] == 'in_use'): ?>
                    <p class="error-message">This course cannot be removed because it still has sessions or enrolled students linked to it.</p>
                <?php endif; ?>

            <div class="table-actions">
                <a href="add_course.php" class="btn-add">
                    <p>Add New</p></a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Faculty</th>
                        <th>Lecturer ID</th>
                        <th>Lecturer Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['course_id']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['faculty_name'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo $row['lecturer_id'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo $row['lecturer_name'] ?? 'Not Assigned'; ?></td>
                            <td>
                    <div class="action-buttons">
                        <a href="edit_course.php?id=<?php echo $row['course_id']; ?>" class="btn-edit">Edit</a>
                        <a href="remove_course.php?id=<?php echo $row['course_id']; ?>" 
                            class="btn-remove" 
                            onclick="return confirm('Are you sure you want to remove this course?');">Remove</a>
                    </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty_message">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                
            </tbody>
            </table>
            </div>

        </section>
    </main>

</body>
</html>