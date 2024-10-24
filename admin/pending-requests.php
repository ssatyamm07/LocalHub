<?php
session_start();
include('includes/dbconnection.php');  // Include your database configuration

// Check if the admin is logged in
if (!isset($_SESSION['lssemsaid'])) {
    header('location:logout.php');
} else {

    // Function to approve worker request
    if (isset($_GET['approve'])) {
        $id = intval($_GET['approve']);
        
        // Fetch the worker request details before approving
        $sql = "SELECT * FROM tblworkerrequests WHERE ID = :ID";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ID', $id, PDO::PARAM_INT);
        $query->execute();
        $worker = $query->fetch(PDO::FETCH_OBJ);

        if ($worker) {
            // Insert approved worker details into tblperson
            $insertSql = "INSERT INTO tblperson (Category, Name, Picture, MobileNumber, Address, City) VALUES (:Category, :Name, :Picture, :MobileNumber, :Address, :City)";
            $insertQuery = $dbh->prepare($insertSql);
            $insertQuery->bindParam(':Category', $worker->Category, PDO::PARAM_STR);
            $insertQuery->bindParam(':Name', $worker->Name, PDO::PARAM_STR);
            $insertQuery->bindParam(':Picture', $worker->Picture, PDO::PARAM_STR);
            $insertQuery->bindParam(':MobileNumber', $worker->MobileNumber, PDO::PARAM_STR);
            $insertQuery->bindParam(':Address', $worker->Address, PDO::PARAM_STR);
            $insertQuery->bindParam(':City', $worker->City, PDO::PARAM_STR);
            $insertQuery->execute();
            
            // Update the status of the request
            $updateSql = "UPDATE tblworkerrequests SET Status = 'approved' WHERE ID = :ID";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':ID', $id, PDO::PARAM_INT);
            $updateQuery->execute();

            $_SESSION['msg'] = "Request approved successfully";
        } else {
            $_SESSION['msg'] = "Error: Worker request not found.";
        }
        
        header('location: pending-requests.php');
        exit;
    }

    // Function to reject worker request
    if (isset($_GET['reject'])) {
        $id = intval($_GET['reject']);
        
        // Delete the worker request from tblworkerrequests
        $sql = "DELETE FROM tblworkerrequests WHERE ID = :ID";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ID', $id, PDO::PARAM_INT);
        $query->execute();

        $_SESSION['msg'] = "Request rejected successfully";
        
        header('location: pending-requests.php');
        exit;
    }

    // Fetch all pending requests
    $sql = "SELECT * FROM tblworkerrequests WHERE Status = 'pending'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Worker Requests | Admin</title>
    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="path-to-your-css.css">
    <style>
        body {
            background-color: #f0f8ff; /* Light blue background color */
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff; /* White background for the container */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333333;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #dddddd;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            color: #ffffff;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .alert {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Pending Worker Registration Requests</h2>

        <!-- Display message for approval/rejection -->
        <?php if (isset($_SESSION['msg'])) { ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php } ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Picture</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                ?>
                        <tr>
                            <td><?php echo htmlentities($result->ID); ?></td>
                            <td><?php echo htmlentities($result->Category); ?></td>
                            <td><?php echo htmlentities($result->Name); ?></td>
                            <td><?php echo '<img src="' . htmlentities($result->Picture) . '" alt="Picture" style="width:50px;height:50px;">'; ?></td>
                            <td><?php echo htmlentities($result->MobileNumber); ?></td>
                            <td><?php echo htmlentities($result->Address); ?></td>
                            <td><?php echo htmlentities($result->City); ?></td>
                            <td>
                                <a href="pending-requests.php?approve=<?php echo $result->ID; ?>" class="btn btn-success">Approve</a>
                                <a href="pending-requests.php?reject=<?php echo $result->ID; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?');">Reject</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>No pending requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Include your JS files here -->
    <script src="path-to-your-js.js"></script>
</body>

</html>

<?php } ?>
