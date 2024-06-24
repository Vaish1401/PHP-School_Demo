<?php
include 'config.php';

// Initialize variables for form data
$name = $email = $address = $class_id = '';
$name_err = $email_err = $address_err = '';

// Fetch classes for dropdown
$class_result = $conn->query("SELECT * FROM classes");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter student's name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter student's email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter student's address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate class id
    if (empty($_POST["class_id"])) {
        $class_id_err = "Please select a class.";
    } else {
        $class_id = $_POST["class_id"];
    }

    // Image upload handling
    $upload_dir = 'uploads/';
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $file_type = $_FILES["image"]["type"];
        $file_size = $_FILES["image"]["size"];

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png');

        if (!in_array($file_ext, $allowed_types)) {
            $upload_err = "Only JPG, JPEG, and PNG files are allowed.";
        } else {
            // Generate unique filename to avoid overwriting
            $new_filename = uniqid('image_') . '.' . $file_ext;

            // Move uploaded file to uploads directory with unique name
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                $image_path = $new_filename;
            } else {
                $upload_err = "Error uploading file.";
            }
        }
    }

    // Insert data into database if no errors
    if (empty($name_err) && empty($email_err) && empty($address_err) && empty($class_id_err) && empty($upload_err)) {
        $sql = "INSERT INTO student (name, email, address, class_id, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $name, $email, $address, $class_id, $image_path);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + .75rem);
            background-image: url('data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' fill=\'none\' stroke=\'%23dc3545\' viewBox=\'0 0 12 12\'%3e%3ccircle cx=\'6\' cy=\'6\' r=\'4.5\'/%3e%3cpath stroke-linejoin=\'round\' d=\'M5.8 3.6h.4L6 7.4z\'/%3e%3cpath d=\'M6 7.4V8\'/%3e%3c/svg%3e');
            background-repeat: no-repeat;
            background-position: right calc(.375em + .1875rem) center;
            background-size: calc(.75em + .375rem) calc(.75em + .375rem);
        }
        .invalid-feedback {
            color: #dc3545;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Add New Student</h1>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($address); ?></textarea>
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group">
                <label>Class</label>
                <select name="class_id" class="form-control <?php echo (!empty($class_id_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select Class</option>
                    <?php while ($row = $class_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['class_id']; ?>" <?php echo ($class_id == $row['class_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php } ?>
                </select>
                <span class="invalid-feedback"><?php echo $class_id_err; ?></span>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control-file">
                <?php if (!empty($upload_err)) { ?>
                    <span class="text-danger"><?php echo $upload_err; ?></span>
                <?php } ?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional for some components) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
