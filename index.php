<?php
include "connect.php";

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

// Fetch latest grocery items (for cards)
$sql_items = "SELECT id, item_name, category, image, price FROM grocery_items ORDER BY created_at DESC LIMIT 8";
$result_items = mysqli_query($conn, $sql_items);

// Fetch all categories for search dropdown
$sql_categories = "SELECT DISTINCT category FROM grocery_items";
$result_categories = mysqli_query($conn, $sql_categories);

// Fetch previous grocery lists
$sql_lists = "SELECT id, item_name FROM grocery_items ORDER BY created_at DESC";
$result_lists = mysqli_query($conn, $sql_lists);

// $sql = "DELETE FROM grocery_items WHERE id = $id";
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Prevent SQL injection

    // Delete query
    $delete_query = "DELETE FROM grocery_items WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Item deleted successfully!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error deleting item: " . mysqli_error($conn) . "');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px 20px;
            color: white;
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .navbar ul li a:hover {
            background-color: #555;
        }

        #searchBox {
            padding: 5px;
            border-radius: 5px;
            border: none;
        }

        #darkModeToggle {
            background-color: #444;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #darkModeToggle:hover {
            background-color: #666;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
        }

        .card img {
            width: 100%;
            border-radius: 5px;
        }
        .btn-edit, .btn-delete {
                display: inline-block;
                padding: 8px 12px;
                margin: 5px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                text-align: center;
            }

            .btn-edit {
                background-color: #007bff;
                color: white;
            }

            .btn-delete {
                background-color: #dc3545;
                color: white;
            }

            .btn-edit:hover {
                background-color: #0056b3;
            }

            .btn-delete:hover {
                background-color: #c82333;
            }

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-left">
        <img src="https://media.istockphoto.com/id/1127711786/vector/supermarket-cart-icon-vector-on-white-background-supermarket-ca.jpg?s=1024x1024&w=is&k=20&c=1IVZ3f_recQ4yX_U5Ix9GxELI4AJ7OsxSR2hC0s7hls=" 
             alt="Grocery Cart Icon" 
             class="nav-icon">
        <h2>Grocery List</h2>
    </div>

    <div class="nav-right">
        <p><?php echo isset($_SESSION["user_name"]) ? "Welcome, " . $_SESSION["user_name"] : "Guest"; ?></p>
        
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="add_item.php">Add Item</a></li>
            <li><a href="check_list.php">Check list</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <!-- Search Bar with Dropdown -->
        <select id="searchBox">
            <option value="">Search by Category</option>
            <?php while ($row = mysqli_fetch_assoc($result_categories)) { ?>
                <option value="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></option>
            <?php } ?>
        </select>

        <button id="darkModeToggle">Dark Mode</button>
    </div>
</nav>

<!-- Image Carousel -->


<!-- Grocery Items Cards -->
<div class="cards-container">
    <?php
    if (mysqli_num_rows($result_items) > 0) {
        while ($row = mysqli_fetch_assoc($result_items)) {
            ?>
            <div class="card" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Grocery Item">
                <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                <p>Price: $<?php echo number_format($row['price'], 2); ?></p>

                <!-- Edit Button -->
                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                
                <!-- Delete Button -->
                <form action="delete_item.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <button type="submit" class="btn-delete">Delete</button>
</form>

            </div>
            <?php
        }
    } else {
        echo "<p>No grocery items found.</p>";
    }
    ?>
</div>


<!-- Previous Grocery Lists -->
<!-- <h2>Previous Grocery Lists</h2>
<form action="reuse_list.php" method="POST">
    <select name="list_id" required>
        <option value="">Select a previous list</option>
        <?php while ($row = mysqli_fetch_assoc($result_lists)) { ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['item_name']; ?></option>
        <?php } ?>
    </select>
    <button type="submit">Reuse List</button>
</form> -->

<script>
    // Dark Mode Toggle
    document.getElementById('darkModeToggle').addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });

    // Carousel Slideshow
    let index = 0;
    function showNextImage() {
        const images = document.querySelectorAll('.carousel-image');
        images[index].style.display = 'none';
        index = (index + 1) % images.length;
        images[index].style.display = 'block';
    }
    setInterval(showNextImage, 3000);

    // Search Functionality
    document.getElementById("searchBox").addEventListener("change", function () {
        let selectedCategory = this.value.toLowerCase();
        let items = document.querySelectorAll(".card");

        items.forEach(item => {
            let category = item.getAttribute("data-category").toLowerCase();
            if (category.includes(selectedCategory) || selectedCategory === "") {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    });
</script>

</body>
</html>