<?php
header('Content-Type: application/json');
session_start();

include '../connection.php';

if (!$conn) {
    echo json_encode(['error' => 'Failed to connect to the database.']);
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

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
    exit;
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    echo json_encode(['error' => 'Failed to execute the SQL statement.']);
    exit;
}

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
