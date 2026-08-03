<?php
session_start();
include 'config.php';

$role = $_POST['role'];
$user_id = $_POST['user_id'];
$password = $_POST['password'];

if ($role == "admin") {
    $table = "admin";
    $id_column = "admin_id";
    $password_column = "admin_password";
} elseif ($role == "lecturer") {
    $table = "lecturer";
    $id_column = "lecturer_id";
    $password_column = "lecturer_password";
} elseif ($role == "student") {
    $table = "student";
    $id_column = "student_id";
    $password_column = "student_password";
} else {
    die("Invalid role selected.");
}

$stmt = $conn->prepare("SELECT * FROM $table WHERE $id_column = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    

    if (password_verify($password, $user[$password_column])) {
        $_SESSION['role'] = $role;
        $_SESSION['user_id'] = $user_id;

        if ($role == "admin") {
            header("Location: admin_home.php");
        } elseif ($role == "lecturer") {
            header("Location: lecturer_home.php");
        } elseif ($role == "student") {
            header("Location: student_home.php");
        }
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
    } else {
        header("Location: login.php?error=1");
    exit();
    }
    
?>

