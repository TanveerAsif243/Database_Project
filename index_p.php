<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>University Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; }
        header { background:#2c3e50; color:white; padding:15px; text-align:center; }
        nav { background:#34495e; padding:10px; }
        nav a { color:white; margin:0 15px; text-decoration:none; }
        nav a:hover { text-decoration:underline; }
        .container { padding:20px; }
        .card { display:inline-block; width:200px; margin:15px; padding:20px; background:white; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.2); text-align:center; }
        .routine, .priority-form { margin-top:30px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#2c3e50; color:white; }
        form { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.2); }
        input, select { padding:8px; margin:5px 0; width:100%; }
        button { padding:10px 20px; margin-top:10px; background:#2c3e50; color:white; border:none; border-radius:5px; cursor:pointer; }
        button:hover { background:#34495e; }
        .msg { color: green; font-weight:bold; margin-bottom:15px; }
    </style>
</head>
<body>
<header>
    <h1>University Management System</h1>
</header>

<nav>
    <a href="dept.php">Departments</a>
    <a href="students.php">Students</a>
    <a href="instructors.php">Instructors</a>
    <a href="courses.php">Courses</a>
    <a href="sections.php">Sections</a>
    <a href="takes.php">Adding Courses</a>
    <a href="routine.php">Routine</a>
    <a href="alumni.php">Alumni</a>
    <a href="priority.php">Priority</a>
    <a href="logout.php">Log Out</a>
	
	
</nav>

<div class="container">

    <!-- Priority Form -->
    <div class="priority-form">
        <h2>Priority Course Submission</h2>

        <?php if(isset($msg) && $msg != ""): ?>
            <div class='msg'><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Course</label>
            <select name="ccode" required>
                <option value="">See Courses</option>
                <?php
                $res = $conn->query("SELECT Ccode, Cname FROM Courses");
                if ($res && $res->num_rows > 0) {
                    while($r = $res->fetch_assoc()) {
                        echo "<option value='".$r['Ccode']."'>".$r['Cname']."</option>";
                    }
                } else {
                    echo "<option value=''>No courses found</option>";
                }
                ?>
            </select>

            <label>Instructor</label>
            <select name="ins_id" required>
                <option value="">See Instructors</option>
                <?php
                $res = $conn->query("SELECT INS_ID, Name FROM instructors");
                if ($res && $res->num_rows > 0) {
                    while($r = $res->fetch_assoc()) {
                        echo "<option value='".$r['INS_ID']."'>".$r['Name']."</option>";
                    }
                } else {
                    echo "<option value=''>No instructors found</option>";
                }
                ?>
            </select>


        
        </form>

        <h3>Priorities</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Instructor</th>
       
            </tr>
            <?php
            $res = $conn->query("
                SELECT p.PriorityID, c.Ccode, c.Cname, i.Name AS Instructor
                FROM Priority p
                JOIN Courses c ON p.Ccode = c.Ccode
                JOIN instructors i ON p.INS_ID = i.INS_ID
                WHERE p.Sid = Sid
            ");

            if($res && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    echo "<tr>
                            <td>{$row['PriorityID']}</td>
                            <td>{$row['Ccode']} - {$row['Cname']}</td>
                            <td>{$row['Instructor']}</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='priorityID' value='{$row['PriorityID']}'>
                                

                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No priorities submitted yet</td></tr>";
            }
            ?>
        </table>
    </div>

 




<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
</body>
</html>
