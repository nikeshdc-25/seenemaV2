<?php
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

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 28;
$offset = ($page - 1) * $limit;

$totalMoviesResult = $conn->query("SELECT COUNT(*) as total FROM movies");
$totalMovies = $totalMoviesResult->fetch_assoc()['total'];
$totalPages = ceil($totalMovies / $limit);

$sql = "SELECT movieID, title, director, actor, genre, country, description, poster, release_date, rating, imdbVotes FROM movies LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$movies = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

echo json_encode([
    'movies' => $movies,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);

$conn->close();
?>
