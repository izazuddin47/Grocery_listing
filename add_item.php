<?php
include "connect.php"; // Database connection file

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate input fields
    $item_name = isset($_POST["itemName"]) ? mysqli_real_escape_string($conn, ($_POST["itemName"])) : "";
    $category = isset($_POST["category"]) ? mysqli_real_escape_string($conn, ($_POST["category"])) : "";
    $other_category = isset($_POST["otherCategory"]) ? mysqli_real_escape_string($conn, ($_POST["otherCategory"])) : NULL;
    $quantity = isset($_POST["quantity"]) ? (int) $_POST["quantity"] : 0;
    $price = isset($_POST["price"]) ? (float) $_POST["price"] : 0.0;

    // Handle checkbox selections
    $availability = isset($_POST["availability"]) && is_array($_POST["availability"]) 
        ? implode(", ", array_map('trim', $_POST["availability"])) 
        : "Out of Stock";

    // Handle Image Upload
    $target_dir = "uploads/";
    $new_image_name = NULL; // Default value if no file uploaded

    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES["file"]["name"]);
        $image_tmp_name = $_FILES["file"]["tmp_name"];
        $image_size = $_FILES["file"]["size"];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Allowed file types for security
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (in_array($image_ext, $allowed_extensions) && $image_size > 0) {
            $new_image_name = time() . "_" . $image_name; // Rename image to prevent conflicts
            $target_file = $target_dir . $new_image_name;
            move_uploaded_file($image_tmp_name, $target_file);
        }
    }

    // Insert Data into Database
    $sql = "INSERT INTO grocery_items (item_name, category, other_category, quantity, price, image, availability) 
            VALUES ('$item_name', '$category', '$other_category', '$quantity', '$price', '$new_image_name', '$availability')";

 

if (mysqli_query($conn, $sql)) {
    echo "<p style='color:green;'>Grocery item added successfully!</p>";
    header("Location: index.php"); // Redirect to prevent form resubmission
    exit(); 
} else {
    echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
}// Redirect back to home

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grocery Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .checkbox-group {
            margin-top: 10px;
        }
        .checkbox-group label {
            font-weight: normal;
            display: inline-block;
            margin-right: 10px;
        }
        .hidden {
            display: none;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1a252f;
        }
       
    </style>
</head>
<div style="
    position: absolute;
    top: -5px;
    left: 292px;
    width: calc(100% - 250px);
    padding: 0;
    background: white;
">
    <h1>Welcome to Grocery List</h1>
</div>

<body style="margin: 0; font-family: Arial, sans-serif; display: flex;">

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
">
    <h2 style="display: flex; align-items: center; gap: 10px; text-align: center;">
        <img src="store.png" alt="Grocery Cart Icon" style="width: 40px; height: 40px;">
        Grocery List
    </h2>
    <ul style="list-style: none; padding: 0; margin-top: 20px;">
        <li style="padding: 10px;">
            <a href="index.php" style="color: white; text-decoration: none; display: block;">Home</a>
        </li>
       
        <li style="padding: 10px;">
            <a href="check_list.php" style="color: white; text-decoration: none; display: block;">Check items</a>
        </li>
        <li style="padding: 10px;">
            <a href="logout.php" style="color: white; text-decoration: none; display: block;">Logout</a>
        </li>
    </ul>
    
</nav>

<!-- Main Content -->





    <div class="form-container">
        <h2>Add Grocery Item</h2>
        <form id="groceryForm" action = "" method ="POST" style="margin-top:10px"  enctype="multipart/form-data">
            <label for="itemName">Item Name:</label>
            <input type="text" id="itemName" name="itemName" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required onchange="toggleOtherCategory()">
                <option value="">Select Category</option>
                <option value="vegetables">Vegetables</option>
                <option value="fruits">Fruits</option>
                <option value="dairy">Dairy</option>
                <option value="beverages">Beverages</option>
                <option value="snacks">Snacks</option>
                <option value="other">Other</option>
            </select>

            <div id="otherCategoryDiv" class="hidden">
                <label for="otherCategory">Other Category:</label>
                <input type="text" id="otherCategory" name="otherCategory">
            </div>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" min="0.01" step="0.01" required>

            <label for="file">Upload Image:</label>
            <input type="file" id="file" name="file" >

            <div class="checkbox-group">
                <label>Availability:</label>
                <input type="checkbox" id="inStock" name="availability" value="In Stock"> <label for="inStock">In Stock</label>
                <input type="checkbox" id="outOfStock" name="availability" value="Out of Stock"> <label for="outOfStock">Out of Stock</label>
            </div>

            <button type="submit">Add Item</button>
        </form>
    </div>

    <script>
        function toggleOtherCategory() {
            var categorySelect = document.getElementById("category");
            var otherCategoryDiv = document.getElementById("otherCategoryDiv");

            if (categorySelect.value === "other") {
                otherCategoryDiv.classList.remove("hidden");
            } else {
                otherCategoryDiv.classList.add("hidden");
                document.getElementById("otherCategory").value = ""; // Clear input if hidden
            }
        }
    </script>

</body>
</html>