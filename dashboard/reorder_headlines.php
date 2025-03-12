<?php
// Minimal includes for your UI. Make sure these do not output any HTML in the POST branch.
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Query headlines (sort_order, title, etc.)
$sql = "SELECT h_id, title, sort_order FROM headline ORDER BY sort_order ASC, title ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Reorder Headlines</h1>
    <p>Drag and drop the headlines to reorder them. Click "Save Order" when finished.</p>
    
    <ul id="sortable" class="list-group">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li class="list-group-item" data-id="<?php echo $row['h_id']; ?>">
                <span class="badge  me-2"><?php echo $row['sort_order']; ?></span>
                <?php echo htmlspecialchars($row['title']); ?>
            </li>
        <?php endwhile; ?>
    </ul>
    
    <button id="saveOrder" class="btn btn-primary mt-3">Save Order</button>
</div>

<!-- Include SortableJS (or jQuery UI) for drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Wait for the DOM to be ready
document.addEventListener("DOMContentLoaded", function() {
    // Initialize SortableJS on the list
    var sortable = Sortable.create(document.getElementById("sortable"), {
        animation: 150,
        onEnd: function() {
            // Update badge numbers after reordering
            document.querySelectorAll("#sortable li").forEach(function(item, index) {
                var badge = item.querySelector(".badge");
                if (badge) {
                    badge.textContent = index + 1;
                }
            });
        }
    });

    // When "Save Order" is clicked, gather the new order and send it via Fetch
    document.getElementById("saveOrder").addEventListener("click", function() {
        var order = [];
        document.querySelectorAll("#sortable li").forEach(function(item, index) {
            var id = item.getAttribute("data-id");
            order.push({ id: id, order: index + 1 });
        });
        fetch("reorder_update.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Order saved successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            alert("AJAX error: " + error);
        });
    });
});
</script>

<?php
// Footer or any additional includes
require_once 'includes/footer.php';
?>
