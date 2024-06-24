<?php
include 'config.php';

// Initialize variables for form data
$class_id = $class_name = $class_name_err = "";

if (isset($_GET['id'])) {
    $class_id = $_GET['id'];

    // Fetch class details
    $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
        $class_name = $class['name'];
    } else {
        echo "No class found with ID: " . $class_id;
        exit();
    }

    $stmt->close();
} else {
    echo "No class ID provided.";
    exit();
}

// Handle form submission to edit class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_class"])) {
    $class_name = trim($_POST["class_name"]);

    // Validate class name
    if (empty($class_name)) {
        $class_name_err = "Class name is required";
    } else {
        // Update class in database
        $stmt = $conn->prepare("UPDATE classes SET name = ? WHERE class_id = ?");
        $stmt->bind_param("si", $class_name, $class_id);

        if ($stmt->execute()) {
            header("Location: classes.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class</title>
</head>
<body>
    <h1>Edit Class</h1>
    
    <!-- Form to edit class -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $class_id; ?>" method="post">
        <div>
            <label>Class Name:</label>
            <input type="text" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>">
            <span><?php echo $class_name_err; ?></span>
        </div>
        <div>
            <input type="submit" name="edit_class" value="Update Class">
        </div>
    </form>
</body>
</html>
