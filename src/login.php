<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "seenema";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
    }

    $email = $_POST['login-email'];
    $userPassword = $_POST['login-password'];
    
    $query = "SELECT userID, userName, userPassword FROM userdata WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $userName, $hashedPassword);
        $stmt->fetch();
        if (password_verify($userPassword, $hashedPassword)) {
            $_SESSION['userID'] = $userId;
            $_SESSION['userName'] = $userName;
            $_SESSION['userPassword'] = $userPassword;
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect Password, Try again."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
