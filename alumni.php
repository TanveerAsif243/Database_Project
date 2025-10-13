<?php
include 'db_connect.php';

// Set the actual column name for department name in your Department table
$deptColumn = 'DeptID'; // Change if your column name is different

// Query to fetch alumni with department names
$sql = "SELECT 
            a.AlumniID, 
            a.Name, 
            a.Email, 
            a.Position, 
            a.Company, 
            a.Graduation_Year, 
            a.Degree, 
            d.$deptColumn AS DeptName
        FROM Alumni a
        LEFT JOIN Department d ON a.DeptID = d.DeptID
        ORDER BY a.AlumniID DESC";

$res = $conn->query($sql);

// Check for errors
if (!$res) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alumni List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #2c3e50; color: white; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Alumni List</h2>
    <table>
        <tr>
            <th>Alumni ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Position</th>
            <th>Company</th>
            <th>Graduation Year</th>
            <th>Degree</th>
            <th>Department</th>
        </tr>
        <?php if ($res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['AlumniID']) ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Position']) ?></td>
                    <td><?= htmlspecialchars($row['Company']) ?></td>
                    <td><?= htmlspecialchars($row['Graduation_Year']) ?></td>
                    <td><?= htmlspecialchars($row['Degree']) ?></td>
                    <td><?= htmlspecialchars($row['DeptName']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No alumni found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
