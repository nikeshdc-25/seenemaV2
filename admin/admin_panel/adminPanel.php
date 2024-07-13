<?php
session_start();

if (!isset($_SESSION['admin_id'], $_SESSION['admin_username'])) {
    header("Location: ../../logout.php");
    exit;
}

include '../../connection.php';


// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$totalMoviesQuery = "SELECT COUNT(*) as total FROM movies";
$totalMoviesResult = $conn->query($totalMoviesQuery);
$totalMoviesRow = $totalMoviesResult->fetch_assoc();
$totalMovies = $totalMoviesRow['total'];
$totalPages = ceil($totalMovies / $perPage);

$sql = "SELECT movieID, title, director, actor, actor2, genre, genre2, minute, country, description, poster, release_date, rating, imdbVotes FROM movies LIMIT $offset, $perPage";
$result = $conn->query($sql);

$movies = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../seenema_img/seenemaLogo.png">
    <title>Admin Panel - Seenema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #939ab7;
        }
        .container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            background-color: #111c46;
            padding: 20px;
        }
        .admin-panel {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-image: linear-gradient(to top, rgb(0, 0, 0),  #2377ec);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .admin-panel h2 {
            font-size: 25px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 10px;
        }
        .text-center {
            text-align: center;
            color: red;
            font-weight: bolder;
            font-family: monospace;
        }
        .btn {
            padding: 10px 20px;
            margin: 10px;
            border-radius: 5px;
            font-size: 18px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }
        .btn:hover {
            background-color: #333;
            color: #fff;
        }
        .movie-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 10px;
            padding: 15px;
            background-color: #f9f9f9;
            max-width: 300px;
            text-align: center;
            background-color: #b6b6b6;
        }
        .movie-card img {
            max-width: 100%;
            height: 30rem;
            border-radius: 8px;
            margin-bottom: 10px;
            object-fit: contain;
        }
        .movie-card h5 {
            margin: 0 0 10px;
            font-size: 20px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .movie-card p {
            margin: 0;
            font-size: 16px;
        }
        h5{
            white-space: wordwrap;
        }
        /*Pagination*/
        .pagination {
            margin: 20px;
        }

        .pagination .page-item {
            display: inline-block;
            margin: 0 5px;
        }

        .pagination .page-link {
            color: #333;
            background-color: #f8f9fa;
            border: 1px solid #ccc;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <?php echo "Welcome, " . htmlspecialchars($_SESSION['admin_username']); ?>
        </div>
        <div class="admin-panel">
            <h2 class="text-center">Admin Panel</h2>
            <div class="d-flex justify-content-center flex-wrap">
                <a href="userhandling.php" id="userhandle" class="btn btn-secondary">Users</a>
                <a href="upload.php" id="uploadMoviesBtn" class="btn btn-info">Add Movie</a>
                <a href="addedAdmin.php" id="addAdminBtn" class="btn btn-primary">Add Admin</a>
                <a href="../../logout.php" id="homeBtn" class="btn btn-danger">Logout</a>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
            <?php foreach ($movies as $movie): ?>
                <div class="col">
                    <div class="movie-card">
                        <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <h5><?php echo htmlspecialchars($movie['title']); ?></h5>
                        <button class="btn btn-success view-btn" data-title="<?php echo htmlspecialchars($movie['title']); ?>">View</button>
                        <button class="btn btn-warning update-btn" 
                            data-movie-id="<?php echo $movie['movieID']; ?>" 
                            data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                            data-director="<?php echo htmlspecialchars($movie['director']); ?>"
                            data-actor="<?php echo htmlspecialchars($movie['actor']); ?>"
                            data-actor2="<?php echo htmlspecialchars($movie['actor2']); ?>"
                            data-genre="<?php echo htmlspecialchars($movie['genre']); ?>"
                            data-genre2="<?php echo htmlspecialchars($movie['genre2']); ?>"
                            data-minute="<?php echo htmlspecialchars($movie['minute']); ?>"
                            data-country="<?php echo htmlspecialchars($movie['country']); ?>"
                            data-description="<?php echo htmlspecialchars($movie['description']); ?>"
                            data-poster="<?php echo htmlspecialchars($movie['poster']); ?>"
                            data-rating="<?php echo htmlspecialchars($movie['rating']); ?>"
                            data-imdbVotes="<?php echo htmlspecialchars($movie['imdbVotes']); ?>"
                            >Update</button>
                        <button class="btn btn-danger delete-btn" data-movie-id="<?php echo $movie['movieID']; ?>">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const updateButtons = document.querySelectorAll('.update-btn');
            const viewButtons = document.querySelectorAll('.view-btn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const movieId = this.getAttribute('data-movie-id');

                    if (confirm('Are you sure you want to delete this movie?')) {
                        fetch('delete_movie.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'movie_id=' + encodeURIComponent(movieId)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Movie deleted successfully');
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the movie...');
                        });
                    }
                });
            });

            updateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const movieId = this.getAttribute('data-movie-id');
                    const title = this.getAttribute('data-title');
                    const director = this.getAttribute('data-director');
                    const actor = this.getAttribute('data-actor');
                    const actor2 = this.getAttribute('data-actor2');
                    const genre = this.getAttribute('data-genre');
                    const genre2 = this.getAttribute('data-genre2');
                    const minute = this.getAttribute('data-minute');
                    const country = this.getAttribute('data-country');
                    const description = this.getAttribute('data-description');
                    const poster = this.getAttribute('data-poster');
                    const rating = this.getAttribute('data-rating');
                    const imdbVotes = this.getAttribute('data-imdbVotes');
                    window.location.href = `update.php?movie_id=${movieId}&title=${encodeURIComponent(title)}&director=${encodeURIComponent(director)}&actor=${encodeURIComponent(actor)}&actor2=${encodeURIComponent(actor2)}&genre=${encodeURIComponent(genre)}&genre2=${encodeURIComponent(genre2)}&minute=${encodeURIComponent(minute)}&country=${encodeURIComponent(country)}&description=${encodeURIComponent(description)}&poster=${encodeURIComponent(poster)}&rating=${encodeURIComponent(rating)}&imdbVotes=${encodeURIComponent(imdbVotes)}`;
                });
            });

            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const title = this.getAttribute('data-title');
                    window.location.href = `../../movies/movieOverview.php?title=${encodeURIComponent(title)}`;
                });
            });
        });
    </script>
</body>
</html>
