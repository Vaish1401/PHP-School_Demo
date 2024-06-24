<?php
include 'config.php';

// Check if ID parameter is provided
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student details to display confirmation
    $sql = "SELECT name FROM student WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $student_name = $row['name'];
    } else {
        echo "Student not found.";
        exit();
    }

    $stmt->close();
} else {
    echo "Invalid request.";
    exit();
}

// Handle deletion upon confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_delete'])) {
        $delete_sql = "DELETE FROM student WHERE id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("i", $student_id);

        if ($stmt_delete->execute()) {
            // Delete associated image file if exists
            $image_path = "uploads/" . $student_id . ".jpg"; // Adjust according to your image naming convention
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            header("Location: index.php");
            exit();
        } else {
            echo "Error deleting student.";
        }

        $stmt_delete->close();
    } else {
        // If cancel is clicked, redirect back to view page or index.php
        header("Location: view.php?id=" . $student_id);
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Delete Student</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Are you sure you want to delete <?php echo htmlspecialchars($student_name); ?>?</h5>
            </div>
            <div class="card-footer">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $student_id; ?>" method="post">
                    <input type="submit" name="confirm_delete" class="btn btn-danger" value="Yes, Delete">
                    <a href="view.php?id=<?php echo $student_id; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for some components) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
