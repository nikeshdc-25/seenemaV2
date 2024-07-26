<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$json_file = 'movie.json';

if (!file_exists($json_file)) {
    die("File not found: $json_file");
}

$json_data = file_get_contents($json_file);

$movies = json_decode($json_data, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON decoding error: " . json_last_error_msg());
}

if (!is_array($movies)) {
    die("The JSON data could not be converted to an array.");
}

foreach ($movies as $movie) {
    $title = $conn->real_escape_string($movie['title']);
    $director = $conn->real_escape_string($movie['director']);
    $actor = $conn->real_escape_string($movie['actor']);
    $actor2 = $conn->real_escape_string($movie['actor2']);
    $genre = $conn->real_escape_string($movie['genre']);
    $genre2 = $conn->real_escape_string($movie['genre2']);
    $minute = (int)$movie['minute'];
    $country = $conn->real_escape_string($movie['country']);
    $description = $conn->real_escape_string($movie['description']);
    $poster = $conn->real_escape_string($movie['poster']);
    $release_date = $conn->real_escape_string($movie['release_date']);
    $rating = (float)$movie['rating'];
    $imdbVotes = (int)$movie['imdbVotes'];

    $sql = "INSERT INTO movies (title, director, actor, actor2, genre, genre2, minute, country, description, poster, release_date, rating, imdbVotes)
            VALUES ('$title', '$director', '$actor', '$actor2', '$genre', '$genre2', $minute, '$country', '$description', '$poster', '$release_date', $rating, $imdbVotes)";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully for movie: $title\n";
    } else {
        echo "Error: " . $sql . "\n" . $conn->error;
    }
}

$conn->close();
?>
