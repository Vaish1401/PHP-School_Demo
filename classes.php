<?php
include 'config.php';

// Initialize variables
$name = "";
$update = false;

// Handle form submission for adding or updating classes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];

        // Insert new class into database
        $sql = "INSERT INTO classes (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            header("Location: classes.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $class_id = $_POST['class_id'];
        $name = $_POST['name'];

        // Update existing class in database
        $sql = "UPDATE classes SET name=? WHERE class_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $class_id);

        if ($stmt->execute()) {
            header("Location: classes.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Handle delete operation
if (isset($_GET['delete'])) {
    $class_id = $_GET['delete'];

    // Delete class from database
    $sql = "DELETE FROM classes WHERE class_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        header("Location: classes.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all classes from database
$sql = "SELECT * FROM classes";
$result = $conn->query($sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Manage Classes</h1>

        <!-- Add or Edit Form -->
        <div class="card mb-4">
            <h5 class="card-header"><?php echo ($update ? 'Edit Class' : 'Add New Class'); ?></h5>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                    <div class="form-group">
                        <label for="name">Class Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <?php if ($update): ?>
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-success">Add</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Class List -->
        <div class="card">
            <h5 class="card-header">Classes</h5>
            <div class="card-body">
                <ul class="list-group">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($row['name']); ?>
                            <div>
                                <a href="classes.php?edit=<?php echo $row['class_id']; ?>" class="btn btn-sm btn-outline-primary mr-2">Edit</a>
                                <a href="classes.php?delete=<?php echo $row['class_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for some components) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
