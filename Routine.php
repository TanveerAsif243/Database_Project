<?php
session_start();
if (!isset($_SESSION['Sid'])) {
    header("Location: index_p.php");
    exit();
}

$Sid = $_SESSION['Sid'];
$Sname = $_SESSION['Sname'];

include 'db_connect.php';

// Fetch routine from Takes, including classroom info
$routineQuery = $conn->query("
    SELECT 
        t.secNo, 
        s.Ccode, 
        c.Cname, 
        i.Name AS InstructorName, 
        i.Class AS ClassTime,
        cl.RoomN,
        cl.BLDG
    FROM Takes t
    JOIN Section s ON t.secNo = s.secNo
    JOIN courses c ON s.Ccode = c.Ccode
    JOIN instructors i ON s.INS_ID = i.INS_ID
    LEFT JOIN Classroom cl ON s.secNo = cl.secNo
    WHERE t.Sid = '$Sid'
    ORDER BY i.Class
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Routine</title>
    <style>
        body { font-family: Arial, sans-serif; margin:30px; }
        table { border-collapse: collapse; width: 100%; max-width:800px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 20px; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <h2>📅 Routine for <?= htmlspecialchars($Sname) ?> (ID: <?= htmlspecialchars($Sid) ?>)</h2>

    <?php if ($routineQuery->num_rows > 0): ?>
        <table>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Section</th>
                <th>Instructor</th>
                <th>Class Time</th>
                <th>Day</th>
                <th>Room</th>
                <th>Building</th>
            </tr>
            <?php while ($row = $routineQuery->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Ccode']) ?></td>
                    <td><?= htmlspecialchars($row['Cname']) ?></td>
                    <td><?= htmlspecialchars($row['secNo']) ?></td>
                    <td><?= htmlspecialchars($row['InstructorName']) ?></td>
                    <td><?= htmlspecialchars($row['ClassTime']) ?></td>
                    <td>Sunday - Thursday</td> <!-- Fixed day value -->
                    <td><?= htmlspecialchars($row['RoomN']) ?></td>
                    <td><?= htmlspecialchars($row['BLDG']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p>
            <a href="index_p.php">Logout</a>
        </p>
    <?php else: ?>
        <p>No routine available.</p>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>
