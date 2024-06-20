<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $user_id = $_SESSION['user_id'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $cover_file = $target_dir . basename($_FILES["cover"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file) && move_uploaded_file($_FILES["cover"]["tmp_name"], $cover_file)) {
        $stmt = $conn->prepare("INSERT INTO songs (title, artist, file_path, cover_path, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $artist, $target_file, $cover_file, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Lagu berhasil diunggah!";
            header('Location: songs.php');
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Gagal mengunggah file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Lagu - AudioHub</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="songs.php">Daftar Lagu</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h1>Unggah Lagu</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="title">Judul Lagu:</label>
            <input type="text" name="title" required>
            <label for="artist">Artis:</label>
            <input type="text" name="artist" required>
            <label for="file">File Lagu:</label>
            <input type="file" name="file" accept="audio/*" required>
            <label for="cover">Cover Lagu:</label>
            <input type="file" name="cover" accept="image/*" required>
            <button type="submit">Unggah</button>
        </form>
    </div>
</body>

</html>