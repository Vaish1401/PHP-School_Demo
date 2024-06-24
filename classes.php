<?php
include 'config.php';

// Initialize variables for form data
$class_name = $class_name_err = "";

// Handle form submission to add a new class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_class"])) {
    $class_name = trim($_POST["class_name"]);

    // Validate class name
    if (empty($class_name)) {
        $class_name_err = "Class name is required";
    } else {
        // Insert new class into database
        $stmt = $conn->prepare("INSERT INTO classes (name) VALUES (?)");
        $stmt->bind_param("s", $class_name);

        if ($stmt->execute()) {
            header("Location: classes.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Handle deletion of a class
if (isset($_GET['delete'])) {
    $class_id = $_GET['delete'];

    // Delete class from database
    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        header("Location: classes.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all classes
$class_result = $conn->query("SELECT * FROM classes");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
</head>
<body>
    <h1>Manage Classes</h1>
    
    <!-- Form to add a new class -->
    <h2>Add New Class</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Class Name:</label>
            <input type="text" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>">
            <span><?php echo $class_name_err; ?></span>
        </div>
        <div>
            <input type="submit" name="add_class" value="Add Class">
        </div>
    </form>

    <!-- List of all classes -->
    <h2>All Classes</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($class_result->num_rows > 0) {
                while ($row = $class_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['class_id']}</td>
                        <td>{$row['name']}</td>
                        <td>
                            <a href='edit_class.php?id={$row['class_id']}'>Edit</a> | 
                            <a href='classes.php?delete={$row['class_id']}' onclick=\"return confirm('Are you sure you want to delete this class?');\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No classes found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
