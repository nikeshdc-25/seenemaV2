
<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../src/logout.php");
    exit;
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$title = $_POST['title'];
$director = $_POST['director'];
$actor = $_POST['actor'];
$genre = $_POST['genre'];
$country = $_POST['country'];
$description = $_POST['description'];
$poster = $_POST['poster'];
$release_date = $_POST['release_date'];
$rating = $_POST['rating'];
$imdbVotes = $_POST['imdbVotes'];

$checkStmt = $conn->prepare("SELECT COUNT(*) FROM movies WHERE title = ?");
$checkStmt->bind_param("s", $title);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    echo json_encode(["status" => "error", "message" => "Movie with the same title already exists."]);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("INSERT INTO movies (title, director, actor, genre, country, description, poster, release_date, rating, imdbVotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssdd", $title, $director, $actor, $genre, $country, $description, $poster, $release_date, $rating, $imdbVotes);

if ($stmt->execute()) {
    $movie_id = $stmt->insert_id;
    echo json_encode(["status" => "success", "movie_id" => $movie_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Error inserting movie: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>

