<?php
include 'config.php';

$name = $email = $address = $class_id = $image = "";
$name_err = $image_err = "";
$student_id = $_GET['id'];

// Fetch student details
$sql = "SELECT * FROM student WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $name = $student['name'];
    $email = $student['email'];
    $address = $student['address'];
    $class_id = $student['class_id'];
    $image = $student['image'];
} else {
    echo "No student found with ID: " . $student_id;
    exit();
}

$stmt->close();

// Fetch classes for the dropdown
$class_result = $conn->query("SELECT class_id, name FROM classes");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $class_id = trim($_POST["class_id"]);
    $new_image = $image;

    // Validate name
    if (empty($name)) {
        $name_err = "Name is required";
    }

    // Validate image
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed_types = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png");
        $file_type = $_FILES["image"]["type"];
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

        if (!array_key_exists($file_ext, $allowed_types) || !in_array($file_type, $allowed_types)) {
            $image_err = "Please upload a valid image file (jpg, jpeg, png)";
        } else {
            $new_image = time() . "_" . basename($_FILES["image"]["name"]);
            $target_path = "images/" . $new_image;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_path);
        }
    }

    // Update data if no errors
    if (empty($name_err) && empty($image_err)) {
        $stmt = $conn->prepare("UPDATE student SET name = ?, email = ?, address = ?, class_id = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $name, $email, $address, $class_id, $new_image, $student_id);

        if ($stmt->execute()) {
            header("Location: index.php");
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
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $student_id; ?>" method="post" enctype="multipart/form-data">
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <span><?php echo $name_err; ?></span>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div>
            <label>Address:</label>
            <textarea name="address"><?php echo htmlspecialchars($address); ?></textarea>
        </div>
        <div>
            <label>Class:</label>
            <select name="class_id">
                <?php
                if ($class_result->num_rows > 0) {
                    while ($row = $class_result->fetch_assoc()) {
                        $selected = ($row['class_id'] == $class_id) ? "selected" : "";
                        echo "<option value='" . $row['class_id'] . "' " . $selected . ">" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No classes available</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label>Current Image:</label>
            <img src="images/<?php echo htmlspecialchars($image); ?>" alt="Student Image" style="max-width: 100px;">
        </div>
        <div>
            <label>New Image:</label>
            <input type="file" name="image">
            <span><?php echo $image_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Update Student">
        </div>
    </form>
</body>
</html>
