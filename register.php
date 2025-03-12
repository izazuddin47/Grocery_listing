<?php
include "connect.php"; // Ensure this file correctly initializes $conn

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $errors = [];

    // Validate Password Match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Password Length Validation
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Email Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // Check if Email Exists
    $email_query = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $email_query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email already exists.";
    }

    // If No Errors, Insert Data
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

        if (mysqli_query($conn, $insert_query)) {
            echo "<p style='color: green;'>User created successfully.</p>";
            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        // Display Errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 300px; text-align: center;">
        <h2 style="color: #333;">Register</h2>
        <form action="" method="POST">
            <input type="text" name="name" placeholder="Full Name" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
            <input type="email" name="email" placeholder="Email" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
            <button type="submit" name="register" style="background-color: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer;">Register</button>
            <a href="login.php" style="display: block; text-align: center; background-color: #007bff; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">login</a>
        </form>
    </div>
</body>
</html>
