 <?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Ensure ID is numeric to prevent SQL injection
    if (!is_numeric($id)) {
        die("Invalid ID.");
    }

    // Delete item from database
    $sql = "DELETE FROM grocery_items WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php"); // Redirect to the main page
        exit();
    } else {
        echo "Error deleting item: " . mysqli_error($conn);
    }
}
?> 
