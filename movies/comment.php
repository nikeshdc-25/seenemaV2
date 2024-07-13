<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../connection.php';

    $movieID = isset($_POST['movieID']) ? (int)$_POST['movieID'] : 0;
    $userID = isset($_POST['userID']) ? (int)$_POST['userID'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($movieID && $userID && $comment) {
        $sql = "INSERT INTO comments (movieID, userID, comment, comment_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $movieID, $userID, $comment);
        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            http_response_code(500);
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo "Invalid input.";
    }

    $conn->close();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>
