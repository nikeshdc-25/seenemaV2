<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../seenema_img/seenemaLogo.png">
    <title>Upload Movies - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #888a8d;
            font-family: monospace;
        }
        .back-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .container {
            padding-top: 50px;
        }
        h1 {
            font-weight: bolder;
        }
        .form-container {
            background-image: linear-gradient(270deg, #28b82d .81%, #6d46bc 99.97%);
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
                    <h1 class="text-center mb-4">Add Movie</h1>
                    <form id="uploadForm" method="POST" action="upload_movies.php">
                        <div class="form-group">
                            <label for="title" class="form-label">Movie Name:</label><span style="color: red;"> *</span>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="director" class="form-label">Director:</label><span style="color: red;"> *</span>
                            <input type="text" id="director" name="director" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="actor" class="form-label">Actor 1:</label><span style="color: red;"> *</span>
                            <input type="text" id="actor" name="actor" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="actor2" class="form-label">Actor 2:</label><span style="color: red;"> *</span>
                            <input type="text" id="actor2" name="actor2" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="genre" class="form-label">Genre 1:</label><span style="color: red;"> *</span>
                            <input type="text" id="genre" name="genre" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="genre2" class="form-label">Genre 2:</label><span style="color: red;"> *</span>
                            <input type="text" id="genre2" name="genre2" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="minute" class="form-label">Minute:</label><span style="color: red;"> *</span>
                            <input type="number" id="minute" name="minute" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="country" class="form-label">Country:</label>
                            <input type="text" id="country" name="country" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description" class="form-label">Description:</label><span style="color: red;"> *</span>
                            <textarea id="description" name="description" class="form-control" rows="4"required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="poster" class="form-label">Poster URL:</label><span style="color: red;"> *</span>
                            <input type="text" id="poster" name="poster" class="form-control"required>
                        </div>
                        <div class="form-group">
                            <label for="release_date" class="form-label">Release Date:</label>
                            <input type="date" id="release_date" name="release_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="rating" class="form-label">Rating:</label><span style="color: red;"> *</span>
                            <input type="number" id="rating" name="rating" class="form-control" step="0.1" min="0" max="10"required>
                        </div>
                        <div class="form-group">
                            <label for="imdbVotes" class="form-label">imdbVotes:</label><span style="color: red;"> *</span>
                            <input type="number" id="imdbVotes" name="imdbVotes" class="form-control"required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Movie</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('upload_movies.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Movie added successfully!");
                    window.location.href = "adminPanel.php"
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