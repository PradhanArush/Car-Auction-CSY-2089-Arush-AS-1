<?php
include 'databaseconnection.php';

$e_l = '';
$n_e = '';
$e_rrs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $e_l = $_POST['email'] ?? '';
    $p_wd = $_POST['password'] ?? '';
    $n_e = $_POST['name'] ?? '';

    if (empty($e_l)) {
    } elseif (!filter_var($e_l, FILTER_VALIDATE_EMAIL)) {
        $e_rrs[] = 'Invalid Email';
    }
    
    if (empty($p_wd)) {
    } elseif (strlen($p_wd) < 7) {
        $e_rrs[] = 'Your Password should be minimum 7 letters.';
    }
    
    if (empty($e_rrs)) {
        $c_s_t_m = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $c_s_t_m->execute(['email' => $e_l]);
        
        if ($c_s_t_m->fetchColumn() > 0) {
            $e_rrs[] = 'Email address is already registered';
        }
    }
    
    if (empty($e_rrs)) {
        try {
            $h_pd = password_hash($p_wd, PASSWORD_DEFAULT);
            
            $s_t_m = $pdo->prepare('INSERT INTO users (email, password, name) VALUES (:email, :password, :name)');
            $s_t_m->execute([
                'email' => $e_l,
                'password' => $h_pd,
                'name' => $n_e
            ]);
            
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $e_rrs[] = 'Registration failed: ' . $e->getMessage();
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
        <li><a class="categoryLink" href="index.php">Home</a></li>
        <li><a class="categoryLink" href="login.php">Login</a></li>
        <li><a class="categoryLink" href="register.php">Register</a></li>
    </ul>
</nav>

<main>
    <h1>Register</h1>

    <?php if (!empty($e_rrs)): ?>
        <ul>
            <?php foreach ($e_rrs as $error): ?>
                <li><?php echo ($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Register">
    </form>

    <p>Have an Account ? <a href="login.php">Click to Login</a></p>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
