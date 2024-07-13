<?php
session_start();
session_unset();
session_destroy();

header("Location: index.php");
echo json_encode(["status" => "success"]);
exit;
?>
