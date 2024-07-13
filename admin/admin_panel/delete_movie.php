<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../logout.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = $_POST['movie_id'];
    include '../../connection.php';


    $stmt = $conn->prepare("DELETE FROM movies WHERE movieID = ?");
    $stmt->bind_param("i", $movieId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Movie deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Movie ID not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
