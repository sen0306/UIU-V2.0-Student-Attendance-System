<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'];


$stmt = $conn->prepare("SELECT course_name FROM course WHERE course_id = ?");
$stmt->bind_param("s", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();


$stmt2 = $conn->prepare("SELECT DISTINCT DATE_FORMAT(session_date, '%Y-%m') AS month_value, DATE_FORMAT(session_date, '%M %Y') AS month_label 
                        FROM session WHERE course_id = ? ORDER BY month_value");
$stmt2->bind_param("s", $course_id);
$stmt2->execute();
$months = $stmt2->get_result();


$selected_month = isset($_GET['month']) ? $_GET['month'] : '';


if (!empty($selected_month)) {
    $stmt3 = $conn->prepare("SELECT session.session_id, session.session_date, session.session_time,
                            COUNT(CASE WHEN attendance_record.status = 'Attended' THEN 1 END) as attended_count,
                            COUNT(CASE WHEN attendance_record.status = 'Absent' THEN 1 END) as absent_count,
                            COUNT(CASE WHEN attendance_record.status = 'Absent with Excuse' THEN 1 END) as excused_count
                            FROM session
                            LEFT JOIN attendance_record 
                            ON session.session_id = attendance_record.session_id
                            AND attendance_record.student_id IN 
                            (SELECT student_id FROM student_course WHERE course_id = session.course_id)
                            WHERE session.course_id = ? AND DATE_FORMAT(session.session_date, '%Y-%m') = ?
                            GROUP BY session.session_id, session.session_date, session.session_time
                            ORDER BY session.session_date");
    $stmt3->bind_param("ss", $course_id, $selected_month);
} else {
    $stmt3 = $conn->prepare("SELECT session.session_id, session.session_date, session.session_time,
                            COUNT(CASE WHEN attendance_record.status = 'Attended' THEN 1 END) as attended_count,
                            COUNT(CASE WHEN attendance_record.status = 'Absent' THEN 1 END) as absent_count,
                            COUNT(CASE WHEN attendance_record.status = 'Absent with Excuse' THEN 1 END) as excused_count
                            FROM session
                            LEFT JOIN attendance_record 
                            ON session.session_id = attendance_record.session_id
                            AND attendance_record.student_id IN (
                            SELECT student_id FROM student_course WHERE course_id = session.course_id
                            )
                            WHERE session.course_id = ?
                            GROUP BY session.session_id, session.session_date, session.session_time
                            ORDER BY session.session_date");
    $stmt3->bind_param("s", $course_id);
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
    <link rel="stylesheet" href="admin_course_sessions.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>
    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>

            <div class="left-arrow">
                <a href="admin_attendance.php"><i class="fa-solid fa-angle-left"></i></a>
                <p><?php echo $course['course_name']; ?></p>
            </div>
            
            <div class="table-container">


            <div class="table-actions">
                <a href="add_session.php?course_id=<?php echo $course_id; ?>" class="btn-add">Add New</a>
            </div>
            
                <form method="get" action="admin_course_sessions.php" class="month-filter-form">
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
                        <th>Attended</th>
                        <th>Absent</th>
                        <th>Excused</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sessions->num_rows > 0): ?>
                        <?php while ($session = $sessions->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date("l", strtotime($session['session_date'])); ?></td>
                                <td><?php echo date("jS F Y", strtotime($session['session_date'])); ?></td>
                                <td><?php echo date("g:ia", strtotime($session['session_time'])); ?></td>
                                <td><?php echo $session['attended_count']; ?></td>
                                <td><?php echo $session['absent_count']; ?></td>
                                <td><?php echo $session['excused_count']; ?></td>
                                <td>
                                    <div class="actions-buttons">
                                        <a href="admin_session_update.php?id=<?php echo $session['session_id']; ?>" class="btn-update">Update</a>
                                        <a href="remove_session.php?id=<?php echo $session['session_id']; ?>" class="btn-remove">Remove</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty_message">No sessions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

        </section>
    </main>
    
</body>
</html>