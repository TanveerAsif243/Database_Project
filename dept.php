<?php
include 'db_connect.php';

// ---------------- FETCH ALL DEPARTMENTS ----------------
$result = $conn->query("SELECT DeptID, Dname, Doffice, Dphone, Dcode FROM department ORDER BY DeptID");

if(!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Departments</title>
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
            width: 80%;
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

<h2>Departments Portfolio</h2>

<!-- Departments Table -->
<table>
<tr>
    <th>Department ID</th>
    <th>Department Name</th>
    <th>Department Office</th>
    <th>Department Phone</th>
    <th>Department Code</th>
</tr>
<?php if($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['DeptID']) ?></td>
        <td><?= htmlspecialchars($row['Dname']) ?></td>
        <td><?= htmlspecialchars($row['Doffice']) ?></td>
        <td><?= htmlspecialchars($row['Dphone']) ?></td>
        <td><?= htmlspecialchars($row['Dcode']) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5">No departments found</td></tr>
<?php endif; ?>
</table>

<br>
<a href="index_p.php">Back</a>

</body>
</html>
<?php 
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>