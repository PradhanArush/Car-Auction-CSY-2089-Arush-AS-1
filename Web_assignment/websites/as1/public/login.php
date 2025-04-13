<?php
session_start();

include 'databaseconnection.php'; 

$e_l = '';
$p_w_d = '';
$e_rr = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $e_l = $_POST['email'] ?? '';
    $p_w_d = $_POST['password'] ?? '';

    if (empty($e_rr)) {
        try {
            $s_t_m = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = :email');
            $s_t_m->execute(['email' => $e_l]);
            $s_r = $s_t_m->fetch(PDO::FETCH_ASSOC);
            
            if ($s_r && password_verify($p_w_d, $s_r['password'])) {

                $_SESSION['user_id'] = $s_r['id'];
                $_SESSION['user_name'] = $s_r['name'];
                
                $s_t_m = $pdo->prepare('SELECT id FROM admins WHERE email = :email');
                $s_t_m->execute(['email' => $s_r['email']]);
                $i_aa = $s_t_m->fetch();

                if ($i_aa) {
                    header('Location: adminCategories.php');
                    exit;
                } else {
                    header('Location: index.php');
                    exit;
                }
            } else {
                $e_rr[] = 'Email not registered.';
            }
        } catch (PDOException $e) {
            $e_rr[] = 'Login failed: ' . $e->getMessage();
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
    <h1>Carbuy Auctions</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    </ul>
</nav>

<main>
    <h1>Login</h1>

    <?php if (!empty($e_rr)): ?>
        <ul>
            <?php foreach ($e_rr as $error): ?>
                <li><?php echo ($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
    </form>

    <p>Make an account? <a href="register.php">Click here to register!</a></p>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
