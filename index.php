<?php
include "config/db.php";

// CREATE
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->close();
}

// SEARCH
$search = $_GET['search'] ?? '';
$limit_options = [5,10,15];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if (!in_array($limit, $limit_options)) $limit = 10;  

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// COUNT TOTAL USERS
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR email LIKE ?");
    $stmt->bind_param("ss", $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
$stmt->close();

// FETCH USERS  
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id ASC LIMIT ?, ?");
    $stmt->bind_param("ssii", $search_param, $search_param, $start, $limit);
} else {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY id ASC LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP CRUD</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >
</head>

<body>

<!-- ADD USER FORM (POST) -->




<!-- USER LIST -->
<div class="container">

    <h2>Add User</h2>
    <button>
        <a href="pages/calcu.php"> <i class="fa fa-calculator"></i> Calculator
</a>

    </button>
    
    <form method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <button name="save">Save</button>
</form>

    

    <!-- SEARCH FORM (GET) -->
    <form method="GET" style="margin-bottom:10px;">
                 <h2>User List</h2>

        <div class="labelCon">
            <label class="filter">Show
                <select class="filterNum" name="limit" onchange="this.form.submit()">
                    <option value="5" <?php if($limit==5) echo "selected"; ?>>5</option>
                    <option value="1" <?php if($limit==10) echo "selected"; ?>>10</option>
                    <option value="1" <?php if($limit==15) echo "selected"; ?>>15</option>
                </select>
                entries
            </label>
        </div>

        <input type="text" id="search" name="search" placeholder="Search name or email" value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
        <div id="suggestions"></div>
        <button>Search</button>
    </form>

    <!-- TABLE -->
    <table class="user-table">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <a href="components/edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="components/delete.php?id=<?php echo $row['id']; ?>" class="delete">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- PAGINATION -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>" 
               class="<?php if($i==$page) echo 'active'; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<script src="assets/script/main.js"></script>
</body>
</html>