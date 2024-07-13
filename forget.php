<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    $email = filter_var($_POST['forget-email'], FILTER_SANITIZE_EMAIL);
    $favdish = $_POST['forget-favdish'];
    $newPassword = $_POST['forget-password'];
    $confirmPassword = $_POST['forget-confirm-password'];

    if (strlen($newPassword) < 8) {
        echo json_encode(["status" => "error", "message" => "Password should be at least 8 characters long!"]);
        exit();
    } else if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit();
    }

    $query = "SELECT userID FROM userdata WHERE email = ? AND favDish = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $favdish);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId);
        $stmt->fetch();

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE userdata SET userPassword = ? WHERE userID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $hashedPassword, $userId);

        if ($updateStmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update the password."]);
        }

        $updateStmt->close();
    }
     else {
        echo json_encode(["status" => "error", "message" => "Invalid email or favorite dish."]);
    }

    $stmt->close();
    $conn->close();
}
?>
