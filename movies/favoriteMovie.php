<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['userID'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$userID = $_SESSION['userID'];

$sql = "SELECT m.movieID, m.title, m.director, m.actor, m.genre, m.country, m.description, m.poster, m.release_date, m.rating, m.imdbVotes
        FROM movies m
        JOIN favorites f ON m.movieID = f.movieID
        WHERE f.userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
}

echo json_encode($favorites);

$stmt->close();
$conn->close();
?>
