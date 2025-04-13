<?php
session_start();
require 'databaseconnection.php';

$is_ln = isset($_SESSION['user_id']);
$usn = $is_ln ? $_SESSION['user_name'] : '';

$e_r = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnm = trim($_POST['categoryName']);

    if (empty($cnm)) {
        $e_r = "Name of the category cannot be empty.";
    } else {
        try {
            $s_t_m = $pdo->prepare("INSERT INTO category (name) VALUES (:name)");
            $s_t_m->bindParam(':name', $cnm);
            $s_t_m->execute();

            header("Location: index.php");
            exit();
        } catch (PDOException $est) {
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>

<header>
    <h1>Car Buy</h1>
</header>
<main>
    <h1>New Category</h1>
    <?php if ($e_r): ?>
        <p><?php echo($e_r); ?></p>
    <?php endif; ?>
    <form action="addCategory.php" method="POST">
        <label for="categoryName">Name:</label>
        <input type="text" id="categoryName" name="categoryName" required />
        <input type="submit" value="Add Category" />
    </form>
    <button onclick="window.location.href='index.html'">Exit</button>
    </main>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
