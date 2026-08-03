<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="lecturer_C001update.css">
    <title>UIU V2.0 Student Attendance System</title>
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>
            <div class="left-arrow">
                <a href="lecturer_C001.php"><i class="fa-solid fa-angle-left"></i></a>
                <p>1st June 2026, 10:00a.m</p>
            </div>

            <div class="table-container">
                <div class="table-actions">
                <div class="search-sort">
                    <div class="search-box">
                        <input type="text" placeholder="Search" name="search"><i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <button class="btn-sort">Sort<i class="fa-solid fa-sort"></i></button>
                </div>
                </div>

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
                <tr>
                    <td>S001</td>
                    <td>Adam</td>
                    <td>Attended</td>
                    <td>
                    <div class="action-button">
                    <button class="btn-attended">Attended</button>
                    <button class="btn-absent">Absent</button> 
                    </div>
                    </td>
                </tr>
            </tbody>
            </table>

            <div class="summary-bar">
                <span>Attended: 3/5</span>
                <span>Absent: 2/5</span>
            </div>

            <div class="save-changes">
                <button class="btn-save">Save Changes</button>
            </div>
            
        </section>
    </main>

</body>
</html>