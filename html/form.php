<!DOCTYPE html>
<html>
<head>
    <title>Job Application Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Job Application Form</h1>

    <form action="process_application.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label  
 for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="resume">Resume  
 (PDF):</label>
        <input type="file" id="resume" name="resume" accept=".pdf" required><br><br>

        <label for="transcript">Transcript  

 (PDF):</label>
        <input type="file" id="transcript" name="transcript" accept=".pdf" required><br><br>

        <label for="images">Images (JPEG/PNG):</label>
        <input type="file" id="images" name="images[]" multiple accept=".jpg, .png"><br><br>

        <button type="submit">Submit Application</button>
    </form>

    <?php if (isset($_POST["name"])) { ?>
    <h2>Application Summary</h2>
    <table>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Name</td>
            <td><?php echo $_POST["name"]; ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $_POST["email"]; ?></td>
        </tr>
        <tr>
            <td>Resume</td>
            <td><a href="uploads/<?php echo basename($_FILES["resume"]["name"]); ?>">Download</a></td>
        </tr>
        <tr>
            <td>Transcript</td>
            <td><a href="uploads/<?php echo basename($_FILES["transcript"]["name"]); ?>">Download</a></td>
        </tr>
        <tr>
            <td>Images</td>
            <td>
                <?php
                foreach ($_FILES["images"]["error"] as $key => $error) {
                    if ($error == UPLOAD_ERR_OK) {
                        echo "<a href='uploads/" . basename($_FILES["images"]["name"][$key]) . "'><img src='uploads/" . basename($_FILES["images"]["name"][$key]) . "' alt='Image' width='100'></a><br>";
                    }
                }
                ?>
            </td>
        </tr>
    </table>
    <?php } ?>
</body>
</html>

Use code with caution.

PHP Code (process_application.php):
PHP

<?php
// Define upload directory
$upload_dir = "uploads/";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $resume = $_FILES["resume"];
    $transcript = $_FILES["transcript"];
    $images = $_FILES["images"];

    // Validate form data (add more validation as needed)
    if (empty($name) || empty($email) || empty($resume) || empty($transcript)) {
        echo "Please fill out all required fields.";
    } else {
        // Process resume and transcript uploads
        $resume_target_path = $upload_dir . basename($resume["name"]);
        $transcript_target_path = $upload_dir . basename($transcript["name"]);

        if (move_uploaded_file($resume["tmp_name"], $resume_target_path) && move_uploaded_file($transcript["tmp_name"], $transcript_target_path)) {
            // Process image uploads
            foreach ($images["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $image_target_path = $upload_dir . basename($images["name"][$key]);
                    move_uploaded_file($images["tmp_name"][$key], $image_target_path);
                }
            }

            // Redirect to the same page with the application data
            header("Location: " . $_SERVER["PHP_SELF"] . "?name=" . urlencode($name) . "&email=" . urlencode($email) . "&resume=" . urlencode(basename($resume["name"])) . "&transcript=" . urlencode(basename($transcript["name"])) . "&images=" . urlencode(json_encode(array_values($images["name"]))));
            exit();
        } else {
            echo "Error uploading files.";
        }
    }
}
?>