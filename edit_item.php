<?php
include 'connect.php';

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request!");
}

$item_id = $_GET['id'];

// Fetch item detail
$sql = "SELECT * FROM grocery_items WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    die("Item not found!");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    $update_sql = "UPDATE grocery_items SET item_name = ?, category = ?, price = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ssdi", $item_name, $category, $price, $item_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Item updated successfully!'); window.location='index.php';</script>";
    } else {
        echo "Error updating item: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            border: none;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Grocery Item</h2>
    <form action="" method="POST">
        <label>Item Name:</label>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
        
        <label for="category">Category:</label>
            <select id="category" name="category" required onchange="toggleOtherCategory()">
                <option value="">Select Category</option>
                <option value="vegetables">Vegetables</option>
                <option value="fruits">Fruits</option>
                <option value="dairy">Dairy</option>
                <option value="beverages">Beverages</option>
                <option value="snacks">Snacks</option>
            </select>
        <input type="text" name="category" value="<?php echo htmlspecialchars($item['category']); ?>" required>
        
        <label>Price:</label>
        <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>" required>
        
        <button type="submit">Update Item</button>
    </form>
</div>

<script>
        function toggleOtherCategory() {
            var categorySelect = document.getElementById("category");
            var otherCategoryDiv = document.getElementById("otherCategoryDiv");

          
        }
    </script>
</body>
</html>