<?php
session_start();
error_reporting(0);
include('Includes/dbConnection.php'); // Ensure the case matches your directory

if(isset($_POST['submit'])) {

    $name = $_POST['name'];
    $mobnum = $_POST['mobilenumber'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $service_category = $_POST['service_category'];
    $profile_picture = $_FILES["profile_picture"]["name"];
    
    // File extension validation
    $extension = substr($profile_picture, strlen($profile_picture) - 4, strlen($profile_picture));
    $allowed_extensions = array(".jpg", "jpeg", ".png", ".gif");
    if (!in_array($extension, $allowed_extensions)) {
        echo "<script>alert('Profile picture has an invalid format. Only jpg / jpeg / png / gif formats are allowed');</script>";
    } else {
        // Renaming the uploaded file
        $profile_picture_new = md5($profile_picture) . time() . $extension;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], "images/" . $profile_picture_new);

        // Insert data into the database
        $sql = "INSERT INTO tblworker(service_category, name, profile_picture, mobile_number, address, city) 
                VALUES(:service_category, :name, :profile_picture, :mobile_number, :address, :city)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':service_category', $service_category, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':profile_picture', $profile_picture_new, PDO::PARAM_STR);
        $query->bindParam(':mobile_number', $mobnum, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':city', $city, PDO::PARAM_STR);
        $query->execute();

        $LastInsertId = $dbh->lastInsertId();
        if ($LastInsertId > 0) {
            echo '<script>alert("You have registered successfully as a worker.")</script>';
            echo "<script>window.location.href ='index.php'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Local Services Search Engine | Register as Worker</title>
    <!-- Include your CSS and JS files here -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Custom styles -->
    <style>
        body { padding: 20px; }
        .form-container { max-width: 600px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <?php include_once('includes/header.php'); ?>
    <div class="container" 
    /*style="background-color: green;"*/> -->
        <div class="form-container">
            <h2>Register as a Worker</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="service_category">Service Category</label>
                    <select name="service_category" id="service_category" class="form-control" required>
                        <option value="">Choose Category</option>
                        <?php 
                        $sql2 = "SELECT * FROM tblcategory";
                        $query2 = $dbh->prepare($sql2);
                        $query2->execute();
                        $result2 = $query2->fetchAll(PDO::FETCH_OBJ);
                        foreach ($result2 as $row) { ?>
                            <option value="<?php echo htmlentities($row->Category); ?>"><?php echo htmlentities($row->Category); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
                </div>
                <div class="form-group">
                    <label for="mobilenumber">Mobile Number</label>
                    <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" placeholder="Enter your mobile number" pattern="[0-9]+" maxlength="10" required>
                </div> 
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" placeholder="Enter your address" required></textarea>
                </div> 
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city" required>
                </div>  
                <button type="submit" class="btn btn-primary" name="submit">Register</button>
            </form>
        </div>
    </div>
    <?php include_once('includes/footer.php'); ?>
    <!-- Include your JS files here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
