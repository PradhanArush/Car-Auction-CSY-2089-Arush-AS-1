<?php
session_start();
include 'databaseconnection.php';

$e_r = [];
$s_s = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$s_t_m = $pdo->prepare('SELECT email FROM users WHERE id = :id');
$s_t_m->execute(['id' => $_SESSION['user_id']]);
$e_e = $s_t_m->fetchColumn();

$s_t_m = $pdo->prepare('SELECT id FROM admins WHERE email = :email');
$s_t_m->execute(['email' => $e_e]);
$i_a = $s_t_m->fetch();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n_a_e = $_POST['admin_email'] ?? '';

    if (empty($n_a_e) || !filter_var($n_a_e, FILTER_VALIDATE_EMAIL)) {
        $e_r[] = 'Enter a authentic email address.';
    } else {
        try {
            $s_t_m = $pdo->prepare('INSERT INTO admins (email) VALUES (:email)');
            $s_t_m->execute(['email' => $n_a_e]);
            $s_s = 'New admin added!';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $e_r[] = 'User is already admin.';
            } else {
                $e_r[] = 'Error: ' . $e->getMessage();
            }
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
    <h2>Add Admin</h2> <br><br>

    <?php if (!empty($s_s)): ?>
        <p><?php echo ($s_s); ?></p>
    <?php endif; ?>

    <?php if (!empty($e_r)): ?>
        <ul>
            <?php foreach ($e_r as $error): ?>
                <li><?php echo ($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="admin_email">Admin Email:</label>
        <input type="email" name="admin_email" required>
        <input type="submit" value="Add Admin">
    </form>
    <button onclick="window.location.href='index.html'">Exit</button>
    </main>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
