<?php
session_start();

include '../../connection.php';


$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT adminID, adminName, adminPassword FROM admins WHERE adminName = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($adminID, $adminName, $adminPassword);
    $stmt->fetch();

    if (password_verify($password, $adminPassword)) {
        $_SESSION['admin_id'] = $adminID;
        $_SESSION['admin_username'] = $adminName;
        echo json_encode(["status" => "success", "message" => "Admin Login successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect password!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Admin not found..."]);
}

$stmt->close();
$conn->close();
?>
