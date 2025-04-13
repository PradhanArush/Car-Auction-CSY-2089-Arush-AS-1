<?php
session_start();
require 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$er = '';
$scs = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $c_id = $_POST['category_id'];

    try {
        $c_s_m_t = $pdo->prepare("SELECT COUNT(*) FROM auction WHERE categoryId = :id");
        $c_s_m_t->bindParam(':id', $c_id);
        $c_s_m_t->execute();
        $a_c = $c_s_m_t->fetchColumn();

        if ($a_c > 0) {
            $er = "Cannot delete category with auctions listed.";
        } else {
            $s_m_t = $pdo->prepare("DELETE FROM category WHERE id = :id");
            $s_m_t->bindParam(':id', $c_id);
            $s_m_t->execute();
            $scs = "Deletion Complete.";
        }
    } catch (PDOException $e) {
    }
}

try {
    $s_m_t = $pdo->query("SELECT id, name FROM category");
    $sgis = $s_m_t->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Remove Category</h1>

    <?php if ($er): ?>
        <p><?php echo ($er); ?></p>
    <?php elseif ($scs): ?>
        <p><?php echo ($scs); ?></p>
    <?php endif; ?>

    <?php if (!empty($sgis)): ?>

            <?php foreach ($sgis as $sgi): ?>
                <li>
                    <?php echo ($sgi['name']); ?>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Remove Category?');">
                        <input type="hidden" name="category_id" value="<?php echo $sgi['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?> <br>
    <button onclick="window.location.href='index.html'">Exit</button>
    </main>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
