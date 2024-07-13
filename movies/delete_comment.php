<?php
session_start();

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentID = isset($_POST['commentID']) ? $_POST['commentID'] : null;

    if ($commentID !== null) {
        $sql = "DELETE FROM comments WHERE commentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $commentID);

        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            http_response_code(500);
            echo "Error deleting comment: " . $stmt->error;
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo "Missing commentID.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}

$conn->close();
?>
