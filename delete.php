<?php
include 'config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student details to get the image file path
    $sql = "SELECT image FROM student WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $image_path = 'images/' . $student['image'];
    } else {
        echo "No student found with ID: " . $student_id;
        exit();
    }

    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Delete the student from the database
        $stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
        $stmt->bind_param("i", $student_id);

        if ($stmt->execute()) {
            // Remove the image from the server
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "No student ID provided.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
</head>
<body>
    <h1>Delete Student</h1>
    <p>Are you sure you want to delete this student?</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $student_id; ?>" method="post">
        <input type="submit" value="Yes, Delete">
        <a href="index.php">No, Go Back</a>
    </form>
</body>
</html>
