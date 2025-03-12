<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_list_id = mysqli_real_escape_string($conn, $_POST["list_id"]);

    // Debugging: Check if List ID is received
    echo "Selected List ID: " . htmlspecialchars($old_list_id) . "<br>";

    // Corrected SQL Query
    $sql = "SELECT * FROM grocery_items WHERE list_id = '$old_list_id' AND in_stock = 1";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn)); // Show SQL errors
    }

    // Check if items exist
    if (mysqli_num_rows($result) > 1) {
        echo "<h2>Items Available  in Selected List:</h2>";
        echo "<div style='display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;'>";
    
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; border-radius: 8px; background: white; text-align: center; width: 200px;'>";
            echo "<img src='uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['item_name']) . "' style='width: 100px; height: 100px; object-fit: cover; border-radius: 5px;'><br>";
            echo "<strong>" . htmlspecialchars($row['item_name']) . "</strong><br>";
            echo "Category: " . htmlspecialchars($row['category']) . "<br>";
            echo "Price: $" . number_format($row['price'], 2) . "<br>";
            echo "</div>";
        }
    
        echo "</div>";
     } else {
         echo "<p style='color: green;'>Items Available  in Selected List:</p>";
    }
    
}


  
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Grocery List</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px;
            font-size: 22px;
            font-weight: bold;
        }

        /* Container Styling */
        .container {
            background: white;
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        /* Dropdown Styling */
        .dropdown {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Button Styling */
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
            
            .dropdown {
                width: 100%;
            }
        }

    </style>
</head>
<body>
    <!-- Sidebar -->
<nav style="
    width: 250px;
    height: 100vh;
    background: #2c3e50;
    color: white;
    padding: 20px;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    text-align: left;
">
    <h2 style="display: flex; align-items: center; gap: 10px; text-align: left;">
        <img src="store.png" alt="Grocery Cart Icon" style="width: 40px; height: 40px;">
        Grocery List
    </h2>
    <ul style="list-style: none; padding: 0; margin-top: 20px;">
        <li style="padding: 10px;">
            <a href="index.php" style="color: white; text-decoration: none; display: block;">Home</a>
        </li>
       
        <li style="padding: 10px;">
            <a href="add_item.php" style="color: white; text-decoration: none; display: block;">Add Item</a>
        </li>
        <li style="padding: 10px;">
            <a href="logout.php" style="color: white; text-decoration: none; display: block;">Logout</a>
        </li>
    </ul>
    
</nav>

<h2 style="text-align: center; color: #333; margin-bottom: 20px;">Check Previous Grocery Lists</h2>
<form action="check_list.php" method="POST" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); width: 350px; margin: auto;">
    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Select a previous list:</label>
    <select name="list_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; margin-bottom: 15px;">
        <option value="">Select a previous list</option>
        <?php
        $query = "SELECT DISTINCT item_name FROM grocery_items";
        $lists = mysqli_query($conn, $query);
        while ($list = mysqli_fetch_assoc($lists)) {
            echo "<option value='" . $list['item_name'] . "'>: " . $list['item_name'] . "</option>";
        }
        ?>
    </select>
    <button type="submit" style="width: 100%; padding: 10px; border: none; background-color: #28a745; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; transition: 0.3s;">Check Availability</button>
</form>


</body>
</html>
