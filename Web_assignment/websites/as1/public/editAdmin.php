<?php
require 'databaseconnection.php';
session_start();

$ers = [];
$s_m = '';
$us = [];

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$s_t_m = $pdo->prepare('SELECT email FROM users WHERE id = :id');
$s_t_m->execute(['id' => $_SESSION['user_id']]);
$ce = $s_t_m->fetchColumn();

$s_t_m = $pdo->prepare('SELECT id FROM admins WHERE email = :email');
$s_t_m->execute(['email' => $ce]);
$iA = $s_t_m->fetch();

if (!$iA) {
    echo "Access denied. You are not an admin.";
    exit;
}

try {
    $s_t_m = $pdo->query("SELECT id, email, name FROM users ORDER BY id");
    $us = $s_t_m->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ers[] = "Error fetching users: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $u_iD = $_POST['user_id'];
    $nm = trim($_POST['new_name']);

    if (!empty($u_iD) && !empty($nm)) {
        try {
            $s_t_m = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
            $s_t_m->execute([':name' => $nm, ':id' => $u_iD]);

            if ($s_t_m->rowCount() > 0) {
                $s_m = "Username updated successfully!";
                $s_t_m = $pdo->query("SELECT id, email, name FROM users ORDER BY id");
                $us = $s_t_m->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $ers[] = "No changes made or user not found.";
            }
        } catch (PDOException $e) {
            $ers[] = "Error updating username: " . $e->getMessage();
        }
    } 
}
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>

<header>
    <h1>Car Buy</h1>
</header>

<main>
    <h2>Change Username</h2>

    <?php if (!empty($s_m)): ?>
        <p><?php echo ($s_m); ?></p>
    <?php endif; ?>

    <?php if (!empty($ers)): ?>
        <ul>
            <?php foreach ($ers as $error): ?>
                <li><?php echo ($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label for="user_id">User :</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Choose --</option>
            <?php foreach ($us as $user): ?>
                <option value="<?php echo $user['id']; ?>">
                    <?php echo ($user['email']) . ' (' . ($user['name']) . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_name" placeholder="Updated username" required>
        <button type="submit" name="update">Done</button>
    </form><br>

    <h2>Existing Users</h2><br>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($us as $user): ?>
                <tr>
                    <td><?php echo ($user['id']); ?></td>
                    <td><?php echo ($user['email']); ?></td>
                    <td><?php echo ($user['name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table> <br>
    <button onclick="window.location.href='index.html'">Exit</button>
    </main>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
