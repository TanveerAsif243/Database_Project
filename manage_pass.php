<?php
// --- DB Connection ---
include 'db_connect.php';



// --- Fetch Students ---
$students = $conn->query("SELECT Sid, Sname FROM Student");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Student Passwords</title>
  <style>
    body { font-family: Arial, sans-serif; margin:30px; }
    form { padding:20px; border:1px solid #ccc; width:350px; margin-bottom:20px; }
    label { display:block; margin-top:10px; }
    select, input[type=text], input[type=submit] { width:100%; padding:8px; margin-top:5px; }
  </style>
</head>
<body>
  <h2>🔑 Manage Student Passwords</h2>


  <form method="POST" action="manage_pass.php">
    <label for="Sid">Choose Student:</label>
    <select name="Sid" required>
      <option value="">-- select student --</option>
      <?php while ($row = $students->fetch_assoc()): ?>
        <option value="<?= $row['Sid'] ?>"><?= $row['Sid'] ?> - <?= $row['Sname'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="Password">New Password:</label>
    <input type="text" name="Password" placeholder="Enter new password" required>

    <input type="submit" value="Update Password">
  </form>
</body>
</html>
<?php $conn->close(); ?>
