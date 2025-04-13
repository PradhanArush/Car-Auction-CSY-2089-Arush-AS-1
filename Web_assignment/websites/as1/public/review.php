<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'databaseconnection.php';

$i_l_i = isset($_SESSION['user_id']);
$u_n = $i_l_i ? $_SESSION['user_name'] : '';

$a_i = isset($_GET['id']) ? $_GET['id'] : null;
$a_n = null;
$r_vw = [];
$ee_r = null;

if ($a_i) {
    try {
        $s_t_m = $pdo->prepare("SELECT a.id, a.title, a.description, a.endDate, a.image, c.name AS category, a.userId
                               FROM auction a
                               JOIN category c ON a.categoryId = c.id 
                               WHERE a.id = :id");
        $s_t_m->execute(['id' => $a_i]);
        $a_n = $s_t_m->fetch();

        if ($a_n) {
            $r_s_t_m = $pdo->prepare("SELECT r.reviewText AS comment, u.name, r.created AS created
                                         FROM review r
                                         JOIN users u ON r.userId = u.id
                                         WHERE r.auctionId = :auctionId
                                         ORDER BY r.created DESC");
            $r_s_t_m->execute(['auctionId' => $a_i]);
            $r_vw = $r_s_t_m->fetchAll();
        }
    } catch (PDOException $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review']) && $i_l_i) {
    $c_m_t = $_POST['comment'] ?? '';

    if ($a_n && $_SESSION['user_id'] == $a_n['userId']) {
        $ee_r = "You cannot review your own auction.";
    } elseif (!empty($c_m_t)) {
        try {
            $s_t_m = $pdo->prepare("INSERT INTO review (auctionId, userId, reviewText, sellerId)
                                   VALUES (:auctionId, :userId, :reviewText, :sellerId)");
            $s_t_m->execute([ 
                'auctionId' => $a_i,
                'userId' => $_SESSION['user_id'],
                'reviewText' => $c_m_t,
                'sellerId' => $a_n['userId']
            ]);
            header("Location: review.php?id=$a_i");
            exit;
        } catch (PDOException $e) {
        }
    } else {
        $ee_r = "Review comment cannot be empty.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_bid']) && $i_l_i) {
    $b_am_t = $_POST['bid_amount'] ?? 0;

    if ($a_n && $_SESSION['user_id'] == $a_n['userId']) {
        $ee_r = "You cannot bid on your own auction.";
    } elseif ($b_am_t > 0) {
        try {
            $s_t_m = $pdo->prepare("INSERT INTO bid (auctionId, userId, amount, created) 
                                   VALUES (:auctionId, :userId, :amount, NOW())");
            $s_t_m->execute([
                'auctionId' => $a_i,
                'userId' => $_SESSION['user_id'],
                'amount' => $b_am_t
            ]);
            header("Location: review.php?id=$a_i");
            exit;
        } catch (PDOException $e) {
            $ee_r = "Error placing bid: " . $e->getMessage();
        }
    } else {
        $ee_r = "Please enter a bid amount greater than zero.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Auction</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<header>
    <h1>Carbuy Auctions</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <?php if ($i_l_i): ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main> 
    <?php if ($a_n): ?>
        <h1><?php echo ($a_n['title']); ?></h1>
        <h3>Category: <?php echo ($a_n['category']); ?></h3>
        <img src="<?php echo ($a_n['image']); ?>" alt="Auction Image" /> 
        <p><br><br><?php echo ($a_n['description']); ?></p>
        <p><br><br>End Date: <?php echo date('F j, Y', strtotime($a_n['endDate'])); ?></p> <br><br>

        <hr />

        <?php if ($i_l_i && $_SESSION['user_id'] != $a_n['userId']): ?><br><br>  
            <h2>Submit a Review</h2>  <br><br>
            <form method="POST"> 
                <textarea name="comment" placeholder="Write your review here" required></textarea><br /> 
                <input type="submit" name="submit_review" value="Submit Review">
            </form>
        <?php elseif ($i_l_i): ?>
            <p><strong>Reviewing on your own auction is not allowed.</strong></p>
        <?php endif; ?>

        <hr  /><br><br>

        <h2>Place a Bid</h2><br><br>
        <?php if ($i_l_i && $_SESSION['user_id'] != $a_n['userId']): ?>
            <form method="POST">
                <input type="number" name="bid_amount" placeholder="Enter your bid" required />
                <input type="submit" name="submit_bid" value="Place Bid" />
            </form><br><br>
        <?php elseif ($i_l_i): ?>
            <p><strong>Bidding on your own auction is not allowed.</strong></p>
        <?php else: ?>
            <p><strong>You cannot bid without logging in.</strong></p>
        <?php endif; ?>

        <hr /><br><br>

        <h2>Reviews</h2><br>
        <?php if (empty($r_vw)): ?>
            <p>Be the first one to review!</p>
        <?php else: ?>
            <?php foreach ($r_vw as $review): ?>
                <div>
                    <p><strong><?php echo ($review['name']); ?></strong> - <?php echo date('F j, Y', strtotime($review['created'])); ?></p>
                    <p><?php echo nl2br(($review['comment'])); ?></p>
                    <hr />
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>

    <?php endif; ?>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>