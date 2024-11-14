<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phonenumber = $_POST['phone_number'];  
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $shippingadress = $_POST['shipping_address']; 
	$confirm_password = $_POST['confirm_password']; 

    // Hash the password
	if ($password !== $confirm_password) {
		
		$error = "Password does not match";
		echo $error;			
		exit();
		
	} else {
	$hashed_password = password_hash($password, PASSWORD_BCRYPT);	
		
	}
    
    // Database credentials
    $host = 'localhost';  
    $db   = 'honig_shop';      
    $user = 'root';       
    $pass = '';           

    // Create a connection to the MySQL database
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Check the connection
    if ($mysqli->connect_error) {
        die('Connection failed: ' . $mysqli->connect_error);
    }

    // Check if the email already exists
    $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If email exists
    if ($stmt->num_rows > 0) {
        $error = "Email already registered!";
		echo $error;
		
    } else {
        // Insert the new user into the database
        $stmt = $mysqli->prepare("INSERT INTO users (email, first_name, last_name, phone_number, shipping_address, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $firstname, $lastname, $phonenumber, $shippingadress, $hashed_password);
        $stmt->execute();

        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            // Registration successful, redirect to login page with a success message
            $_SESSION['register_success'] = "Registration successful! You can now log in.";
            header('Location: login.html');
            exit();
        } else {
            $error = "Error saving data: " . $stmt->error;

			echo "Error saving data";
        }
    }


    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
}
?>
