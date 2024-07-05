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

$sql = "SELECT movieID, title, director, actor, genre, country, description, poster, release_date, rating, imdbVotes 
        FROM movies 
        ORDER BY imdbVotes DESC 
        LIMIT 21";

$result = $conn->query($sql);

$movies = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

echo json_encode($movies);

$conn->close();
?>
