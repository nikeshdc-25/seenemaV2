<?php
header('Content-Type: application/json');
session_start();

include '../connection.php';

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 0;

// Get filter values from request
$year = isset($_GET['year']) ? $_GET['year'] : '';
$rating = isset($_GET['rating']) ? $_GET['rating'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$actor = isset($_GET['actor']) ? $_GET['actor'] : '';

// Build SQL query with filters
$sql = "SELECT m.movieID, m.title, m.director, m.actor, m.actor2, m.genre, m.genre2, m.country, m.description, m.poster, m.release_date, m.rating, m.imdbVotes, 
               IF(f.movieID IS NOT NULL, 1, 0) AS is_favorite
        FROM movies m
        LEFT JOIN favorites f ON m.movieID = f.movieID AND f.userID = ?
        WHERE 1=1";

$params = [$userID];
$types = "i";

if (!empty($year)) {
    $sql .= " AND YEAR(m.release_date) = ?";
    $params[] = $year;
    $types .= "i";
}
if (!empty($rating)) {
    $sql .= " AND m.rating >= ?";
    $params[] = $rating;
    $types .= "d";
}
if (!empty($country)) {
    $sql .= " AND m.country = ?";
    $params[] = $country;
    $types .= "s";
}
if (!empty($genre)) {
    $sql .= " AND (m.genre = ? OR m.genre2 = ?)";
    $params[] = $genre;
    $params[] = $genre;
    $types .= "ss";
}
if (!empty($actor)) {
    $sql .= " AND (m.actor = ? OR m.actor2 = ?)";
    $params[] = $actor;
    $params[] = $actor;
    $types .= "ss";
}

$sql .= " ORDER BY m.title ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

$movies = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

echo json_encode([
    'movies' => $movies
]);

$stmt->close();
$conn->close();
?>
