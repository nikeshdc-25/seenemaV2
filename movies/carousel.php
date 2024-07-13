<?php
include '../connection.php';

$sql = "SELECT title, poster, description, rating, release_date, genre, genre2, minute FROM movies ORDER BY imdbVotes DESC LIMIT 12";
$result = $conn->query($sql);

$movies = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $releaseYear = date('Y', strtotime($row['release_date']));
        $movies[] = [
            'title' => $row['title'],
            'poster' => $row['poster'],
            'description' => $row['description'],
            'rating' => $row['rating'],
            'genre' => $row['genre'],
            'genre2' => $row['genre2'],
            'minute' => $row['minute'],
            'release_date' => $releaseYear,
        ];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['movies' => $movies]);
?>
