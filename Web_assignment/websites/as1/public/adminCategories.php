<?php
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>

<header>
    <h1>Admin Panel</h1>
</header>

<main id="a-c">
    <h2>Category Control</h2><br>
    <button onclick="window.location.href='addCategory.php'">New Category</button>
    <button onclick="window.location.href='editCategory.php'">Modify Category</button>
    <button onclick="window.location.href='deleteCategory.php'">Remove Category</button><br><br><br>

    <h2>Administrator Control</h2><br>
    <button onclick="window.location.href='addAdmin.php'">New Admin</button>
    <button onclick="window.location.href='manageAdmins.php'">Manage Admins</button>
    <button onclick="window.location.href='editAdmin.php'">Edit Admin</button>

    <br><br><br>
    <button onclick="window.location.href='index.html'">Exit</button>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
