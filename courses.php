<?php
include 'db_connect.php';


$result = $conn->query("SELECT DISTINCT Ccode, Cname, DeptID FROM courses ORDER BY Ccode");


if(!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Courses</title>
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
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        th, td {
            padding: 12px 20px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
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
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>All Courses</h2>
    <table>
        <tr>
            <th>Code</th>
            <th>Course Name</th>
            <th>Department ID</th>
			
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Ccode'] ?></td>
            <td><?= $row['Cname'] ?></td>
            <td><?= $row['DeptID'] ?></td>
			
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="index_p.php">Back</a>
</body>
</html>
