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

// Get filter values from request
$year = isset($_GET['year']) ? $_GET['year'] : '';
$rating = isset($_GET['rating']) ? $_GET['rating'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Build SQL query with filters
$sql = "SELECT m.movieID, m.title, m.director, m.actor, m.genre, m.country, m.description, m.poster, m.release_date, m.rating, m.imdbVotes, 
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
    $sql .= " AND m.genre = ?";
    $params[] = $genre;
    $types .= "s";
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
