<?php
session_start();
include 'databaseconnection.php';

$e_rs = [];



$s_t_m = $pdo->prepare('SELECT email FROM users WHERE id = :id');
$s_t_m->execute(['id' => $_SESSION['user_id']]);
$e_a_c_t = $s_t_m->fetchColumn();

$s_t_m = $pdo->prepare('SELECT id FROM admins WHERE email = :email');
$s_t_m->execute(['email' => $e_a_c_t]);
$a_t_i = $s_t_m->fetch();

if (isset($_GET['delete_email'])) {
    $d_e = $_GET['delete_email'];

    try {
        $s_t_m = $pdo->prepare('DELETE FROM admins WHERE email = :email');
        $s_t_m->execute(['email' => $d_e]);
        $s_mm = "Admin with email '$d_e' removed successfully.";
    } catch (PDOException $e) {
        $e_rs[] = 'Error: ' . $e->getMessage();
    }
}

$s_t_m = $pdo->prepare('SELECT u.id, u.name, a.email FROM admins a JOIN users u ON a.email = u.email');
$s_t_m->execute();
$a_d_s = $s_t_m->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>

<header>
    <h1>Car Buy</h1>
</header>

<main>
    <h2>List of All Admins</h2>

    <?php if (isset($s_mm)): ?>
        <p><?php echo ($s_mm); ?></p>
    <?php endif; ?>

    <?php if (!empty($e_rs)): ?>
        <ul>
            <?php foreach ($e_rs as $error): ?>
                <li><?php echo ($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="addAdmin.php">
        <button>Add Admin</button>
    </a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($a_d_s)): ?>
                <?php foreach ($a_d_s as $admin): ?>
                    <tr>
                        <td><?php echo ($admin['name']); ?></td>
                        <td><?php echo ($admin['email']); ?></td>
                        <td>
                            <a href="?delete_email=<?php echo ($admin['email']); ?>" onclick="return confirm('Delete Admin?');">
                                <button>Remove</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
