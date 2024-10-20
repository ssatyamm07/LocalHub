<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
    $lssemsaid = $_SESSION['lssemsaid'];
    $name = $_POST['name'] ?? '';
    $mobnum = $_POST['mobilenumber'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $category = $_POST['category'] ?? '';
    $propic = $_FILES["propic"]["name"] ?? '';

    $allowed_extensions = array("jpg", "jpeg", "png", "gif");

    if (!empty($propic)) {
        $extension = pathinfo($propic, PATHINFO_EXTENSION); // Get file extension
        if (!in_array($extension, $allowed_extensions)) {
            echo "<script>alert('Profile Pics has Invalid format. Only jpg / jpeg / png / gif format allowed');</script>";
        } else {
            $propic = md5($propic) . time() . '.' . $extension; // Ensure file name has extension
            move_uploaded_file($_FILES["propic"]["tmp_name"], "images/" . $propic);

            $sql = "INSERT INTO tblperson(Category, Name, Picture, MobileNumber, Address, City) 
                    VALUES(:cat, :name, :pics, :mobilenumber, :address, :city)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':pics', $propic, PDO::PARAM_STR);
            $query->bindParam(':cat', $category, PDO::PARAM_STR);
            $query->bindParam(':mobilenumber', $mobnum, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':city', $city, PDO::PARAM_STR);
            $query->execute();

            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                echo '<script>alert("Person Detail has been added.")</script>';
                echo "<script>window.location.href ='success.php'</script>";
            } else {
                echo '<script>alert("Something Went Wrong. Please try again")</script>';
            }
        }
    } else {
        echo "<script>alert('Please upload a profile picture.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Local Services Search Engine | Register as Worker</title>
    <!-- Include your CSS and JS files here -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!--================================ Main STYLE SHEETs====================================-->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="css/color/color.css">
    <link rel="stylesheet" type="text/css" href="assets/testimonial/css/style.css" />
    <link rel="stylesheet" type="text/css" href="assets/testimonial/css/elastislide.css" />
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <!--================================FONTAWESOME==========================================-->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <!--================================GOOGLE FONTS=========================================-->
    <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Montserrat:400,700|Lato:300,400,700,900'>
    <!--================================SLIDER REVOLUTION =========================================-->
    <link rel="stylesheet" type="text/css" href="assets/revolution_slider/css/revslider.css" media="screen" />
    <!-- Custom styles -->
    <style>
        body { 
            padding: 20px; 
            background-color: lightblue;
            font-family: Arial, sans-serif;
        }
        .form-container { 
            max-width: 600px; 
            margin: 20px auto 0; /* Added margin-top to keep space from navbar */
            padding: 20px; 
            background-color: #fff; 
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2); 
            border-radius: 8px;
            animation: fadeIn 1s ease-in-out;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #343a40;
            text-align: center;
        }
        .form-group label {
            font-weight: bold;
            color: #495057;
        }
        .form-group input, .form-group select, .form-group textarea {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 4px;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include_once('includes/header.php'); ?>
    <div class="container"> 
        <div class="form-container">
            <h2>Register as a Worker</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category">Service Category</label>
                    <select name="category" id="category" class="form-control" required>
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
                    <input type="file" class="form-control" id="propic" name="propic" required>
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
        