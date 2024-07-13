<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../logout.php");
    exit;
}

include '../../connection.php';


$adminName = $_POST['adminName'];
$adminPassword = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);

$checkStmt = $conn->prepare("SELECT adminName FROM admins WHERE adminName = ?");
$checkStmt->bind_param("s", $adminName);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Admin with this name already exists!"]);
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO admins (adminName, adminPassword) VALUES (?, ?)");
$stmt->bind_param("ss", $adminName, $adminPassword);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Admin added successfully!"]);
} else {
    error_log("Error adding admin: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Error adding admin: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
