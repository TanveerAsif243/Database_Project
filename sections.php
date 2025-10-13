<?php
include 'db_connect.php';

// ---------------- FETCH ALL SECTIONS ----------------
$result = $conn->query("SELECT s.secNo, s.Ccode, s.Semester, s.INS_ID
                        FROM Section s 
                        LEFT JOIN courses c ON s.Ccode = c.Ccode 
                        LEFT JOIN instructors i ON s.INS_ID = i.INS_ID 
                        ORDER BY s.secNo");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sections</title>
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
            width: 90%;
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

<h2>All Sections</h2>

<!-- Sections Table -->
<table>
<tr>
    <th>Section No</th>
    <th>Course Code</th>

    <th>Semester</th>

    <th>Instructor ID</th>

</tr>
<?php if($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['secNo']) ?></td>
        <td><?= htmlspecialchars($row['Ccode']) ?></td>
        <td><?= htmlspecialchars($row['Semester']) ?></td>
        <td><?= htmlspecialchars($row['INS_ID']) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="7">No sections found</td></tr>
<?php endif; ?>
</table>

<br>
<a href="index_p.php">Back</a>

</body>
</html>
<?php $conn->close(); ?>