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

    $userQuery = "
    SELECT u.username, u.email, u.favDish, p.user_rating, p.review_title, p.user_review, c.comment, m.title, m.poster
    FROM userdata u
    LEFT JOIN seenepoll p ON u.userID = p.userID
    LEFT JOIN comments c ON u.userID = c.userID AND c.movieID = p.movieID
    LEFT JOIN movies m ON p.movieID = m.movieID
    WHERE u.userID = $userID
    ";

    $result = $conn->query($userQuery);

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
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
            $title = htmlspecialchars($userData['title']);
            $poster = htmlspecialchars($userData['poster']);
            $userRating = $userData['user_rating'];
            $reviewTitle = htmlspecialchars($userData['review_title']);
            $userReview = htmlspecialchars($userData['user_review']);
            $comment = htmlspecialchars($userData['comment']);

            if (!isset($dataByMovie[$title])) {
                $dataByMovie[$title] = [
                    'poster' => $poster,
                    'user_rating' => $userRating,
                    'review_title' => $reviewTitle,
                    'user_review' => $userReview,
                    'comments' => []
                ];
            }

            if (!empty($comment)) {
                $dataByMovie[$title]['comments'][] = $comment;
            }
        } while ($userData = $result->fetch_assoc());

        foreach ($dataByMovie as $title => $details) {
            echo "<div style='border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;'>";
            echo "<h4>$title</h4>";
            if (!empty($details['poster'])) {
                echo "<img src='{$details['poster']}' alt='$title Poster' style='max-width: 200px;'>";
            }
            if (!empty($details['user_rating'])) {
                echo "<p><strong>Rating:</strong> {$details['user_rating']}</p>";
                echo "<p><strong>Review Title:</strong> {$details['review_title']}</p>";
                echo "<p><strong>Review:</strong> {$details['user_review']}</p>";
            } else {
                echo "<p>No rating or review provided for this movie!</p>";
            }
            if (!empty($details['comments'])) {
                echo "<p><strong>Comments:</strong></p><ul>";
                foreach ($details['comments'] as $comment) {
                    echo "<li>$comment</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No comments available for this movie!</p>";
            }
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "User not found.";
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
