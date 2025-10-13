<?php
session_start();
include 'db_connect.php';

$Sid = $_SESSION['Sid'] ?? '';
$Sname = $_SESSION['Sname'] ?? '';
$msg = "";

// Handle AJAX request for instructors
if(isset($_GET['ajax']) && $_GET['ajax'] == 1 && isset($_GET['ccode'])){
    $ccode = $_GET['ccode'];
    $res = $conn->query("SELECT DISTINCT i.INS_ID, i.Name 
                         FROM instructors i 
                         JOIN Section s ON i.INS_ID = s.INS_ID 
                         WHERE s.Ccode='$ccode'");
    if($res->num_rows > 0){
        echo '<option value="">Select Instructor</option>';
        while($row = $res->fetch_assoc()){
            echo "<option value='".$row['INS_ID']."'>".$row['Name']."</option>";
        }
    } else {
        echo '<option value="">No instructors available</option>';
    }
    exit;
}

// Only students with ID starting with 24 can submit priority
$canSubmitPriority = !empty($Sid) && substr($Sid,0,2)=="24";

// Handle Add/Update Priority
if ($canSubmitPriority && isset($_POST['add_priority'])) {
    $ccode = $_POST['ccode'];
    $ins_id = $_POST['ins_id'];

    // Check if already in Priority
    $check = $conn->query("SELECT * FROM Priority WHERE Sid='$Sid' AND Ccode='$ccode'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE Priority SET INS_ID='$ins_id' WHERE Sid='$Sid' AND Ccode='$ccode'");
        $msg = "✅ Priority updated successfully!";
    } else {
        $conn->query("INSERT INTO Priority (Sid, Ccode, INS_ID) VALUES ('$Sid','$ccode','$ins_id')");
        $msg = "✅ Priority submitted successfully!";
    }

    // Add to TAKES for routine
    $sec = $conn->query("SELECT secNo FROM Section WHERE Ccode='$ccode' AND INS_ID='$ins_id' LIMIT 1")->fetch_assoc();
    if($sec){
        $secNo = $sec['secNo'];
        $taken = $conn->query("SELECT * FROM Takes WHERE Sid='$Sid' AND Ccode='$ccode' AND secNo='$secNo'");
        if($taken->num_rows == 0){
            $conn->query("INSERT INTO Takes (Sid, Ccode, secNo) VALUES ('$Sid','$ccode','$secNo')");
        }
    }
}

// Handle Drop Priority
if ($canSubmitPriority && isset($_POST['drop_priority'])) {
    $priorityID = $_POST['priorityID'];

    // Remove from Takes
    $p = $conn->query("SELECT Ccode, INS_ID FROM Priority WHERE PriorityID='$priorityID' AND Sid='$Sid'")->fetch_assoc();
    if($p){
        $ccode = $p['Ccode'];
        $ins_id = $p['INS_ID'];
        $sec = $conn->query("SELECT secNo FROM Section WHERE Ccode='$ccode' AND INS_ID='$ins_id' LIMIT 1")->fetch_assoc();
        if($sec){
            $secNo = $sec['secNo'];
            $conn->query("DELETE FROM Takes WHERE Sid='$Sid' AND Ccode='$ccode' AND secNo='$secNo'");
        }
    }

    // Delete from Priority
    $conn->query("DELETE FROM Priority WHERE PriorityID='$priorityID' AND Sid='$Sid'");
    $msg = "✅ Priority dropped successfully!";
}

// Fetch current priorities
$priorities = $canSubmitPriority ? $conn->query("
    SELECT p.PriorityID, c.Ccode, c.Cname, i.Name AS Instructor
    FROM Priority p
    JOIN courses c ON p.Ccode = c.Ccode
    JOIN instructors i ON p.INS_ID = i.INS_ID
    WHERE p.Sid='$Sid'
    ORDER BY p.PriorityID ASC
") : null;

// Fetch routine
$routine = $conn->query("
    SELECT r.Day, r.ClassTime, c.Cname, cl.RoomN, cl.BLDG
    FROM RoutineTime r
    JOIN Takes t ON r.Sid = t.Sid AND r.secNo = t.secNo
    JOIN Section sec ON r.secNo = sec.secNo
    JOIN Courses c ON sec.Ccode = c.Ccode
    JOIN Classroom cl ON sec.secNo = cl.secNo
    WHERE r.Sid = '$Sid'
    ORDER BY r.Day, r.ClassTime
");

if(!$canSubmitPriority){
    $msg = "⚠️ Only students with ID starting with 24 can submit priorities.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Priority Courses & Routine</title>
<style>
body { font-family: Arial, sans-serif; margin:30px; }
form { padding:20px; border:1px solid #ccc; width:400px; margin-bottom:20px; }
label { display:block; margin-top:10px; }
select, input[type=submit] { width:100%; padding:8px; margin-top:5px; }
.msg { margin-bottom:15px; font-weight:bold; color:green; }
table { border-collapse: collapse; width: 80%; margin-top:20px; }
th, td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#2c3e50; color:white; }
.drop-btn { background:red; color:white; border:none; padding:5px 10px; cursor:pointer; }
.drop-btn:hover { background:#a00; }
</style>
<script>


function fetchInstructors(courseCode){
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "?ajax=1&ccode="+courseCode, true);
    xhr.onload = function(){
        if(this.status==200){
            document.getElementById("insSelect").innerHTML = this.responseText;
        }
    };
    xhr.send();
}



</script>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($Sname) ?> (ID: <?= htmlspecialchars($Sid) ?>)</h2>
<p><a href="index_p.php">Logout</a></p>

<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

<?php if($canSubmitPriority): ?>
<form method="POST">
    <h3>📌 Submit Priority</h3>

    <label>Course</label>
    <select name="ccode" required onchange="fetchInstructors(this.value)">
        <option value="">Select Course</option>
        <?php
        $res = $conn->query("SELECT Ccode, Cname FROM courses");
        while($r = $res->fetch_assoc()){
            echo "<option value='".$r['Ccode']."'>".$r['Cname']."</option>";
        }
        ?>
    </select>

    <label>Instructor</label>
    <select name="ins_id" id="insSelect" required>
        <option value="">Select Instructor</option>
    </select>

    <input type="submit" name="add_priority" value="Submit Priority">
</form>

<h3>Your Priorities</h3>
<table>
<tr>
<th>Priority ID</th>
<th>Course</th>
<th>Instructor</th>
<th>Action</th>
</tr>
<?php if($priorities && $priorities->num_rows > 0): ?>
    <?php while($row = $priorities->fetch_assoc()): ?>
        <tr>
            <td><?= $row['PriorityID'] ?></td>
            <td><?= $row['Ccode'] ?> - <?= $row['Cname'] ?></td>
            <td><?= $row['Instructor'] ?></td>
            <td>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="priorityID" value="<?= $row['PriorityID'] ?>">
                    <input type="submit" name="drop_priority" value="Drop" class="drop-btn">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr><td colspan="4">No priorities submitted yet.</td></tr>
<?php endif; ?>
</table>
<?php endif; ?>


</body>
</html>
<?php $conn->close(); ?>
