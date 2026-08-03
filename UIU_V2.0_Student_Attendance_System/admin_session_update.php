<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$session_id = $_GET['id'];

// Get session info (date, time, and which course it belongs to)
$stmt = $conn->prepare("SELECT session.session_date, session.session_time, session.course_id, course.course_name 
                        FROM session 
                        JOIN course ON session.course_id = course.course_id 
                        WHERE session.session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

// Get all students enrolled in this course, along with their attendance status for THIS session (if any)
$stmt2 = $conn->prepare("SELECT student.student_id, student.student_name, attendance_record.status
                        FROM student
                        JOIN student_course ON student.student_id = student_course.student_id
                        LEFT JOIN attendance_record 
                        ON student.student_id = attendance_record.student_id 
                        AND attendance_record.session_id = ?
                        WHERE student_course.course_id = ?");
$stmt2->bind_param("ss", $session_id, $session['course_id']);
$stmt2->execute();
$students = $stmt2->get_result();

// Count Attended/Absent for the summary bar, only counting currently enrolled students
$stmt3 = $conn->prepare("SELECT status, COUNT(*) as count 
                        FROM attendance_record 
                        WHERE session_id = ? 
                        AND student_id IN 
                        (SELECT student_id FROM student_course WHERE course_id = ?)
                        GROUP BY status");
$stmt3->bind_param("ss", $session_id, $session['course_id']);
$stmt3->execute();
$counts_result = $stmt3->get_result();
$attended_count = 0;
$absent_count = 0;
$excused_count = 0;
while ($count_row = $counts_result->fetch_assoc()) {
    if ($count_row['status'] == 'Attended') $attended_count = $count_row['count'];
    if ($count_row['status'] == 'Absent') $absent_count = $count_row['count'];
    if ($count_row['status'] == 'Absent with Excuse') $excused_count = $count_row['count'];
}
$total_students = $students->num_rows;$stmt3 = $conn->prepare("SELECT status, COUNT(*) as count 
                        FROM attendance_record 
                        WHERE session_id = ? 
                        AND student_id IN 
                        (SELECT student_id FROM student_course WHERE course_id = ?)
                        GROUP BY status");
$stmt3->bind_param("ss", $session_id, $session['course_id']);
$stmt3->execute();
$counts_result = $stmt3->get_result();
$attended_count = 0;
$absent_count = 0;
$excused_count = 0;
while ($count_row = $counts_result->fetch_assoc()) {
    if ($count_row['status'] == 'Attended') $attended_count = $count_row['count'];
    if ($count_row['status'] == 'Absent') $absent_count = $count_row['count'];
    if ($count_row['status'] == 'Absent with Excuse') $excused_count = $count_row['count'];
}
$total_students = $students->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_session_update.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
        <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <div class="left-arrow">
                <a href="admin_course_sessions.php?id=<?php echo $session['course_id']; ?>"><i class="fa-solid fa-angle-left"></i></a>
                <p><?php echo $session['course_name']; ?>, <?php echo date("jS F Y", strtotime($session['session_date'])); ?>, <?php echo date("g:ia", strtotime($session['session_time'])); ?></p>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo $student['student_name']; ?></td>
                                <td><?php echo $student['status'] ?? 'Not Marked'; ?></td>
                                <td>
                                    <div class="actions-buttons">
                                        <a href="update_attendance_process.php?session_id=<?php echo $session_id; ?>&student_id=<?php echo $student['student_id']; ?>&status=Attended" class="btn-attended">Attended</a>
                                        <a href="update_attendance_process.php?session_id=<?php echo $session_id; ?>&student_id=<?php echo $student['student_id']; ?>&status=Absent" class="btn-absent">Absent</a>
                                        <a href="update_attendance_process.php?session_id=<?php echo $session_id; ?>&student_id=<?php echo $student['student_id']; ?>&status=<?php echo urlencode('Absent with Excuse'); ?>" class="btn-excused">Excused</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="summary-bar">
                    <span>Attended: <?php echo $attended_count; ?>/<?php echo $total_students; ?></span>
                    <span>Absent: <?php echo $absent_count; ?>/<?php echo $total_students; ?></span>
                    <span>Excused: <?php echo $excused_count; ?>/<?php echo $total_students; ?></span>
                </div>
            </div>

        </section>
    </main>

</body>
</html>