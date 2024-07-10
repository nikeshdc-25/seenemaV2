<?php
session_start();

if (!isset($_SESSION['admin_id'], $_SESSION['admin_username'])) {
    header("Location: ../../src/logout.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Query to fetch user reviews and review titles
    $reviewQuery = "
        SELECT u.username, u.email, u.favDish, p.user_rating, p.review_title, p.user_review, m.movieID, m.title, m.poster
        FROM userdata u
        LEFT JOIN seenepoll p ON u.userID = p.userID
        LEFT JOIN movies m ON p.movieID = m.movieID
        WHERE u.userID = $userID
    ";

    $reviewResult = $conn->query($reviewQuery);

    if ($reviewResult->num_rows > 0) {
        $userData = $reviewResult->fetch_assoc();
        $username = htmlspecialchars($userData['username']);
        $favDish = htmlspecialchars($userData['favDish']);
        
        echo "
            <h5>Username: $username</h5>
            <p>Favorite Dish: $favDish</p>
        ";
        
        echo "<h6>Contents:</h6>";
        echo "<div>";
        
        $dataByMovie = [];
        do {
            $movieID = $userData['movieID'];
            $title = htmlspecialchars($userData['title']);
            $poster = htmlspecialchars($userData['poster']);
            $userRating = $userData['user_rating'];
            $reviewTitle = htmlspecialchars($userData['review_title']);
            $userReview = htmlspecialchars($userData['user_review']);

            // Initialize movie entry if not exists
            if (!isset($dataByMovie[$movieID])) {
                $dataByMovie[$movieID] = [
                    'title' => $title,
                    'poster' => $poster,
                    'reviews' => [],
                    'comments' => []
                ];
            }

            // Add review if available
            if (!empty($userRating)) {
                $dataByMovie[$movieID]['reviews'][] = [
                    'user_rating' => $userRating,
                    'review_title' => $reviewTitle,
                    'user_review' => $userReview
                ];
            }
        } while ($userData = $reviewResult->fetch_assoc());

        // Query to fetch comments for the user
        $commentQuery = "
            SELECT c.comment, m.movieID, m.title, m.poster
            FROM comments c
            LEFT JOIN movies m ON c.movieID = m.movieID
            WHERE c.userID = $userID
        ";

        $commentResult = $conn->query($commentQuery);

        if ($commentResult->num_rows > 0) {
            while ($commentData = $commentResult->fetch_assoc()) {
                $movieID = $commentData['movieID'];
                $title = htmlspecialchars($commentData['title']);
                $poster = htmlspecialchars($commentData['poster']);
                $comment = htmlspecialchars($commentData['comment']);

                // Add comment if movie already exists in dataByMovie array
                if (isset($dataByMovie[$movieID])) {
                    $dataByMovie[$movieID]['comments'][] = $comment;
                } else {
                    // Add movie entry if not exists
                    $dataByMovie[$movieID] = [
                        'title' => $title,
                        'poster' => $poster,
                        'reviews' => [],
                        'comments' => [$comment]
                    ];
                }
            }
        }

        // Display each movie with reviews and comments
        foreach ($dataByMovie as $movie) {
            echo "<div style='border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;'>";
            echo "<h4>{$movie['title']}</h4>";
            if (!empty($movie['poster'])) {
                echo "<img src='{$movie['poster']}' alt='{$movie['title']} Poster' style='max-width: 200px;'>";
            }

            // Display reviews if available
            if (!empty($movie['reviews'])) {
                echo "<h5>User Reviews:</h5>";
                foreach ($movie['reviews'] as $review) {
                    echo "<p><strong>Rating:</strong> {$review['user_rating']}</p>";
                    echo "<p><strong>Review Title:</strong> {$review['review_title']}</p>";
                    echo "<p><strong>Review:</strong> {$review['user_review']}</p>";
                }
            } else {
                echo "<p>No reviews available for this movie.</p>";
            }

            // Display comments if available
            if (!empty($movie['comments'])) {
                echo "<h5>User Comments:</h5>";
                echo "<ul>";
                foreach ($movie['comments'] as $comment) {
                    echo "<li>$comment</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No comments available for this movie.</p>";
            }

            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "User data not found.";
    }
} else {
    echo "User ID not provided.";
}

$conn->close();

function logDeletion($adminName, $deletedUser, $deletedEmail) {
    $logFile = 'log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] Admin '$adminName' deleted user '$deletedUser' with email '$deletedEmail'.\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Log the deletion
if (isset($_POST['delete_user'])) {
    $userIDToDelete = $_POST['delete_user'];

    $getUserQuery = "SELECT username, email FROM userdata WHERE userID = $userIDToDelete";
    $getUserResult = $conn->query($getUserQuery);
    $userData = $getUserResult->fetch_assoc();

    $deleteUserQuery = "DELETE FROM userdata WHERE userID = $userIDToDelete";
    $deleteUserResult = $conn->query($deleteUserQuery);

    if ($deleteUserResult) {
        logDeletion($_SESSION['admin_username'], $userData['username'], $userData['email']);
        header("Location: userhandling.php");
        exit;
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}
?>
