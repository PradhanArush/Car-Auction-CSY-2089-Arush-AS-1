<?php
session_start();
require 'databaseconnection.php';

$i_l_i = isset($_SESSION['user_id']);

if (!$i_l_i) {
    header("Location: login.php");
    exit();
}

$e_r = '';
$s_s = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id']) && isset($_POST['new_name'])) {
    $c_g_id = $_POST['category_id'];
    $n_m = trim($_POST['new_name']);

    if ($n_m === '') {
        $e_r = "Category name cannot be empty.";
    } else {
        try {
            $s_t_m = $pdo->prepare("UPDATE category SET name = :name WHERE id = :id");
            $s_t_m->bindParam(':name', $n_m);
            $s_t_m->bindParam(':id', $c_g_id);
            $s_t_m->execute();
            $s_s = "Updated the category successfully!";
        } catch (PDOException $e) {
        }
    }
}

$c_t_gs = [];
try {
    $s_t_m = $pdo->query("SELECT * FROM category");
    $c_t_gs = $s_t_m->fetchAll();
} catch (PDOException $e) {
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
    <h1>Edit Categories</h1>

    <?php if ($e_r): ?>
        <p><?php echo ($e_r); ?></p>
    <?php elseif ($s_s): ?>
        <p><?php echo ($s_s); ?></p>
    <?php endif; ?>

    <?php foreach ($c_t_gs as $pct): ?>
        <form action="editCategory.php" method="POST">
            <input type="hidden" name="category_id" value="<?php echo ($pct['id']); ?>">
            <label>
                <strong><?php echo ($pct['name']); ?>:</strong>
            </label>
            <input type="text" name="new_name" value="<?php echo ($pct['name']); ?>" required>
            <input type="submit" value="Done">
        </form>
    <?php endforeach; ?>
    <button onclick="window.location.href='index.html'">Exit</button>
    </main>
</main> 

</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
