<?php
session_start();
require 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['user_id'];
$e_r = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t_t = trim($_POST['title']);
    $d_n = trim($_POST['description']);
    $c_id = intval($_POST['category']);
    $e_d = $_POST['endDate'];

    $i_p = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $u_d = 'uploads/';
        if (!is_dir($u_d)) {
            mkdir($u_d, 0777, true);
        }

        $i_nn = basename($_FILES['image']['name']);
        $t_f = $u_d . uniqid() . "_" . $i_nn;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $t_f)) {
            $i_p = $t_f;
        } else {
            $e_r = "Image upload failed.";
        }
    }

    if (empty($t_t) || empty($d_n) || empty($c_id) || empty($e_d)) {
        $e_r = "All fields are required.";
    } else {
        try {
            $s_t_m = $pdo->prepare("INSERT INTO auction (title, description, categoryId, endDate, userId, image) 
                                 VALUES (:title, :description, :categoryId, :endDate, :user_id, :image)");
            $s_t_m->execute([
                ':title' => $t_t,
                ':description' => $d_n,
                ':categoryId' => $c_id,
                ':endDate' => $e_d,
                ':user_id' => $u_id,
                ':image' => $i_p
            ]);
            
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
        }
    }
}

try {
    $s_t_m = $pdo->prepare("SELECT id, name FROM category");
    $s_t_m->execute();
    $c_gr = $s_t_m->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $e_r = "Error fetching category: " . $e->getMessage();
    $c_gr = [];
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
    <h2>Start an auction</h2>
    <form action="addAuction.php" method="POST" enctype="multipart/form-data">

            <label for="title">Name:</label>
            <input type="text" id="title" name="title" required />
        </div>


            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" />
        </div>


            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Vehicle Category</option>
                <?php foreach ($c_gr as $rww): ?>
                    <option value="<?php echo $rww['id']; ?>">
                        <?php echo ($rww['name']); ?>
                    </option>   
                <?php endforeach; ?>
            </select>
        </div>
            <label for="endDate">Auction Expiry Date:</label>
            <input type="datetime-local" id="endDate" name="endDate" required />
        </div>


            <input type="submit" value="Done." />
        </div>
    </form>
</main>
</body>
<footer>
    &copy; Carbuy <?php echo date('Y'); ?>
</footer>
</html>
