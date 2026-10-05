<?php
session_start();

/* ================= DATABASE ================= */
$conn = mysqli_connect("localhost", "root", "", "university_db");
if (!$conn) {
    die("Database connection failed");
}

/* ================= LOGIN ================= */
if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE username=? AND role=? AND status='active' LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "ss", $username, $role);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($res)) {

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "User not found or inactive";
    }
}

/* ================= REGISTER ================= */
if (isset($_POST['register'])) {

    $full_name = trim($_POST['full_name']);
    $phone     = trim($_POST['phone']);
    $roll_no   = trim($_POST['roll_no']);
    $email     = trim($_POST['email']);
    $username  = trim($_POST['reg_username']);
    $password  = $_POST['reg_password'];

    if (strlen($password) < 6) {
        $reg_error = "Password must be at least 6 characters";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users 
            (full_name, phone, roll_no, email, username, password, role, status)
            VALUES (?, ?, ?, ?, ?, ?, 'student', 'active')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssss",
            $full_name,
            $phone,
            $roll_no,
            $email,
            $username,
            $hash
        );

        if (mysqli_stmt_execute($stmt)) {
            $reg_success = "Registration successful! Please login.";
        } else {
            $reg_error = "Username or email already exists";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Smart University Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    background:
        linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65)),
        url("images/huzaifa3.jpg") center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:Segoe UI, sans-serif;
}

.card-box{
    width:950px;
    background:#fff;
    border-radius:18px;
    display:flex;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,.45);
}

.left{
    width:45%;
    background:linear-gradient(180deg,#0f2a44,#1b4f72);
    color:#fff;
    padding:40px;
}

.right{
    width:55%;
    padding:40px;
}
</style>
</head>

<body>

<div class="card-box">

<!-- LEFT PANEL -->
<div class="left">
    <h3><i class="fa-solid fa-building-columns"></i> Smart University</h3>
    <p class="mt-3">Academic Management System</p>
    <ul class="mt-4">
        <li>Attendance Management</li>
        <li>Online Courses</li>
        <li>Results & Grades</li>
        <li>Fee Management</li>
        <li>Library System</li>
    </ul>
</div>

<!-- RIGHT PANEL -->
<div class="right">

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#login">Login</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#register">Register</a>
    </li>
</ul>

<div class="tab-content">

<!-- LOGIN -->
<div class="tab-pane fade show active" id="login">
<form method="post">

<input name="username" class="form-control mb-2" placeholder="Username" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<select name="role" class="form-control mb-2" required>
    <option value="">Select Role</option>
    <option value="student">Student</option>
    <option value="faculty">Faculty</option>
    <option value="admin">Admin</option>
</select>

<?php if(isset($error)) echo "<div class='text-danger mb-2'>$error</div>"; ?>

<button name="login" class="btn btn-primary w-100">
    <i class="fa-solid fa-right-to-bracket"></i> Login
</button>

</form>
</div>

<!-- REGISTER -->
<div class="tab-pane fade" id="register">
<form method="post">

<input name="full_name" class="form-control mb-2" placeholder="Full Name" required>

<input name="phone" class="form-control mb-2" placeholder="Phone Number" required>

<input name="roll_no" class="form-control mb-2" placeholder="Roll Number" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

<input name="reg_username" class="form-control mb-2" placeholder="Username" required>

<input type="password" name="reg_password" class="form-control mb-2" placeholder="Password" required>

<?php
if (isset($reg_error)) echo "<div class='text-danger mb-2'>$reg_error</div>";
if (isset($reg_success)) echo "<div class='text-success mb-2'>$reg_success</div>";
?>

<button name="register" class="btn btn-success w-100">
    <i class="fa-solid fa-user-plus"></i> Register
</button>

</form>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
