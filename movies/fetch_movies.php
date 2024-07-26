<?php
header('Content-Type: application/json');
session_start();

include '../connection.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 28;
$offset = ($page - 1) * $limit;

$totalMoviesResult = $conn->query("SELECT COUNT(*) as total FROM movies");
$totalMovies = $totalMoviesResult->fetch_assoc()['total'];
$totalPages = ceil($totalMovies / $limit);

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 0;

$sql = "SELECT m.movieID, m.title, m.director, m.actor, m.genre, m.country, m.description, m.poster, m.release_date, m.rating, m.imdbVotes, 
               IF(f.movieID IS NOT NULL, 1, 0) AS is_favorite
        FROM movies m
        LEFT JOIN favorites f ON m.movieID = f.movieID AND f.userID = ?
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userID, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$movies = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

echo json_encode([
    'movies' => $movies,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);

$stmt->close();
$conn->close();
