<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seenema</title>
    <link rel="icon" href="../seenema_img/seenemaLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <style>
    .favorite-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        color: white;
        font-size: 24px;
        z-index: 1;
        cursor: pointer;
        transition: all 0.1s ease;
    }
    .favorite-icon:hover{
        font-size: 27px;
    }
    </style>
</head>
<body id="top">
    <header class="d-flex align-items-center justify-content-left logo-container">
        <img src="../seenema_img/seenemaLogo.png" alt="Seenema Logo" class="me-2">
        <a class="seenemaTxt">SEENEMA</a> 
    </header>
    <aside class="button-container d-flex justify-content-center align-items-center flex-wrap">
        <button class="curved-button" title="Home" id="homeButton"><i class="fas fa-home"></i></button>
        <button class="curved-button" id="featuredButton" title="Feature">Featured Movie</button>
        <button class="curved-button" id="latestButton" title="Latest">Latest</button>
        <button class="curved-button" id="allMoviesButton" title="All Movies">All Movies</button>
        <button class="curved-button" id="favMoviesButton" title="Favorites">Favorites</button>
        <button id="filterButton" class="curved-button filter-toggle" type="button">Filter</button>           
        <div class="search-container d-flex align-items-center">
            <input type="text" id="quickSearchInput" class="form-control me-2" placeholder="Quick Search..." aria-label="Search">
            <button  id="quickSearchButton" class="search-button curved-button" title="Search"><i class="fas fa-search"></i></button>
        </div>
        <?php if (isset($_SESSION['userID'], $_SESSION['userName'], $_SESSION['userPassword'])): ?>
            <div class="user-container d-flex align-items-center">
            <button class="user-logo" type="button" id="userButton" aria-expanded="false">
                <i class="fas fa-user-circle" style="font-size: 35px;"></i>
            </button>
            <div id="userMenu" class="user-menu" style="display: none;">
                <p id="userMessage"></p>
                <button id="logoutButton" class="logout-button" title="Logout">
                    <i class="fas fa-sign-out">Logout</i>
                </button>
            </div>
        </div>
        <?php else: ?>
            <button class="curved-button signup-button" title="Login">Login</button>
        <?php endif; ?>
        </aside>
    <div class="filter-bar">
        <nav class="d-flex align-items-center justify-content-center flex-wrap">
            <div class="form-group">
                <label for="year" class="sr-only">Year</label>
                <select class="form-control" id="year">
                    <option value="">Year</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                    <option value="2013">2013</option>
                    <option value="2012">2012</option>
                    <option value="2011">2011</option>
                    <option value="2010">2010</option>
                    <option value="2009">2009</option>
                    <option value="2008">2008</option>
                    <option value="2007">2007</option>
                    <option value="2006">2006</option>
                    <option value="2005">2005</option>
                    <option value="2004">2004</option>
                    <option value="2003">2003</option>
                    <option value="2002">2002</option>
                    <option value="2001">2001</option>
                    <option value="2000">2000</option>
                    <option value="1999">1999</option>
                    <option value="1998">1998</option>
                    <option value="1997">1997</option>
                    <option value="1996">1996</option>
                    <option value="1995">1995</option>
                    <option value="1994">1994</option>
                    <option value="1993">1993</option>
                    <option value="1992">1992</option>
                    <option value="1991">1991</option>
                    <option value="1990">1990</option>
                    <option value="1989">1989</option>
                    <option value="1988">1988</option>
                    <option value="1987">1987</option>
                    <option value="1986">1986</option>
                    <option value="1985">1985</option>
                </select>
            </div>
            <div class="form-group">
                <label for="rating" class="sr-only">Rating</label>
                <input type="text" class="form-control" id="rating" placeholder="Rating" step="0.1" min="0" max="10">
            </div>
            <div class="form-group">
                <label for="country" class="sr-only">Country</label>
                <select class="form-control" id="country">
                    <option value="">Country</option>
                    <option value="USA">United States</option>
                    <option value="India">India</option>
                    <option value="Nepal">Nepal</option>
                    <option value="China">China</option>
                    <option value="UK">United Kingdom</option>
                    <option value="France">France</option>
                    <option value="Japan">Japan</option>
                    <option value="Germany">Germany</option>
                    <option value="South Korea">South Korea</option>
                    <option value="Italy">Italy</option>
                    <option value="Canada">Canada</option>
                </select>
            </div>
            <div class="form-group">
                <label for="genre" class="sr-only">Genre</label>
                <select class="form-control" id="genre">
                    <option value="">Genre</option>
                    <option value="action">Action</option>
                    <option value="adventure">Adventure</option>
                    <option value="comedy">Comedy</option>
                    <option value="history">History</option>
                    <option value="animation">Animation</option>
                    <option value="crime">Crime</option>
                    <option value="family">Family</option>
                    <option value="drama">Drama</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="horror">Horror</option>
                    <option value="mystery">Mystery</option>
                    <option value="romance">Romance</option>
                    <option value="sci-fi">Sci-Fi</option>
                    <option value="thriller">Thriller</option>
                    <option value="war">War</option>
                    <option value="western">Western</option>
                </select>
            </div>
            <button class="search-filter" title="Search" type="submit"><i class="fas fa-search"></i></button>
        </nav>
    </div>
    <main class="col-12">
        <div id="movieTitleContainer" class="d-flex align-items-center" style="display: none;">
            <h2 id="movieTitle" class="ms-2"></h2>
        </div>
        <div class="movie-container d-flex justify-content-center flex-wrap"></div>
        <div class="pagination-container"></div>
        <!--Login Form-->
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Login</h2>
                <form id="loginForm" action="login.php">
                    <div class="input-container">
                        <label for="login-email">Email:</label>
                        <input type="email" id="login-email" name="login-email" class="email form-control" required>
                    </div>
                    <div class="input-container">
                        <label for="login-password">Password:</label>
                        <input type="password" id="login-password" name="login-password" class="form-control" required>
                        <a href="forget-form" class="forget">Forgot Password?</a>                      
                    </div>
                    <button type="submit" class="btn loginbtn">Login</button>
                    <div class="foot">
                        <h4>Don't have an account yet?</h4>
                        <h4><a href="#" id="registerLink">Register</a></h4>
                    </div>
                </form>                    
            </div>
        </div>            
        <!--Register Form-->
        <div id="registerModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Register</h2>
                <form id="registerForm" method="POST">
                    <div class="input-container">
                        <label for="reg-username">Username:</label>
                        <input type="text" id="reg-username" class="form-control" name="reg-username" required>
                    </div>
                    <div class="input-container">
                        <label for="reg-email">Email:</label>
                        <input type="email" id="reg-email" class="form-control" name="reg-email" required>
                    </div>
                    <div class="input-container">
                        <label for="reg-favdish">Favorite Dish:</label>
                        <input type="text" id="reg-favdish" class="form-control" name="reg-favdish" required>
                    </div>
                    <div class="input-container">
                        <label for="reg-password">Password:</label>
                        <input type="password" id="reg-password" class="form-control" name="reg-password" required>
                    </div>
                    <div class="input-container">
                        <label for="reg-confirm-password">Confirm Password:</label>
                        <input type="password" id="reg-confirm-password" class="form-control" name="reg-confirm-password" required>
                    </div>
                    <button type="submit" class="btn loginbtn" name="submit">Register</button>
                    <div class="foot">
                        <h4>Already have an account?</h4>
                        <h4><a href="#" id="loginLink">Login</a></h4>
                    </div>
                </form>
            </div>
        </div>
        <!--Forget Password Form-->
        <div id="forgetModal" class="modal">
            <div class="modal-content forgColor">
                <span class="close-btn">&times;</span>
                <span class="back-btn"><i class="fas fa-arrow-left"></i></span>
                <h2>Forget Password</h2>
                <form id="forgetForm" action="forget.php">
                    <div class="input-container">
                        <label for="forget-email">Email:</label>
                        <input type="text" id="forget-email" name="forget-email" class="form-control" required>
                    </div>
                    <div class="input-container">
                        <label for="forget-favdish">Security Question: Your Favorite Dish?</label>
                        <input type="text" id="forget-favdish" name="forget-favdish" class="form-control" required>
                    </div>
                    <div class="input-container">
                        <label for="forget-password">New Password:</label>
                        <input type="password" id="forget-password" name="forget-password" class="form-control" required>
                    </div>
                    <div class="input-container">
                        <label for="forget-confirm-password">Confirm Password:</label>
                        <input type="password" id="forget-confirm-password" class="form-control" name="forget-confirm-password" required>
                    </div>
                    <button type="submit" class="btn loginbtn">Reset</button>
                </form>
            </div>
        </div>
    </main> 
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>Contact Me</h3>
                    <p>Email: nutternikexdx@gmail.com</p>
                    <p>Phone: +977 9848050240</p>
                </div>
                <div class="col-md-4">
                    <h3>Follow Me</h3>
                    <ul class="social-icons">
                        <li><a href="https://github.com/nikeshdc-25"><i class="fab fa-github"></i></a></li>
                        <li><a href="https://facebook.com/nikeshdhakal25"><i class="fab fa-facebook"></i></a></li>
                        <li><a href="https://instagram.com/nikkey_25"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="../About/aboutme.html">About Me</a></li>
                        <li><a href="tel:+9779848050240"><i class="fas fa-phone"></i> Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p class="text-center">© 2024 Seenema. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const movieContainer = document.querySelector('.movie-container');
        const userButton = document.getElementById('userButton');
        const userMenu = document.getElementById('userMenu');
        const userMessage = document.getElementById('userMessage');
        const logoutButton = document.getElementById('logoutButton');
        const paginationContainer = document.querySelector('.pagination-container');
        const movieTitleContainer = document.getElementById('movieTitleContainer');
        const movieTitle = document.getElementById('movieTitle');
        const quickSearchInput = document.getElementById('quickSearchInput');
        const quickSearchButton = document.getElementById('quickSearchButton');
        const favMoviesButton = document.getElementById('favMoviesButton');
        let userFavorites = [];


        function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        // Set styles to make sure it shows up
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.left = '50%';
        notification.style.transform = 'translateX(-50%)';
        notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
        notification.style.color = '#fff';
        notification.style.padding = '10px 20px';
        notification.style.width = '15rem';
        notification.style.borderRadius = '5px';
        notification.style.zIndex = '9999';
        notification.style.opacity = '1';
        notification.style.transition = 'opacity 0.3s ease';


        document.body.appendChild(notification);

        setTimeout(() => {
            progressBar.style.width = '0';
        }, 0);

        setTimeout(function() {
            notification.style.opacity = '0';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 300);
        }, 1500);
    }

        function fetchUserFavorites() {
        return fetch('../movies/favoriteMovie.php')
            .then(response => response.json())
            .then(data => {
                userFavorites = data.map(favorite => favorite.movieID);
            })
            .catch(error => {
                console.error('Error fetching user favorites:', error);
                userFavorites = [];
            });
        }

        fetchUserFavorites()
            .then(() => fetchMoviesFromFetchMoviesPHP())
            .catch(error => console.error('Error initializing:', error));


        //For Message:
        function updateMovieTitle(title) {
            movieTitle.innerHTML = `${title}`;
            movieTitleContainer.style.display = 'flex';
        }

        //Pagination Starts here:
        function fetchMoviesFromFetchMoviesPHP(page = 1) {
            fetch(`../movies/fetch_movies.php?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.movies.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });

                    updatePagination(data.totalPages, data.currentPage);
                })
                .catch(error => console.error('Error fetching movies from fetch_movies.php:', error));
        }

        function updatePagination(totalPages, currentPage) {
            paginationContainer.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.classList.add('page-button');
                if (i === currentPage) {
                    button.classList.add('active');
                }
                button.addEventListener('click', () => fetchMoviesFromFetchMoviesPHP(i));
                paginationContainer.appendChild(button);
            }
        }
        //For All Movies
        function fetchAllMoviesFromAllMoviePHP() {
            fetch('../movies/allMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.movies.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    updateMovieTitle(`<i class="fa-solid fa-arrow-down-a-z"></i> All Movies`);
                    paginationContainer.style.visibility = 'hidden'; // Hide pagination for all movies
                })
                .catch(error => console.error('Error fetching all movies from allMovie.php:', error));
        }

        //For Featured Movies:
        function fetchFeaturedMoviesFromFeatureMoviePHP() {
            fetch('../movies/featureMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    updateMovieTitle('<i class="fa-solid fa-fire"></i> Featured Movies');
                    paginationContainer.style.visibility = 'hidden';
                })
                .catch(error => console.error('Error fetching featured movies from featureMovie.php:', error));
        }

        //For Latest Movies:
        function fetchLatestMoviesFromLatestPHP() {
            fetch('../movies/latestMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    updateMovieTitle('<i class="fa-solid fa-rocket"></i> Latest Movies');
                    paginationContainer.style.visibility = 'hidden';
                })
                .catch(error => console.error('Error fetching latest movies from latest.php:', error));
        }

        // For Search Button:
        quickSearchButton.addEventListener('click', function() {
            const searchTerm = quickSearchInput.value.trim();
            if (searchTerm !== '') {
                fetchMoviesFromQuickSearch(searchTerm);
            }
        });

        //Event listener for Enter key press in input field
        quickSearchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                const searchTerm = quickSearchInput.value.trim();
                if (searchTerm !== '') {
                    fetchMoviesFromQuickSearch(searchTerm);
                }
            }
        });

        function fetchMoviesFromQuickSearch(searchTerm) {
            fetch(`../movies/quickSearch.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    if (data.length === 0) {
                        movieContainer.innerHTML = `<p style="color: red; font-size: 30px; font-weight: 700;">No movies found!</p>`;
                        paginationContainer.style.visibility = 'hidden';
                        return;
                    }
                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    updateMovieTitle(`Quick Search:<span style="background-color: rgb(39, 39, 39); font-weight:">${searchTerm}</span>`);
                    paginationContainer.style.visibility = 'hidden';
                })
                .catch(error => console.error('Error fetching movies from quickSearch.php:', error));
        }

        // For Favorite Movies:
        favMoviesButton.addEventListener('click', function() {
            fetch('../movies/favoriteMovie.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error' && data.message === 'User not logged in') {
                        movieContainer.innerHTML = `<p style="color: red; font-size: 30px; font-weight: 400;">No favorite movies found!</p>`;
                        paginationContainer.style.visibility = 'hidden';
                    } else {
                        movieContainer.innerHTML = '';
                        if (data.length === 0) {
                            movieContainer.innerHTML = `<p style="color: red; font-size: 30px; font-weight: 400;">No favorite movies found!</p>`;
                            paginationContainer.style.visibility = 'hidden';
                            return;
                        }
                        data.forEach(movie => {
                            const movieCard = createMovieCard(movie);
                            movieContainer.appendChild(movieCard);
                        });
                        updateMovieTitle('<i class="fa-solid fa-heart"></i> Favorites');
                        paginationContainer.style.visibility = 'hidden';
                    }
                })
                .catch(error => console.error('Error fetching favorite movies:', error));
        });

        //For movie cards:
        function createMovieCard(movie) {
            const movieCard = document.createElement('div');
            movieCard.classList.add('movie-card');
            movieCard.setAttribute('data-movie-id', movie.movieID);

            // Favorite icon
            const favoriteIcon = document.createElement('i');
            favoriteIcon.classList.add('fa-solid', 'fa-heart', 'favorite-icon');
            if (userFavorites.includes(movie.movieID)) {
                favoriteIcon.style.color = 'red';
            }
            if (movie.is_favorite) {
                favoriteIcon.style.color = 'red';
            }
            favoriteIcon.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleFavorite(movie.movieID, favoriteIcon);
            });
            movieCard.appendChild(favoriteIcon);

            const img = document.createElement('img');
            img.src = movie.poster;
            img.alt = movie.title;
            movieCard.appendChild(img);

            const overlay = document.createElement('div');
            overlay.classList.add('movie-overlay');

            const movieRating = document.createElement('div');
            movieRating.classList.add('movie-rating');
            const formattedVotes = parseInt(movie.imdbVotes).toLocaleString();
            movieRating.textContent = `Rating: ${movie.rating}/10 ❤️: ${formattedVotes}`;
            overlay.appendChild(movieRating);

            movieCard.appendChild(overlay);

            const title = document.createElement('p');
            title.classList.add('movie-title');
            title.textContent = movie.title;
            movieCard.appendChild(title);

            movieCard.addEventListener('click', function() {
                window.location.href = `../movies/movieOverview.php?title=${encodeURIComponent(movie.title)}`;
            });

            return movieCard;
        }

        //For Favorite Icon Toggle function
        function toggleFavorite(movieID, favoriteIcon) {
            fetch('../movies/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `movieID=${encodeURIComponent(movieID)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                showNotification('You need to log in!', 'error');
                } 
                else {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        favoriteIcon.style.color = 'red';
                        showNotification('Favorite Movie Added!', 'success');
                        userFavorites.push(movieID);
                    } else if (data.action === 'removed') {
                        favoriteIcon.style.color = '';
                        showNotification('Favorite Movie Removed!', 'error');
                        userFavorites = userFavorites.filter(id => id !== movieID);
                    }
                } else {
                    console.error(data.message);
                }
            }
            })
            .catch(error => console.error('Error toggling favorite:', error));
        }

        //Initial Movies Display:
        fetchMoviesFromFetchMoviesPHP();

        //For Navigation Bar:
        const homeButton = document.getElementById('homeButton');
        if (homeButton) {
            homeButton.addEventListener('click', ()=>{
                window.location.href = "index.php";
            });
        }

        const allMovieButton = document.getElementById('allMoviesButton');
        if (allMoviesButton) {
            allMoviesButton.addEventListener('click', () => {
                fetchAllMoviesFromAllMoviePHP();
                paginationContainer.style.visibility = 'hidden';
            });
        }

        const featuredButton = document.getElementById('featuredButton');
        if (featuredButton) {
            featuredButton.addEventListener('click', ()=>{
                fetchFeaturedMoviesFromFeatureMoviePHP();
                paginationContainer.style.visibility = 'hidden';
            });
        }

        const latestButton = document.getElementById('latestButton');
        if (latestButton) {
            latestButton.addEventListener('click', ()=>{
                fetchLatestMoviesFromLatestPHP();
                paginationContainer.style.visibility = 'hidden';
            });
        }
        });

        //For User Login Buttons
        if (userButton) {
            userButton.addEventListener('click', function() {
                const username = "<?php echo isset($_SESSION['userName']) ? $_SESSION['userName'] : '' ?>";
                userMessage.textContent = `Hi, ${username}!`;
                userMenu.style.display = userMenu.style.display === 'none' ? 'block' : 'none';
            });
        }

        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                fetch('logout.php', { method: 'POST' })
                    .then(() => {
                        window.location.reload();
                    })
                    .catch(error => console.error('Error logging out:', error));
            });
        }

        document.addEventListener('click', function(event) {
            if (!userMenu.contains(event.target) && !userButton.contains(event.target)) {
                userMenu.style.display = 'none';
            }
        });

        document.querySelector('.footer-links a[href="home"]').addEventListener('click', function (event) {
                event.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
    </script>
    <script src="index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
