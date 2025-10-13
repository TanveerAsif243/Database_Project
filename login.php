<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Sid = $_POST['Sid'];
    $Password = $_POST['Password'];

    // Check plain password
    $sql = "SELECT * FROM Student WHERE Sid='$Sid' AND Password='$Password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['Sid'] = $row['Sid'];
        $_SESSION['Sname'] = $row['Sname'];
        header("Location: index_p.php");
        exit();
    } else {
        $message = "<p style='color:red;'>❌ Invalid Student ID or Password.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Login</title>
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f6f9;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        width: 400px;
        text-align: center;
    }
    h2 {
        margin-bottom: 20px;
        color: #2c3e50;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    label {
        text-align: left;
        font-weight: bold;
        color: #34495e;
    }
    input[type=text], input[type=password] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }
    input[type=submit] {
        background-color: #2c3e50;
        color: white;
        padding: 12px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }
    input[type=submit]:hover {
        background-color: #34495e;
    }
    .message {
        margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🎓 Student Login</h2>
    <div class="message"><?= $message ?></div>
    <form method="POST" action="login.php">
        <label for="Sid">Student ID:</label>
        <input type="text" name="Sid" required>
        <label for="Password">Password:</label>
        <input type="password" name="Password" required>
        <input type="submit" value="Login">
    </form>
  </div>
</body>
</html>
