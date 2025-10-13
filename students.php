<?php
include 'db_connect.php';

// ---------------- FETCH ALL STUDENTS ----------------
$result = $conn->query("SELECT Sid, Sname, Address, Phone, DeptID FROM Student");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 85%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-size: 16px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            display: block;
            width: 100px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            background-color: #2c3e50;
            color: white;
            border-radius: 5px;
        }
        a:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

<h2>Students Portfolio</h2>

<!-- Students Table -->
<table>
<tr>
    <th>Student ID</th>
    <th>Name</th>
    <th>Address</th>
    <th>Phone</th>
    <th>DeptID</th>
</tr>
<?php if($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['Sid']) ?></td>
        <td><?= htmlspecialchars($row['Sname']) ?></td>
        <td><?= htmlspecialchars($row['Address']) ?></td>
        <td><?= htmlspecialchars($row['Phone']) ?></td>
        <td><?= htmlspecialchars($row['DeptID']) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5">No students found</td></tr>
<?php endif; ?>
</table>

<br>
<a href="index_p.php">Back</a>

</body>
</html>
