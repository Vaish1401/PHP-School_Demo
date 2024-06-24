<?php
include 'config.php';

// Check if ID parameter is provided
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student details including class name for dropdown
    $sql = "SELECT s.id, s.name, s.email, s.address, s.class_id, s.image, c.name as class_name
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
        $student_class_id = $row['class_id'];
        $student_class_name = $row['class_name'];
        $student_image = $row['image'];
    } else {
        echo "Student not found.";
        exit();
    }

    $stmt->close();
} else {
    echo "Invalid request.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $class_id = $_POST["class_id"];

    // Image upload handling
    $upload_dir = 'uploads/';
    $image_path = $student_image; // Default to existing image

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];
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

    // Update data in database
    if (empty($upload_err)) {
        $sql_update = "UPDATE student SET name=?, email=?, address=?, class_id=?, image=? WHERE id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssisi", $name, $email, $address, $class_id, $image_path, $student_id);

        if ($stmt_update->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error updating student: " . $stmt_update->error;
        }

        $stmt_update->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
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
        <h1 class="my-4">Edit Student</h1>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $student_id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($student_name); ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($student_email); ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($student_address); ?></textarea>
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group">
                <label>Class</label>
                <select name="class_id" class="form-control <?php echo (!empty($class_id_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select Class</option>
                    <?php while ($row = $class_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['class_id']; ?>" <?php echo ($student_class_id == $row['class_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php } ?>
                </select>
                <span class="invalid-feedback"><?php echo $class_id_err; ?></span>
            </div>
            <div class="form-group">
                <label>Current Image</label><br>
                <img src="uploads/<?php echo htmlspecialchars($student_image); ?>" alt="Current Image" style="max-width: 300px;">
            </div>
            <div class="form-group">
                <label>Upload New Image</label>
                <input type="file" name="image" class="form-control-file">
                <?php if (!empty($upload_err)) { ?>
                    <span class="text-danger"><?php echo $upload_err; ?></span>
                <?php } ?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional for some components) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
