<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$course_id = $_GET['id'];

// Security check: make sure this course is actually one the student is enrolled in
$check = $conn->prepare("SELECT course.course_name 
                        FROM student_course 
                        JOIN course ON student_course.course_id = course.course_id 
                        WHERE student_course.student_id = ? AND student_course.course_id = ?");
$check->bind_param("ss", $student_id, $course_id);
$check->execute();
$course = $check->get_result()->fetch_assoc();

if (!$course) {
    header("Location: student_home.php");
    exit();
}

// Get the list of distinct months this course has sessions in (for the dropdown)
$stmt2 = $conn->prepare("SELECT DISTINCT DATE_FORMAT(session_date, '%Y-%m') AS month_value, DATE_FORMAT(session_date, '%M %Y') AS month_label 
                        FROM session WHERE course_id = ? ORDER BY month_value");
$stmt2->bind_param("s", $course_id);
$stmt2->execute();
$months = $stmt2->get_result();

$selected_month = isset($_GET['month']) ? $_GET['month'] : '';

// Get sessions, filtered by month if selected, along with this student's status
if (!empty($selected_month)) {
    $stmt3 = $conn->prepare("SELECT session.session_id, session.session_date, session.session_time, attendance_record.status
                            FROM session
                            LEFT JOIN attendance_record 
                            ON session.session_id = attendance_record.session_id 
                            AND attendance_record.student_id = ?
                            WHERE session.course_id = ? AND DATE_FORMAT(session.session_date, '%Y-%m') = ?
                            ORDER BY session.session_date");
    $stmt3->bind_param("sss", $student_id, $course_id, $selected_month);
} else {
    $stmt3 = $conn->prepare("SELECT session.session_id, session.session_date, session.session_time, attendance_record.status
                            FROM session
                            LEFT JOIN attendance_record 
                            ON session.session_id = attendance_record.session_id 
                            AND attendance_record.student_id = ?
                            WHERE session.course_id = ?
                            ORDER BY session.session_date");
    $stmt3->bind_param("ss", $student_id, $course_id);
}
$stmt3->execute();
$sessions = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="student_course_sessions.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>

            <div class="left-arrow">
                <a href="student_home.php"><i class="fa-solid fa-angle-left"></i></a>
                <p><?php echo $course['course_name']; ?></p>
            </div>

            <div class="table-container">

                    <form method="get" action="student_course_sessions.php" class="month-filter-form">
                        <input type="hidden" name="id" value="<?php echo $course_id; ?>">
                        <select name="month" onchange="this.form.submit()">
                            <option value="">All Months</option>
                            <?php while ($month = $months->fetch_assoc()): ?>
                                <option value="<?php echo $month['month_value']; ?>"
                                    <?php if ($month['month_value'] == $selected_month) echo 'selected'; ?>>
                                    <?php echo $month['month_label']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($sessions->num_rows > 0): ?>
                                    <?php while ($session = $sessions->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date("l", strtotime($session['session_date'])); ?></td>
                                            <td><?php echo date("jS F Y", strtotime($session['session_date'])); ?></td>
                                            <td><?php echo date("g:ia", strtotime($session['session_time'])); ?></td>
                                            <td><?php echo $session['status'] ?? '-'; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="empty_message">No sessions found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


        </section>
    </main>

</body>
</html>