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
$courseID = isset($_GET['course_id']) ? $_GET['course_id'] : (isset($_SESSION['course_id']) ? $_SESSION['course_id'] : null);

if (!$courseID) {
    echo "Course ID not set. Please select a course.";
    exit();
}

// Check if the form is submitted and if a file is uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fileInput'])) {
    $file = $_FILES['fileInput'];

    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    $timestamp = date('Y-m-d'); // Current date

    $directoryName = $courseName . "_" . str_replace(' ', '_', $courseSection);
    $destinationDir = "/home/group5/public_html/DashboardOfficial/" . $directoryName . "/";

    if (!file_exists($destinationDir)) {
        if (!mkdir($destinationDir, 0777, true)) {
            echo "Failed to create the destination directory.";
            exit();
        }
    }

    $filePath = $destinationDir . $fileName; // Complete file path

    if (move_uploaded_file($fileTmpName, $filePath)) {
        try {
            $query = "INSERT INTO Notes (Note_Size, Note_Date, Note_Approval, Note_Checked, Users_User_Id, Course_Course_ID, Note_Path, Note_Name) 
                      VALUES (?, ?, 0, 0, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$fileSize, $timestamp, $userID, $courseID, $filePath, $fileName]);

            header("Location: " . $_SERVER['REQUEST_URI']); // Redirect to prevent resubmission
            exit();
        } catch (PDOException $e) {
            echo "Error inserting note into database: " . $e->getMessage();
        }
    } else {
        echo "File upload failed: " . $_FILES['fileInput']['error'];
    }
}

// Function to fetch and display notes from the database specific to the course
function getNotes($conn, $courseID) {
    $query = 'SELECT * FROM Notes WHERE Course_Course_ID = ? ORDER BY Note_Date DESC';
    $stmt = $conn->prepare($query);
    $stmt->execute([$courseID]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($notes)) {
        echo '<tr><td colspan="5">No notes found for this course.</td></tr>';
        return;
    }

    foreach ($notes as $note) {
        $path = str_replace("/home/group5/public_html/", "/~group5/", $note['Note_Path']);
        echo '<tr>';
        echo '<td>' . htmlspecialchars($note['Note_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($note['Note_Name']) . '</td>';
        echo '<td>' . htmlspecialchars($note['Users_User_Id']) . '</td>';
        echo '<td><a href="' . htmlspecialchars($path) . '" target="_blank">View Note</a></td>';
        echo '<td><form method="POST"><input type="hidden" name="note_id" value="' . $note['Note_ID'] . '"><button type="submit" name="deleteNote">Delete</button></form></td>';
        echo '</tr>';
    }
}

// Handle note deletion from POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteNote'])) {
    $noteID = $_POST['note_id'];
    $query = 'DELETE FROM Notes WHERE Note_ID = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$noteID]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Notes: <?php echo htmlspecialchars($courseName); ?></title>
    <link rel="stylesheet" href="professor_notes.css">
</head>
<body>
    <header class="course-header">
        <h1><?php echo htmlspecialchars($courseName); ?></h1>
    </header>
    <main class="content">
        <section class="upload-section">
            <h2>Upload New Note</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="fileInput">
                <button type="submit">Upload</button>
            </form>
        </section>
        <section class="notes-section">
            <h2>Available Notes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date Uploaded</th>
                        <th>Note Name</th>
                        <th>User</th>
                        <th>View Notes</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="notesList">
                    <?php getNotes($conn, $courseID); ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
