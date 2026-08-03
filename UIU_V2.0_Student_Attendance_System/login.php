<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIU V2.0 Student Attendance System</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <main>
        <section>   

        <div class="container">
        
        <div class="logo-container">
            <h1>UIU V2.0 Student Attendance System</h1>
        </div>

        <div class="bg-image">
            <img src="unitar.jpg" alt="An image of a campus">
        </div>

        <div class="form-box" id="login-form">
            <form action="login_process.php" method="post">
                <h2>Login</h2>

                <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Incorrect User ID or Password.</p>
                <?php endif; ?>
                
                <select name="role" required>
                <option value="admin">Admin</option>
                <option value="lecturer">Lecturer</option>
                <option value="student">Student</option>
                </select>
                <input type="text" name="user_id" placeholder="User ID" required>
                <input type="password" name="password" placeholder="Password" required>

                <div class="button-container"> 
                <button type="submit" name="login">Login</button>
                </div>
            </form>
        </div>

        </section>
    </main>

</body>
</html>
