<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS count FROM songs WHERE user_id = {$_SESSION['user_id']}");
$totalSongs = $totalResult->fetch_assoc()['count'];
$totalPages = ceil($totalSongs / $limit);

$stmt = $conn->prepare("SELECT * FROM songs WHERE user_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $_SESSION['user_id'], $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lagu - AudioHub</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="upload.php">Unggah Lagu</a></li>
            <li><a href="songs.php">Daftar Lagu</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h1>Daftar Lagu</h1>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p style="color: green;">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>
        <div id="songList">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo '<div class="song">';
                echo '<h2>' . htmlspecialchars($row['title']) . '</h2>';
                echo '<p>' . htmlspecialchars($row['artist']) . '</p>';
                if (!empty($row['cover_path'])) {
                    echo '<img src="' . htmlspecialchars($row['cover_path']) . '" alt="Cover">';
                }
                echo '<audio controls><source src="' . htmlspecialchars($row['file_path']) . '" type="audio/mpeg"></audio>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == $page) {
                    echo '<span class="current-page">' . $i . '</span>';
                } else {
                    echo '<a href="songs.php?page=' . $i . '">' . $i . '</a>';
                }
            }
            ?>
        </div>
    </div>
</body>

</html>