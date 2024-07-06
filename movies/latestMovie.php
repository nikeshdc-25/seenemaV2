<?php
header('Content-Type: application/json');
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 0;

$sql = "SELECT m.movieID, m.title, m.director, m.actor, m.genre, m.country, m.description, m.poster, m.release_date, m.rating, m.imdbVotes, 
               IF(f.movieID IS NOT NULL, 1, 0) AS is_favorite
        FROM movies m
        LEFT JOIN favorites f ON m.movieID = f.movieID AND f.userID = ?
        ORDER BY m.release_date DESC
        LIMIT 21";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$movies = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

echo json_encode($movies);

$stmt->close();
$conn->close();
?>
