<?php
include '../connection.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movieID = $_POST['movieID'];
    $userID = $_POST['userID'];
    $rating = $_POST['rating'];
    $review_title = $_POST['review_title'];
    $review = $_POST['review'];

    $sql = "INSERT INTO seenepoll (movieID, userID, user_rating, review_title, user_review) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $movieID, $userID, $rating, $review_title, $review);
    if ($stmt->execute()) {
        $sqlUpdateVotes = "UPDATE seenepoll SET total_votes = total_votes + 1 WHERE movieID = ?";
        $stmtUpdateVotes = $conn->prepare($sqlUpdateVotes);
        $stmtUpdateVotes->bind_param("i", $movieID);
        $stmtUpdateVotes->execute();
        $stmtUpdateVotes->close();
        echo "Rating submitted successfully!";
    } else {
        echo "Error submitting rating: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid data.";
}

$conn->close();
?>
