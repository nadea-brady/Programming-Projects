<?php
session_start(); // Start the session

require '/home/group5/public_html/connect.php'; // Database connection file

if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit(); // Ensure user is logged in
} else {
    $userID = $_SESSION['user_id'];
}

// Retrieve course name, section, and ID from URL query parameters
$courseName = isset($_GET['courseName']) ? $_GET['courseName'] : 'Default Course Name';
$courseSection = isset($_GET['courseSection']) ? $_GET['courseSection'] : 'Default Course Section';
$courseID = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if (!$courseID) {
    echo "Course ID not set. Please select a course.";
    exit();
}

// Function to fetch and display students enrolled in the course
function getStudents($conn, $courseID) {
    $query = 'SELECT u.User_Name, u.User_Type
              FROM Users u
              INNER JOIN UsersCourse uc ON u.User_Id = uc.Users_User_Id
              WHERE uc.Course_Course_ID = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$courseID]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($students)) {
        echo '<tr><td colspan="2">No students found for this course.</td></tr>';
        return;
    }

    foreach ($students as $student) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($student['User_Name']) . '</td>';
        echo '<td>' . htmlspecialchars($student['User_Type']) . '</td>';
        echo '</tr>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students: <?php echo htmlspecialchars($courseName); ?></title>
    <link rel="stylesheet" href="professor_notes.css">
</head>
<body>
<header class="course-header">
    <div class="course-title">
        <h1><?php echo htmlspecialchars($courseName); ?></h1>
    </div>
    <div class="button-container">
    <div class="back-button">
    <form method="GET" action="professor_notes.php">
        <input type="hidden" name="course_id" value="<?php echo $courseID; ?>">
        <input type="hidden" name="courseName" value="<?php echo $courseName; ?>">
        <button type="submit">Back to Notes</button>
    </form>
</div>
    </div>
</header>
<main class="content">
    <section class="students-section">
        <h2>Students Enrolled</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>User Type</th>
                </tr>
            </thead>
            <tbody>
                <?php getStudents($conn, $courseID); ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>



