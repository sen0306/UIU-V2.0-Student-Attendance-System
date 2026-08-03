<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_SESSION['user_id'];
$course_id = $_GET['id'];

// Security check: make sure this course actually belongs to the logged-in lecturer
$check = $conn->prepare("SELECT course_name FROM course WHERE course_id = ? AND lecturer_id = ?");
$check->bind_param("ss", $course_id, $lecturer_id);
$check->execute();
$course = $check->get_result()->fetch_assoc();

if (!$course) {
    header("Location: lecturer_home.php");
    exit();
}

// Get total number of sessions held for this course
$stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM session WHERE course_id = ?");
$stmt2->bind_param("s", $course_id);
$stmt2->execute();
$total_sessions = $stmt2->get_result()->fetch_assoc()['total'];

// Get search and sort values from the URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$sql = "SELECT student.student_id, student.student_name,
        COUNT(CASE WHEN attendance_record.status IN ('Attended', 'Absent with Excuse') THEN 1 END) as attended_count
        FROM student
        JOIN student_course ON student.student_id = student_course.student_id AND student_course.course_id = ?
        LEFT JOIN session ON session.course_id = ?
        LEFT JOIN attendance_record ON attendance_record.session_id = session.session_id 
            AND attendance_record.student_id = student.student_id
        WHERE 1=1";

$params = [$course_id, $course_id];
$types = "ss";

if (!empty($search)) {
    $sql .= " AND student.student_name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$sql .= " GROUP BY student.student_id, student.student_name";

if ($sort == "name_asc") {
    $sql .= " ORDER BY student.student_name ASC";
} elseif ($sort == "name_desc") {
    $sql .= " ORDER BY student.student_name DESC";
} elseif ($sort == "percent_asc") {
    $sql .= " ORDER BY attended_count ASC";
} elseif ($sort == "percent_desc") {
    $sql .= " ORDER BY attended_count DESC";
}

$stmt3 = $conn->prepare($sql);
$stmt3->bind_param($types, ...$params);
$stmt3->execute();
$students = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="lecturer_attendance_percentage.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <div class="left-arrow">
                <a href="lecturer_home.php"><i class="fa-solid fa-angle-left"></i></a>
                <p>Attendance Percentage - <?php echo $course['course_name']; ?></p>
            </div>

            <div class="table-container">
            <div class="table-actions">

                <form method="get" action="lecturer_attendance_percentage.php" class="search-sort">
                    <input type="hidden" name="id" value="<?php echo $course_id; ?>">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit()"></i>
                    </div>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Sort by...</option>
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                        <option value="percent_desc" <?php if ($sort == 'percent_desc') echo 'selected'; ?>>Percentage (High-Low)</option>
                        <option value="percent_asc" <?php if ($sort == 'percent_asc') echo 'selected'; ?>>Percentage (Low-High)</option>
                    </select>
                </form>

            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Attendance Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students->num_rows > 0): ?>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo $student['student_name']; ?></td>
                                <td>
                                    <?php 
                                    if ($total_sessions > 0) {
                                        $percentage = round(($student['attended_count'] / $total_sessions) * 100);
                                        echo $percentage . "%";
                                    } else {
                                        echo "No sessions yet";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty_message">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

        </section>
    </main>

</body>
</html>