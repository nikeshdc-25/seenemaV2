<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../src/logout.php");
    exit;
}
$movieID = $_GET['movie_id'] ?? null;
$title = $_GET['title'] ?? '';
$director = $_GET['director'] ?? '';
$actor = $_GET['actor'] ?? '';
$actor2 = $_GET['actor2'] ?? '';
$genre = $_GET['genre'] ?? '';
$genre2 = $_GET['genre2'] ?? '';
$minute = $_GET['minute'] ?? '';
$country = $_GET['country'] ?? '';
$description = $_GET['description'] ?? '';
$poster = $_GET['poster'] ?? '';
$rating = $_GET['rating'] ?? '';
$imdbVotes = $_GET['imdbVotes'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../seenema_img/seenemaLogo.png">
    <title>Update Movies - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #888a8d;
            font-family: monospace;
        }
        .container {
            padding-top: 50px;
        }
        h1 {
            font-weight: bolder;
        }
        .form-container {
            background-image: linear-gradient(270deg, #b8282e .81%, #6d46bc 99.97%);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
            text-decoration: underline;
            font-size: 18px;
        }
        .back-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: cursive;
        }
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            text-decoration: none;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form-container">
                    <a href="adminPanel.php" class="btn btn-secondary back-btn">Back</a>
                    <h1 class="text-center mb-4">Update Movie</h1>
                    <form id="updateForm" method="POST">
                        <input type="hidden" name="movie_id" value="<?php echo $movieID; ?>">
                        <div class="form-group">
                            <label for="title" class="form-label">Movie Name:</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>">
                        </div>
                        <div class="form-group">
                            <label for="director" class="form-label">Director:</label>
                            <input type="text" id="director" name="director" class="form-control" value="<?php echo htmlspecialchars($director); ?>"> 
                        </div>
                        <div class="form-group">
                            <label for="actor" class="form-label">Actor 1:</label>
                            <input type="text" id="actor" name="actor" class="form-control" value="<?php echo htmlspecialchars($actor); ?>">
                        </div>
                        <div class="form-group">
                            <label for="actor2" class="form-label">Actor 2:</label>
                            <input type="text" id="actor2" name="actor2" class="form-control" value="<?php echo htmlspecialchars($actor2); ?>">
                        </div>
                        <div class="form-group">
                            <label for="genre" class="form-label">Genre 1:</label>
                            <input type="text" id="genre" name="genre" class="form-control" value="<?php echo htmlspecialchars($genre); ?>">
                        </div>
                        <div class="form-group">
                            <label for="genre2" class="form-label">Genre 2:</label>
                            <input type="text" id="genre2" name="genre2" class="form-control" value="<?php echo htmlspecialchars($genre2); ?>">
                        </div>
                        <div class="form-group">
                            <label for="minute" class="form-label">Minute:</label>
                            <input type="number" id="minute" name="minute" class="form-control" value="<?php echo htmlspecialchars($minute); ?>">
                        </div>
                        <div class="form-group">
                            <label for="country" class="form-label">Country:</label>
                            <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($country); ?>">
                        </div>
                        <div class="form-group">
                            <label for="description" class="form-label">Description:</label>
                            <textarea name="description" id="description" class="form-control" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                        <div class="form-group">
                            <label for="poster" class="form-label">Poster URL:</label>
                            <input type="text" id="poster" name="poster" class="form-control" value="<?php echo htmlspecialchars($poster); ?>">
                        </div>
                        <div class="form-group">
                            <label for="release_date" class="form-label">Release Date:</label>
                            <input type="date" id="release_date" name="release_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="rating" class="form-label">Rating:</label>
                            <input type="number" id="rating" name="rating" class="form-control" step="0.1" min="0" max="10" value="<?php echo htmlspecialchars($rating); ?>">
                        </div>
                        <div class="form-group">
                            <label for="imdbVotes" class="form-label">imdbVotes:</label>
                            <input type="number" id="imdbVotes" name="imdbVotes" class="form-control" value="<?php echo htmlspecialchars($imdbVotes); ?>">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Movie</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('updateForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('update_movies.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Movie updated successfully!");
                    window.location.href = "adminPanel.php";
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
