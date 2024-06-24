<?php
include 'config.php';

// Fetch all students with their class names
$sql = "SELECT student.id, student.name, student.email, student.created_at, student.image, classes.name AS class_name 
        FROM student 
        LEFT JOIN classes ON student.class_id = classes.class_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Demo - Home</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        img {
            width: 50px;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>List of Students</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Class</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['created_at']}</td>
                        <td>{$row['class_name']}</td>
                        <td><img src='images/{$row['image']}' alt='Student Image'></td>
                        <td>
                            <a href='view.php?id={$row['id']}'>View</a> | 
                            <a href='edit.php?id={$row['id']}'>Edit</a> | 
                            <a href='delete.php?id={$row['id']}'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No students found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
