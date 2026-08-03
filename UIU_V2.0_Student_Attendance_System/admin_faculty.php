<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT faculty.faculty_id, faculty_name FROM faculty");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_faculty.css">
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
                <p>Faculty</p>
            </div>
        
            <div class="table-container">

            <?php if (isset($_GET['error']) && $_GET['error'] == 'in_use'): ?>
                <p class="error-message">This faculty cannot be removed because it still has lecturers, students, or courses assigned to it.</p>
            <?php endif; ?>

            <div class="table-actions">
                <a href="add_faculty.php" class="btn-add">
                    <p>Add New</p></a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['faculty_id']; ?></td>
                            <td><?php echo $row['faculty_name']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_faculty.php?id=<?php echo $row['faculty_id']; ?>" class="btn-edit">Edit</a>
                                    <a href="remove_faculty.php?id=<?php echo $row['faculty_id']; ?>" 
                                        class="btn-remove" 
                                        onclick="return confirm('Are you sure you want to remove this faculty?');">Remove</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty_message">No faculties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
            </div>
                    
        </section>
    </main>

</body>
</html>