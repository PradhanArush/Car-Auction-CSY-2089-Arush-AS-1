<?php
session_start();
require 'databaseconnection.php';

$i_l_ = isset($_SESSION['user_id']);
$u_s_n = $i_l_ ? $_SESSION['user_name'] : '';

$c_t_y = isset($_GET['category']) ? $_GET['category'] : 'all';

$s_t_t = isset($_GET['search']) ? $_GET['search'] : '';

$q_ry = "SELECT a.id, a.title, a.description, a.endDate, a.image, a.userId, c.name AS category 
          FROM auction a 
          JOIN category c ON a.categoryId = c.id";

if ($c_t_y != 'all') {
    $q_ry .= " WHERE c.name = :category";
}

if ($s_t_t != '') {
    $q_ry .= $c_t_y == 'all' ? " WHERE" : " AND";
    $q_ry .= " (a.title LIKE :searchTerm OR a.description LIKE :searchTerm)";
}

$q_ry .= " ORDER BY a.endDate DESC";

try {
    $s_t_m = $pdo->prepare($q_ry);
    if ($c_t_y != 'all') {
        $s_t_m->bindParam(':category', $c_t_y);
    }
    if ($s_t_t != '') {
        $searchTermWithWildcards = "%" . $s_t_t . "%"; 
        $s_t_m->bindParam(':searchTerm', $searchTermWithWildcards);
    }

    $s_t_m->execute();
    $a_ns = $s_t_m->fetchAll();

    $c_t_y_s = $pdo->query("SELECT name FROM category");
    $ct_g_es = $c_t_y_s->fetchAll(PDO::FETCH_ASSOC);

    $b_s_t_m = $pdo->prepare("SELECT auctionId, MAX(amount) AS currentBid FROM bid GROUP BY auctionId");
    $b_s_t_m->execute();
    $c_b = $b_s_t_m->fetchAll(PDO::FETCH_ASSOC);
    $c_b_d = [];
    foreach ($c_b as $bid) {
        $c_b_d[$bid['auctionId']] = $bid['currentBid'];
    }

} catch (PDOException $e) {
    $a_ns = [];
    $ct_g_es = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<header>
    <h1>
        <span class="C">C</span>
        <span class="a">a</span>
        <span class="r">r</span>
        <span class="b">b</span>
        <span class="u">u</span>
        <span class="y">y</span>
    </h1>
    <form action="#" method="GET">
        <input type="text" name="search" value="<?php echo ($s_t_t); ?>" placeholder="Search for a car" />
        <input type="submit" name="submit" value="Search" />
    </form>
</header>

<nav>
    <ul>
        <li><a href="?category=all">All</a></li>
        <?php foreach ($ct_g_es as $cat): ?>
            <li><a href="?category=<?php echo urlencode($cat['name']); ?>"><?php echo ($cat['name']); ?></a></li>
        <?php endforeach; ?>

        <?php if ($i_l_): ?>
            <li><a href="addAuction.php">Add Auction</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<img src="banners/1.jpg" alt="Banner" />

<main>
    <p><?php echo $i_l_ ? "Hello, $u_s_n! You are logged in." : "Please log in or register to continue."; ?></p>
    
    <h1>Latest Car Listings</h1>

    <ul class="carList">
        <?php foreach ($a_ns as $auction): ?>
        <li>
            <img src="<?php echo ($auction['image']); ?>" alt="Auction Image" />
            <article>
                <h2><?php echo ($auction['title']); ?></h2>
                <h3><?php echo ($auction['category']); ?></h3>
                <p><?php echo ($auction['description']); ?></p>
                <p class="price">
                    Current bid: £<?php 
                    echo isset($c_b_d[$auction['id']]) ? number_format($c_b_d[$auction['id']], 2) : 'No bids yet'; 
                    ?>
                </p>
                <a href="review.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">More &gt;&gt;</a>

                <?php if ($i_l_): ?> 
                    <?php if ($_SESSION['user_id'] == $auction['userId']): ?>
                        <form action="editAuction.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $auction['id']; ?>">
                            <input type="submit" value="Edit"  />
                        </form>
                    <?php else: ?>
                        <button disabled title>You can not edit this auction.</button>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        </li>
        <?php endforeach; ?>
    </ul>
</main>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</body>
</html>
