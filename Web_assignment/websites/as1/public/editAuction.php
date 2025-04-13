<?php
session_start();
require 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$aid = isset($_GET['id']) ? intval($_GET['id']) : 0;
$e_r = "";

if ($aid > 0) {
    try {
        $s_t_m = $pdo->prepare("SELECT * FROM auction WHERE id = :id");
        $s_t_m->execute([':id' => $aid]);
        $ai = $s_t_m->fetch(PDO::FETCH_ASSOC);

        if (!$ai || $ai['userId'] != $uid) {
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
    }
} else {

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Save'])) {
    $tt = trim($_POST['title']);
    $d_pt = trim($_POST['description']);
    $c_di = intval($_POST['category']);
    $eed = $_POST['endDate'];
    $igm = $_POST['image'] ?? $ai['image'];

    if (empty($tt) || empty($d_pt) || empty($c_di) || empty($eed)) {
        $e_r = "All fields are required.";
    } else {
        try {
            $s_t_m = $pdo->prepare("UPDATE auction SET title = :title, description = :description, 
                                   categoryId = :categoryId, endDate = :endDate, image = :image
                                   WHERE id = :id AND userId = :user_id");
            $s_t_m->execute([
                ':title' => $tt,
                ':description' => $d_pt,
                ':categoryId' => $c_di,
                ':endDate' => $eed,
                ':image' => $igm,
                ':id' => $aid,
                ':user_id' => $uid
            ]);
            
            header("Location: index.php"); 
            exit();
        } catch (PDOException $e) {

        }
    }
}

if (isset($_POST['remove'])) {
    try {
        $s_t_m = $pdo->prepare("DELETE FROM auction WHERE id = :id AND userId = :user_id");
        $s_t_m->execute([':id' => $aid, ':user_id' => $uid]);
        
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
    }
}

try {
    $ctgs = $pdo->query("SELECT id, name FROM category");
} catch (PDOException $e) {
    $ctgs = [];
}
?>

<!DOCTYPE html>
<html>
<head>
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
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="addAuction.php">Add Auction</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <main>
        <h2>Edit Auction</h2>
        
        <?php if ($e_r): ?>
            <?php echo ($e_r); ?>
        <?php endif; ?>

        <form action="editAuction.php?id=<?php echo $aid; ?>" method="POST" enctype="multipart/form-data">
                <label for="title">Name:</label>
                <input type="text" id="title" name="title" value="<?php echo ($ai['title']); ?>" required />
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo ($ai['description']); ?></textarea>
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <?php foreach ($ctgs as $row): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $ai['categoryId'] ? 'selected' : ''; ?>>
                            <?php echo ($row['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="endDate">Auction Expiry Date:</label>
                <input type="datetime-local" id="endDate" name="endDate" value="<?php echo date('Y-m-d\TH:i', strtotime($ai['endDate'])); ?>" required />
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" />
                <p>Current image: <img src="<?php echo ($ai['image']); ?>" alt="Current Auction Image" width="100" /></p>
                <input type="submit" name="Save" value="Save" />
        </form>

        <form action="editAuction.php?id=<?php echo $aid; ?>" method="POST" onsubmit="return confirm('Delete Auction?');">
                <input type="submit" name="remove" value="Remove" />
        </form>
    </main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
