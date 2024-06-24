<?php
include 'config.php';

// Check if ID parameter is provided
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare SQL query to fetch student details including class name
    $sql = "SELECT s.id, s.name, s.email, s.address, s.created_at, s.image, c.name as class_name
            FROM student s
            LEFT JOIN classes c ON s.class_id = c.class_id
            WHERE s.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $student_name = $row['name'];
        $student_email = $row['email'];
        $student_address = $row['address'];
        $student_class = $row['class_name'];
        $student_image = $row['image'];
        $created_at = $row['created_at'];
    } else {
        echo "Student not found.";
        exit();
    }

    $stmt->close();
} else {
    echo "Invalid request.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-info {
            margin-top: 20px;
        }
        .student-image {
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">View Student Details</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($student_name); ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($student_email); ?></p>
                <p class="card-text"><strong>Address:</strong> <?php echo htmlspecialchars($student_address); ?></p>
                <p class="card-text"><strong>Class:</strong> <?php echo htmlspecialchars($student_class); ?></p>
                <p class="card-text"><strong>Creation Date:</strong> <?php echo htmlspecialchars($created_at); ?></p>
            </div>
            <img src="uploads/<?php echo htmlspecialchars($student_image); ?>" class="card-img-bottom student-image" alt="Student Image">
        </div>

        <a href="index.php" class="btn btn-primary mt-3">Back to Student List</a>
    </div>

    <!-- Bootstrap JS (optional for some components) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
