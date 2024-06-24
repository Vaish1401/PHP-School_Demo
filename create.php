<?php
include 'config.php';

$name = $email = $address = $class_id = $image = "";
$name_err = $image_err = "";

// Fetch classes for the dropdown
$class_result = $conn->query("SELECT class_id, name FROM classes");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $class_id = trim($_POST["class_id"]);
    
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
            $image = time() . "_" . basename($_FILES["image"]["name"]);
            $target_path = "images/" . $image;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_path);
        }
    }

    // Insert data if no errors
    if (empty($name_err) && empty($image_err)) {
        $stmt = $conn->prepare("INSERT INTO student (name, email, address, class_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $name, $email, $address, $class_id, $image);

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
    <title>Create Student</title>
</head>
<body>
    <h1>Create Student</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
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
                        echo "<option value='" . $row['class_id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No classes available</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label>Image:</label>
            <input type="file" name="image">
            <span><?php echo $image_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Create Student">
        </div>
    </form>
</body>
</html>
