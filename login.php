<?php

include "connect.php";

$error_message = ""; // Variable to store error messages

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // No need to escape, it's not used in query directly

    // Fetch user details using email
    $query = "SELECT id, name, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        
        // Verify hashed password
        if(password_verify($password, $user['password'])){
            $_SESSION['name'] = $email;
            $_SESSION["user_name"] = $user["name"];
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Invalid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">

    <!-- Display error message at the top -->
    <?php if (!empty($error_message)) { ?>
        <div style="background-color: #ffdddd; color: #d8000c; padding: 10px; border-radius: 5px; width: 300px; text-align: center; margin-bottom: 10px;">
            <?php echo $error_message; ?>
        </div>
    <?php } ?>

    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 300px; text-align: center;">
        <h2 style="color: #333;">Login</h2>
        <form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
    <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
    
    <button type="submit" name="login" style="background-color: rgb(44, 161, 74); color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; margin-bottom: 10px;">Login</button>
    
    <a href="register.php" style="display: block; text-align: center; background-color: #007bff; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">Register</a>
</form>

    </div>

</body>
</html>
