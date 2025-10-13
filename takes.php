<?php
session_start();
if (!isset($_SESSION['Sid'])) {
    header("Location: index_p.php");
    exit();
}

$Sid = $_SESSION['Sid'];
$Sname = $_SESSION['Sname'];

include 'db_connect.php';

$message = "";
$selectedCourse = $_POST['Ccode'] ?? "";

// ---------------------- DROP COURSE ----------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dropSecNo'])) {
    $dropSecNo = $_POST['dropSecNo'];

    // Delete RoutineTime first
    $conn->query("DELETE FROM RoutineTime WHERE Sid='$Sid' AND secNo='$dropSecNo'");

    // Then delete from Takes
    $drop = $conn->query("DELETE FROM Takes WHERE Sid='$Sid' AND secNo='$dropSecNo'");

    if ($drop) {
        $message = "<p style='color:green;'>✅ Course dropped successfully!</p>";
    } else {
        $message = "<p style='color:red;'>❌ Error while dropping: " . $conn->error . "</p>";
    }
}

// ---------------------- ENROLL COURSE ----------------------
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['secNo'])) {
    $Ccode = $_POST['Ccode'];
    $secNo = $_POST['secNo'];

    // Validate section
    $checkSection = $conn->query("SELECT * FROM Section WHERE secNo='$secNo' AND Ccode='$Ccode'");
    if ($checkSection->num_rows == 0) {
        $message = "<p style='color:red;'>❌ Invalid section selected.</p>";
    } else {
        // Already enrolled?
        $checkAlready = $conn->query("SELECT * FROM Takes WHERE Sid='$Sid' AND secNo='$secNo'");
        if ($checkAlready->num_rows > 0) {
            $message = "<p style='color:red;'>❌ Already enrolled in this section.</p>";
        } else {
            // Check seats
            $checkSeats = $conn->query("SELECT COUNT(*) AS total FROM Takes WHERE secNo='$secNo'");
            $rowSeats = $checkSeats->fetch_assoc();
            if ($rowSeats['total'] >= 10) {
                $message = "<p style='color:red;'>❌ Section full (10/10 seats).</p>";
            } else {
                // Get class time from instructor
                $secInfo = $conn->query("
                    SELECT i.Class 
                    FROM Section s
                    JOIN instructors i ON s.INS_ID = i.INS_ID
                    WHERE s.secNo='$secNo'
                ");
                $secRow = $secInfo->fetch_assoc();
                $newTime = $secRow['Class']; // e.g., "10:00-11:30"
                $newStartEnd = explode('-', $newTime);
                $newStart = strtotime($newStartEnd[0]);
                $newEnd   = strtotime($newStartEnd[1]);

                // Check time clash
                $clashCheck = $conn->query("
                    SELECT i.Class
                    FROM Takes t
                    JOIN Section s ON t.secNo = s.secNo
                    JOIN instructors i ON s.INS_ID = i.INS_ID
                    WHERE t.Sid='$Sid'
                ");
                $hasClash = false;
                while ($row = $clashCheck->fetch_assoc()) {
                    $existTime = $row['Class'];
                    $existStartEnd = explode('-', $existTime);
                    $existStart = strtotime($existStartEnd[0]);
                    $existEnd   = strtotime($existStartEnd[1]);
                    if (!($newEnd <= $existStart || $newStart >= $existEnd)) {
                        $hasClash = true;
                        break;
                    }
                }

                if ($hasClash) {
                    $message = "<p style='color:red;'>❌ Time clash! You already have a class at $newTime.</p>";
                } else {
                    // ---------------- INSERT INTO TAKES (parent) ----------------
                    $insert = $conn->query("INSERT INTO Takes (Sid, Ccode, secNo) VALUES ('$Sid', '$Ccode', '$secNo')");

                    if ($insert) {
                        // ---------------- INSERT INTO ROUTINETIME (child) ----------------
                        $conn->query("INSERT INTO RoutineTime (Sid, secNo, ClassTime, Day) VALUES ('$Sid', '$secNo', '$newTime', 'Everyday')");

                        $message = "<p style='color:green;'>✅ Enrolled successfully!</p>";
                    } else {
                        $message = "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
                    }
                }
            }
        }
    }
}

// ---------------------- FETCH DATA ----------------------
// Available courses
$courses = $conn->query("
    SELECT DISTINCT c.Ccode, c.Cname 
    FROM Section s
    JOIN courses c ON s.Ccode = c.Ccode
    ORDER BY c.Ccode
");

// Sections for selected course
$sections = [];
if (!empty($selectedCourse)) {
    $secQuery = $conn->query("
        SELECT s.secNo, COUNT(t.Sid) AS enrolled, i.Class
        FROM Section s
        LEFT JOIN Takes t ON s.secNo = t.secNo
        JOIN instructors i ON s.INS_ID = i.INS_ID
        WHERE s.Ccode='$selectedCourse'
        GROUP BY s.secNo, i.Class
    ");
    while ($r = $secQuery->fetch_assoc()) {
        $sections[] = $r;
    }
}

// Current courses for drop
$currentCourses = $conn->query("
    SELECT t.secNo, c.Ccode, c.Cname, i.Class
    FROM Takes t
    JOIN Section s ON t.secNo = s.secNo
    JOIN courses c ON s.Ccode = c.Ccode
    JOIN instructors i ON s.INS_ID = i.INS_ID
    WHERE t.Sid='$Sid'
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Enroll / Drop Courses</title>
  <style>
    body { font-family: Arial, sans-serif; margin:30px; }
    form { padding:20px; border:1px solid #ccc; width:450px; margin-bottom:20px; }
    label { display:block; margin-top:10px; }
    select, input[type=submit] { width:100%; padding:8px; margin-top:5px; }
  </style>
</head>
<body>
  <h2>Welcome, <?= $Sname ?> (ID: <?= $Sid ?>) 🎓</h2>
  <p><a href="index_p.php">Logout</a></p>
  <?= $message ?>

  <!-- Enroll Form -->
  <form method="POST" action="takes.php">
    <h3>📚 Enroll in Course</h3>
    <label for="Ccode">Course:</label>
    <select name="Ccode" onchange="this.form.submit()" required>
      <option value="">-- choose course --</option>
      <?php while ($row = $courses->fetch_assoc()): ?>
        <option value="<?= $row['Ccode'] ?>" <?= ($row['Ccode']==$selectedCourse)?"selected":"" ?>>
          <?= $row['Ccode'] ?> - <?= $row['Cname'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label for="secNo">Section (Seats left only):</label>
    <select name="secNo" required>
      <option value="">-- choose section --</option>
      <?php foreach ($sections as $row):
          $seatsLeft = 10-(int)$row['enrolled'];
          $disabled = ($seatsLeft<=0)?"disabled":"";
      ?>
        <option value="<?= $row['secNo'] ?>" <?= $disabled ?>>
          Section <?= $row['secNo'] ?> → <?= $row['Class'] ?> → Seats Left: <?= max($seatsLeft,0) ?>/10
        </option>
      <?php endforeach; ?>
    </select>

    <input type="submit" value="Enroll">
  </form>

  <!-- Drop Form -->
  <form method="POST" action="takes.php">
    <h3>❌ Drop a Course</h3>
    <label for="dropSecNo">Your Enrolled Courses:</label>
    <select name="dropSecNo" required>
      <option value="">-- choose course to drop --</option>
      <?php while ($row = $currentCourses->fetch_assoc()): ?>
        <option value="<?= $row['secNo'] ?>">
          <?= $row['Ccode'] ?> - <?= $row['Cname'] ?> (Section <?= $row['secNo'] ?>, <?= $row['Class'] ?>)
        </option>
      <?php endwhile; ?>
    </select>

    <input type="submit" value="Drop Course">
  </form>
</body>
</html>
<?php $conn->close(); ?>
