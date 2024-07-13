<?php
session_start();

if (!isset($_SESSION['admin_id'], $_SESSION['admin_username'])) {
    header("Location: ../../logout.php");
    exit;
}

include '../../connection.php';


function logDeletion($adminName, $deletedUser) {
    $logFile = 'log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] Admin '$adminName' deleted user '$deletedUser'.\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if (isset($_POST['delete_user'])) {
    $userIDToDelete = $_POST['delete_user'];

    $getUserQuery = "SELECT username FROM userdata WHERE userID = $userIDToDelete";
    $getUserResult = $conn->query($getUserQuery);
    $userData = $getUserResult->fetch_assoc();

    $deleteUserQuery = "DELETE FROM userdata WHERE userID = $userIDToDelete";
    $deleteUserResult = $conn->query($deleteUserQuery);

    if ($deleteUserResult) {
        logDeletion($_SESSION['admin_username'], $userData['username']);

        header("Location: userhandling.php");
        exit;
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

$perPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$sql = "SELECT userID, username, email, favDish FROM userdata LIMIT $offset, $perPage";
$result = $conn->query($sql);

$usersData = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usersData[] = $row;
    }
}

$totalUsersQuery = "SELECT COUNT(*) as total FROM userdata";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsersRow = $totalUsersResult->fetch_assoc();
$totalUsers = $totalUsersRow['total'];
$totalPages = ceil($totalUsers / $perPage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../seenema_img/seenemaLogo.png">
    <title>User Details - Seenema Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="userhandling.css">
</head>
<body>
    <div class="container">
        <div class="text-center">
            <?php echo "Welcome, " . htmlspecialchars($_SESSION['admin_username']); ?>
        </div>
        <div class="admin-panel">
            <h2 class="text-center">User Details</h2>
            <div class="d-flex justify-content-center flex-wrap">
                <a href="adminPanel.php" class="btn btn-primary">Back to Admin Panel</a>
                <a href="../../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersData as $userData): ?>
                    <tr>
                        <td>
                            <button href="#" class="username-link" data-user-id="<?php echo $userData['userID']; ?>">
                               <?php echo htmlspecialchars($userData['username']); ?> 
                            </button>
                        </td>
                        <td><?php echo htmlspecialchars($userData['email']); ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete_user" value="<?php echo $userData['userID']; ?>">
                                <button type="submit" class="btn btn-danger">Delete User</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="userModalLabel">User Details:</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.username-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var userID = this.getAttribute('data-user-id');

                    fetch('fetch_user_details.php?userID=' + userID)
                        .then(response => response.text())
                        .then(data => {
                            document.querySelector('#userModal .modal-body').innerHTML = data;
                            new bootstrap.Modal(document.getElementById('userModal')).show();
                        })
                        .catch(error => {
                            console.error('Error fetching user details:', error);
                        });
                });
            });
        });
    </script>
</body>
</html>
