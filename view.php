<?php
include 'config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student details with class name
    $sql = "SELECT student.id, student.name, student.email, student.address, student.created_at, student.image, classes.name AS class_name 
            FROM student 
            LEFT JOIN classes ON student.class_id = classes.class_id 
            WHERE student.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "No student found with ID: " . $student_id;
        exit();
    }

    $stmt->close();
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
    <title>View Student</title>
    <style>
        .student-details {
            width: 50%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .student-details img {
            max-width: 100%;
            height: auto;
        }
        .student-details h2 {
            text-align: center;
        }
        .student-details p {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="student-details">
        <h2><?php echo htmlspecialchars($student['name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
        <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($student['address'])); ?></p>
        <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?></p>
        <p><strong>Created At:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>
        <p><strong>Image:</strong></p>
        <img src="images/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image">
    </div>
</body>
</html>
