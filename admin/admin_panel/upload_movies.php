
<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../logout.php");
    exit;
}

include '../../connection.php';


$title = $_POST['title'];
$director = $_POST['director'];
$actor = $_POST['actor'];
$actor2 = $_POST['actor2'];
$genre = $_POST['genre'];
$genre2 = $_POST['genre2'];
$minute = $_POST['minute'];
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

$stmt = $conn->prepare("INSERT INTO movies (title, director, actor, actor2, genre, genre2, minute, country, description, poster, release_date, rating, imdbVotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssdssssdd", $title, $director, $actor, $actor2, $genre, $genre2, $minute, $country, $description, $poster, $release_date, $rating, $imdbVotes);

if ($stmt->execute()) {
    $movie_id = $stmt->insert_id;
    echo json_encode(["status" => "success", "movie_id" => $movie_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Error inserting movie: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>

