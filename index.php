<?php
session_start();
$conn = new mysqli("localhost", "your_username", "your_password", "your_database"); // Replace with your DB details

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Registration Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $conn->real_escape_string(trim($_POST['reg_username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = password_hash(trim($_POST['reg_password']), PASSWORD_DEFAULT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($_POST['reg_password']) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $checkUser = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
        if ($checkUser->num_rows > 0) {
            $error = "Username or email already exists!";
        } else {
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
            if ($conn->query($sql) === TRUE) {
                $success = "Registration successful!";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Login Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: welcome.php"); // Redirect to a welcome page
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <!-- Login Form -->
        <div class="form-box login">
            <form method="POST" action="">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-box register">
            <form method="POST" action="">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="reg_username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="reg_password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
        </div>
        
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
