<?php
include('staffheader.php');
include('../connect.php');

// Handle File Upload
if (isset($_POST['btnUpload'])) {
    $bookname = $_POST['bookname'];
    $booksem = $_POST['booksem'];
    $uploadDir = "../Student/uploads/";

    // File upload handling
    $fileName = basename($_FILES["bookfile"]["name"]);
    $cleanName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $fileName); // Replace special characters with "_"
    $targetPath = "../Student/uploads/" . $cleanName;
    
    if (move_uploaded_file($_FILES["bookfile"]["tmp_name"], $targetPath)) {
        // Insert into database
        $query = "INSERT INTO books (bookname, bookfile, booksem) VALUES ('$bookname', '$cleanName', '$booksem')";
        if (mysqli_query($conn, $query)) {
            $success = "File uploaded successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    } else {
        $error = "Error uploading file!";
    }
}
?>

<!-- Upload Form -->
<div class="container mt-4">
    <h4 class="mb-3">Upload Study Materials</h4>

    <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
    <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="bookname" class="form-label">Book Name / Material Name</label>
            <input type="text" name="bookname" id="bookname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="booksem" class="form-label">Select Semester</label>
            <select name="booksem" id="booksem" class="form-select" required>
                <option value="CPISM">CPISM</option>
                <option value="DISM">DISM</option>
                <option value="HDSE I">HDSE I</option>
                <option value="HDSE II">HDSE II</option>
                <option value="ADSE I">ADSE I</option>
                <option value="ADSE II">ADSE II</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="bookfile" class="form-label">Upload File (PDF, DOCX, PPT, etc.)</label>
            <input type="file" name="bookfile" id="bookfile" class="form-control" required>
        </div>

        <button type="submit" name="btnUpload" class="btn btn-dark float-end w-50">Upload File</button>
    </form>
</div>

<?php
include('stafffooter.php');
?>
