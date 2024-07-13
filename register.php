<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['reg-username'];
    $email = $_POST['reg-email'];
    $favdish = $_POST['reg-favdish'];
    $password = $_POST['reg-password'];
    $cPassword = $_POST['reg-confirm-password'];

    if(strlen($password) < 8){
        echo json_encode(["status" => "error", "message" => "Password should be 8 characters long!"]);
        exit();
    }
    else if ($password !== $cPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit();
    }
    include 'connection.php';
  
    $stmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit();
    } else {
        $stmt->close();
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO userdata (userName, email, favDish, userPassword) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $favdish, $hashedPassword);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            echo json_encode(["status" => "success"]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
            $stmt->close();
            $conn->close();
        }
    }
}
?>
