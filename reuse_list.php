<?php
include "connect.php";

// Get previous grocery lists
$sql = "SELECT DISTINCT list_id, item_name FROM grocery_items ORDER BY list_id DESC";
$result_lists = mysqli_query($conn, $sql);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["list_id"])) {
    $old_list_id = $_POST["list_id"];
    $new_list_name = "Grocery List - " . date("F Y");

    // Create a new list
    $sql = "INSERT INTO grocery_items (item_name, created_at) VALUES ('$new_list_name', NOW())";
    mysqli_query($conn, $sql);
    $new_list_id = mysqli_insert_id($conn); // Get new list ID

    // Copy all items from old list to new list
    $sql = "INSERT INTO grocery_items (item_name, category, price, list_id, in_stock)
            SELECT item_name, category, price, '$new_list_id', in_stock FROM grocery_items WHERE list_id = '$old_list_id'";
    mysqli_query($conn, $sql);

    header("Location: check_list.php");
    exit();
}
?>

<h2>Reuse Previous Grocery List</h2>
<form id="reuseListForm" method="POST">
    <select name="list_id" id="listDropdown" required>
        <option value="">Select a previous list</option>
        <?php while ($row = mysqli_fetch_assoc($result_lists)) { ?>
            <option value="<?php echo $row['list_id']; ?>">
                <?php echo $row['item_name']; ?>
            </option>
        <?php } ?>
    </select>
    <button  type="submit">Check Availability</button>
</form>

<p id="listStatus"></p> <!-- Show availability message -->
<div id="itemList"></div> <!-- Show the list of items -->

<script>
document.getElementById("reuseListForm").addEventListener("submit", function (e) {
    e.preventDefault();
    let listId = document.getElementById("listDropdown").value;

    if (listId !== "") {
        // Send request to check availability
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "check_list.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let response = JSON.parse(xhr.responseText);
                let statusMessage = document.getElementById("listStatus");
                let itemListDiv = document.getElementById("itemList");
                itemListDiv.innerHTML = ""; // Clear previous list

                if (response.status === "available") {
                    statusMessage.textContent = "✅ Items are available!";
                    statusMessage.style.color = "green";

                    // Show items
                    response.items.forEach(function (item) {
                        let itemElement = document.createElement("p");
                        itemElement.textContent = item.name + " (Category: " + item.category + ") - " + item.stock_status;
                        itemListDiv.appendChild(itemElement);
                    });

                } else {
                    statusMessage.textContent = "❌ Items are NOT available.";
                    statusMessage.style.color = "red";
                }
            }
        };
        xhr.send("list_id=" + listId);
    }
});
</script>
