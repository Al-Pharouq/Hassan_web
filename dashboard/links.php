<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Set a template id if needed; for links we use a fixed value, e.g. 4.
$temp_id = 4;

// Determine the action from the URL (default to "list")
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// For edit and delete actions, retrieve the resource id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'add') {
    // Process the add form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $link = mysqli_real_escape_string($conn, $_POST['link']);
        
        $sql = "INSERT INTO resources (name, link) VALUES ('$name', '$link')";
        if (mysqli_query($conn, $sql)) {
            header("Location: links.php?action=list");
            exit;
        } else {
            echo "<p>Error adding resource: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Add New Resource</h1>
        <form method="post" action="links.php?action=add">
            <div class="form-group">
                <label for="name">Resource Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter resource name" required>
            </div>
            <div class="form-group">
                <label for="link">Resource Link</label>
                <input type="url" name="link" id="link" class="form-control" placeholder="Enter resource link" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Resource</button>
        </form>
    </div>
    <?php
} elseif ($action == 'edit' && $id > 0) {
    // Process edit form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $link = mysqli_real_escape_string($conn, $_POST['link']);
        
        $sql = "UPDATE links SET name = '$name', link = '$link' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            header("Location: links.php?action=list");
            exit;
        } else {
            echo "<p>Error updating resource: " . mysqli_error($conn) . "</p>";
        }
    } else {
        // Retrieve resource details to pre-fill the edit form
        $sql = "SELECT * FROM resources WHERE id = $id LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $name = $row['name'];
            $link = $row['link'];
        } else {
            echo "<p>Resource not found.</p>";
            require_once 'includes/footer.php';
            exit;
        }
    }
    ?>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit Resource</h1>
        <form method="post" action="links.php?action=edit&id=<?php echo $id; ?>">
            <div class="form-group">
                <label for="name">Resource Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="link">Resource Link</label>
                <input type="url" name="link" id="link" class="form-control" value="<?php echo htmlspecialchars($link); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Resource</button>
        </form>
    </div>
    <?php
} elseif ($action == 'delete' && $id > 0) {
    // Process deletion with confirmation
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = "DELETE FROM resources WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            header("Location: links.php?action=list");
            exit;
        } else {
            echo "<p>Error deleting resource: " . mysqli_error($conn) . "</p>";
        }
    } else {
        // Show confirmation form
        ?>
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Delete Resource</h1>
            <p>Are you sure you want to delete this resource?</p>
            <form method="post" action="links.php?action=delete&id=<?php echo $id; ?>">
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                <a href="links.php?action=list" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
        <?php
    }
} else {
    // Default: list links
    $sql = "SELECT * FROM resources";
    $exe = mysqli_query($conn, $sql);
    if (!$exe) {
        die("Error: " . mysqli_error($conn));
    }
    ?>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Resources</h1>
            <a href="links.php?action=add" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fa-solid fa-plus fa-sm text-white-50"></i> Add Resource
            </a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Resources List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Resource Name</th>
                                <th>Resource Link</th>
                                <th>Delete</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (mysqli_num_rows($exe) > 0) {
                            while ($row = mysqli_fetch_assoc($exe)) {
                                $res_id = $row['id'];
                                $name = $row['name'];
                                $link = $row['link'];
                                echo '<tr>
                                    <td>' . htmlspecialchars($name) . '</td>
                                    <td>' . htmlspecialchars($link) . '</td>
                                    <td><a href="links.php?action=delete&id=' . urlencode($res_id) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this resource?\')"><i class="fa-solid fa-delete-left"></i> Delete</a></td>
                                    <td><a href="links.php?action=edit&id=' . urlencode($res_id) . '" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i> Edit</a></td>
                                </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No links available at the moment.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
}
require_once 'includes/footer.php';
?>
