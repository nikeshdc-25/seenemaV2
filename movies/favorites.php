<?php
session_start();
header('Content-Type: application/json');

include '../connection.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$userID = $_SESSION['userID'];
$movieID = isset($_POST['movieID']) ? intval($_POST['movieID']) : 0;

if ($movieID === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid movie ID"]);
    exit;
}

$action = '';

// Check if movieID exists in favorites for the user
$stmt = $conn->prepare("SELECT * FROM favorites WHERE userID = ? AND movieID = ?");
$stmt->bind_param("ii", $userID, $movieID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remove from favorites
    $stmt = $conn->prepare("DELETE FROM favorites WHERE userID = ? AND movieID = ?");
    $stmt->bind_param("ii", $userID, $movieID);
    if ($stmt->execute()) {
        $action = 'removed';
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove favorite"]);
        exit;
    }
} else {
    // Add to favorites
    $stmt = $conn->prepare("INSERT INTO favorites (userID, movieID) VALUES (?, ?)");
    $stmt->bind_param("ii", $userID, $movieID);
    if ($stmt->execute()) {
        $action = 'added';
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add favorite"]);
        exit;
    }
}

echo json_encode(["status" => "success", "message" => "Favorite $action!", "action" => $action]);

$stmt->close();
$conn->close();
?>
