<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$sql = "SELECT student.student_id, student.student_name, faculty.faculty_name, student.student_password,
        GROUP_CONCAT(course.course_name SEPARATOR ', ') as course_names
        FROM student
        LEFT JOIN faculty ON student.faculty_id = faculty.faculty_id
        LEFT JOIN student_course ON student.student_id = student_course.student_id
        LEFT JOIN course ON student_course.course_id = course.course_id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND student.student_name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$sql .= " GROUP BY student.student_id, student.student_name, faculty.faculty_name, student.student_password";

if ($sort == "name_asc") {
    $sql .= " ORDER BY student.student_name ASC";
} elseif ($sort == "name_desc") {
    $sql .= " ORDER BY student.student_name DESC";
} elseif ($sort == "id_asc") {
    $sql .= " ORDER BY student.student_id ASC";
} elseif ($sort == "id_desc") {
    $sql .= " ORDER BY student.student_id DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_student.css">
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
                <p>Student</p>
            </div>

            <div class="table-container">

                <?php if (isset($_GET['error']) && $_GET['error'] == 'in_use'): ?>
                    <p class="error-message">This student cannot be removed because they still have attendance records or active course enrollments.</p>
                <?php endif; ?>
                
            <div class="table-actions">

                <form method="get" action="admin_student.php" class="search-sort">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit()"></i>
                    </div>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Sort by...</option>
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                        <option value="id_asc" <?php if ($sort == 'id_asc') echo 'selected'; ?>>ID (Ascending)</option>
                        <option value="id_desc" <?php if ($sort == 'id_desc') echo 'selected'; ?>>ID (Descending)</option>
                    </select>
                </form>

                <a href="add_student.php" class="btn-add"> <p>Add New</p></a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Faculty</th>
                        <th>Course</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['faculty_name'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo $row['course_names'] ?? 'Not Assigned'; ?></td>
                            <td>••••••</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="btn-edit">Edit</a>
                                    <a href="remove_student.php?id=<?php echo $row['student_id']; ?>" 
                                    class="btn-remove"
                                    onclick="return confirm('Are you sure you want to remove this student?');">Remove</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty_message">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
            </div>

        </section>
    </main>
</body>
</html>