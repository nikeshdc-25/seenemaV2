<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../src/logout.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
    $movieID = $_POST['movie_id'];

    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "seenema";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    if (!empty($_POST['title'])) {
        $title = $_POST['title'];
        $stmt = $conn->prepare("UPDATE movies SET title = ? WHERE movieID = ?");
        $stmt->bind_param("si", $title, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating title: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['director'])) {
        $director = $_POST['director'];
        $stmt = $conn->prepare("UPDATE movies SET director = ? WHERE movieID = ?");
        $stmt->bind_param("si", $director, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating director: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['actor'])) {
        $actor = $_POST['actor'];
        $stmt = $conn->prepare("UPDATE movies SET actor = ? WHERE movieID = ?");
        $stmt->bind_param("si", $actor, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating actor: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['genre'])) {
        $genre = $_POST['genre'];
        $stmt = $conn->prepare("UPDATE movies SET genre = ? WHERE movieID = ?");
        $stmt->bind_param("si", $genre, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating genre: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['country'])) {
        $country = $_POST['country'];
        $stmt = $conn->prepare("UPDATE movies SET country = ? WHERE movieID = ?");
        $stmt->bind_param("si", $country, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating country: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['description'])) {
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE movies SET description = ? WHERE movieID = ?");
        $stmt->bind_param("si", $description, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating description: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['poster'])) {
        $poster = $_POST['poster'];
        $stmt = $conn->prepare("UPDATE movies SET poster = ? WHERE movieID = ?");
        $stmt->bind_param("si", $poster, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating poster: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['release_date'])) {
        $release_date = $_POST['release_date'];
        $stmt = $conn->prepare("UPDATE movies SET release_date = ? WHERE movieID = ?");
        $stmt->bind_param("si", $release_date, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating release date: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['rating'])) {
        $rating = $_POST['rating'];
        $stmt = $conn->prepare("UPDATE movies SET rating = ? WHERE movieID = ?");
        $stmt->bind_param("di", $rating, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating rating: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    if (!empty($_POST['imdbVotes'])) {
        $imdbVotes = $_POST['imdbVotes'];
        $stmt = $conn->prepare("UPDATE movies SET imdbVotes = ? WHERE movieID = ?");
        $stmt->bind_param("di", $imdbVotes, $movieID);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating imdbVotes: " . $conn->error]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    echo json_encode(["status" => "success", "message" => "Movie updated successfully!", "movieID" => $movieID]);

    $conn->close();
}
?>
