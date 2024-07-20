<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seenema</title>
    <link rel="icon" href="./seenema_img/seenemaLogo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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
        <img src="./seenema_img/seenemaLogo.png" alt="Seenema Logo" class="me-2">
        <a href="#" class="seenemaTxt">SEENEMA</a> 
    </header>
    <nav class="navbar navbar-expand-lg navbar-light button-container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center align-items-center" id="navbarSupportedContent">
            <ul class="navbar-nav justify-content-start align-items-start mx-2">
                <button class="curved-button" title="Home" id="homeButton"><i class="fas fa-home"></i></button>
                <button class="curved-button" id="featuredButton" title="Feature">Features</button>
                <button class="curved-button" id="latestButton" title="Latest">Latest</button>
                <button class="curved-button" id="allMoviesButton" title="All Movies">All Movies</button>
                <button class="curved-button" id="favMoviesButton" title="Favorites">Favorites</button>
                <button id="filterButton" class="curved-button filter-toggle" type="button">Filter</button>           
                <div class="search-container d-flex align-items-center">
                    <input type="text" id="quickSearchInput" class="form-control me-2" placeholder="Quick Search..." aria-label="Search">
                    <button  id="quickSearchButton" class="search-button curved-button" title="Search"><i class="fas fa-search"></i></button>
                </div>
                <?php if (isset($_SESSION['userID'], $_SESSION['userName'], $_SESSION['userPassword'])): ?>
                    <div class="user-container">
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
            </ul>
        </div>
    </nav>
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
                    <option value="America">America</option>
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
    <!-- Carousel -->
    <div id="featureCarousel" class="carousel slide m-2" data-ride="carousel">
        <span style="color: #8b8b8b; font-weight: 700;" class="pl-3"><i class="fa-solid fa-thumbs-up" style="font-size:20px; color:darkorange;"></i> Recommended Movies:</span>
            <div id="carouselInner" class="carousel-inner"></div>
            <a class="carousel-control-prev" href="#featureCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#featureCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    <main class="col-14">
        <!--For Movie Cards Display-->
        <div class="movie-container d-flex justify-content-center flex-wrap"></div>
        <!--For Pagination Display-->
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
                    <p>Email: nikeshdhakal25@gmail.com</p>
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
        const quickSearchInput = document.getElementById('quickSearchInput');
        const quickSearchButton = document.getElementById('quickSearchButton');
        const favMoviesButton = document.getElementById('favMoviesButton');
        const buttons = document.querySelectorAll('.button-container .curved-button');
        const searchFilterButton = document.querySelector('.search-filter');
        const filterToggle = document.querySelector('.filter-toggle');
        const filterBar = document.querySelector('.filter-bar');
        let userFavorites = []


        //For Notification:
        function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
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

        setTimeout(function() {
            notification.style.opacity = '0';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 300);
        }, 1500);
    }

        // Add click event listener to each button
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                buttons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function fetchUserFavorites() {
            return fetch('./movies/favoriteMovie.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched data:', data); // Log the data to inspect its structure
                    if (Array.isArray(data)) {
                        userFavorites = data.map(favorite => favorite.movieID);
                    } else {
                        console.error('Data is not an array:', data);
                        userFavorites = [];
                    }
                })
                .catch(error => {
                    console.error('Error fetching user favorites:', error);
                    userFavorites = [];
                });
        }

        fetchUserFavorites()
            .then(() => fetchMoviesFromFetchMoviesPHP())
            .catch(error => console.error('Error initializing:', error));

        //Pagination Starts here:
        function fetchMoviesFromFetchMoviesPHP(page = 1) {
            fetch(`./movies/fetch_movies.php?page=${page}`)
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
            fetch('./movies/allMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.movies.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    paginationContainer.style.visibility = 'hidden'; // Hide pagination for all movies
                    document.querySelector('.carousel').style.display = 'none';
                })
                .catch(error => console.error('Error fetching all movies from allMovie.php:', error));
        }

        //For Featured Movies:
        function fetchFeaturedMoviesFromFeatureMoviePHP() {
            fetch('./movies/featureMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    paginationContainer.style.visibility = 'hidden';
                    document.querySelector('.carousel').style.display = 'none';
                })
                .catch(error => console.error('Error fetching featured movies from featureMovie.php:', error));
        }

        //For Latest Movies:
        function fetchLatestMoviesFromLatestPHP() {
            fetch('./movies/latestMovie.php')
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });
                    paginationContainer.style.visibility = 'hidden';
                    document.querySelector('.carousel').style.display = 'none';
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
            fetch(`./movies/quickSearch.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    movieContainer.innerHTML = '';
                    const searchMessage = document.createElement('p');
                    searchMessage.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i> Quick Search: <span class="filter-tag">${searchTerm}</span>`;
                    searchMessage.classList.add('search-message');

                    movieContainer.appendChild(searchMessage);

                    if (data.length === 0) {
                        movieContainer.innerHTML += `<p style="color: red; font-size: 30px; font-weight: 700;">No movies found!</p>`;
                        paginationContainer.style.visibility = 'hidden';
                        document.querySelector('.carousel').style.display = 'none';
                        return;
                    }

                    data.forEach(movie => {
                        const movieCard = createMovieCard(movie);
                        movieContainer.appendChild(movieCard);
                    });

                    paginationContainer.style.visibility = 'hidden';
                    document.querySelector('.carousel').style.display = 'none';
                })
                .catch(error => console.error('Error fetching movies from quickSearch.php:', error));
        }



        // For Favorite Movies:
        favMoviesButton.addEventListener('click', function() {
            fetch('./movies/favoriteMovie.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error' && data.message === 'User not logged in') {
                        movieContainer.innerHTML = `<p style="color: red; font-size: 30px; font-weight: 400;">No favorite movies found!</p>`;
                        paginationContainer.style.visibility = 'hidden';
                        document.querySelector('.carousel').style.display = 'none';
                    } else {
                        movieContainer.innerHTML = '';
                        if (data.length === 0) {
                            movieContainer.innerHTML = `<p style="color: red; font-size: 30px; font-weight: 400;">No favorite movies found!</p>`;
                            paginationContainer.style.visibility = 'hidden';
                            document.querySelector('.carousel').style.display = 'none';
                            return;
                        }
                        data.forEach(movie => {
                            const movieCard = createMovieCard(movie);
                            movieContainer.appendChild(movieCard);
                        });
                        paginationContainer.style.visibility = 'hidden';
                        document.querySelector('.carousel').style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching favorite movies:', error));
        });

        // For Filter Bar to show or hide:
        filterBar.style.display = 'none';
        filterToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            filterBar.style.display = filterBar.style.display === 'none' ? 'block' : 'none';
        });
        document.addEventListener('click', (event) => {
            if (filterBar.style.display === 'block' && !filterToggle.contains(event.target) && !filterBar.contains(event.target)) {
                filterBar.style.display = 'none';
            }
        });
        filterBar.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        // For Filter Movies:
        if (searchFilterButton) {
            searchFilterButton.addEventListener('click', () => {
                const year = document.getElementById('year').value;
                const rating = document.getElementById('rating').value;
                const country = document.getElementById('country').value;
                const genre = document.getElementById('genre').value;
                
                if (!year && !rating && !country && !genre) {
                showNotification('Please select at least one filter option.', 'error');
                return;
            }

                const queryParams = new URLSearchParams({
                    year: year,
                    rating: rating,
                    country: country,
                    genre: genre
                });
                
                fetch(`./movies/filterMovie.php?${queryParams.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        movieContainer.innerHTML = '';
                        const searchMessage = document.createElement('p');
                        searchMessage.innerHTML = `<i class="fa-solid fa-filter"></i> Filter:`;
                        if (year) {
                            searchMessage.innerHTML += ` <span class="filter-tag">${year}</span>`;
                        }
                        if (rating) {
                            searchMessage.innerHTML += ` <span class="filter-tag">${rating}</span>`;
                        }
                        if (country) {
                            searchMessage.innerHTML += ` <span class="filter-tag">${country}</span>`;
                        }
                        if (genre) {
                            searchMessage.innerHTML += ` <span class="filter-tag">${genre}</span>`;
                        }
                        searchMessage.classList.add('search-message');
                        movieContainer.appendChild(searchMessage);

                        if (data.movies.length === 0) {
                            movieContainer.innerHTML += `<p style="color: red; font-size: 30px; font-weight: 700;">No movies found!</p>`;
                            paginationContainer.style.visibility = 'hidden';
                            document.querySelector('.carousel').style.display = 'none';
                            return;
                        }
                        data.movies.forEach(movie => {
                            const movieCard = createMovieCard(movie);
                            movieContainer.appendChild(movieCard);
                        });
                        paginationContainer.style.visibility = 'hidden';
                        document.querySelector('.carousel').style.display = 'none';
                    })
                    .catch(error => console.error('Error fetching filtered movies:', error));
            });
        }

        // For Carousel:
const carouselInner = document.querySelector('.carousel-inner');

fetchMoviesForCarousel();
let chunkSize = getChunkSize();
function fetchMoviesForCarousel() {
    fetch('./movies/carousel.php')
        .then(response => response.json())
        .then(data => {
            carouselInner.innerHTML = '';  // Clear previous carousel items if any

            for (let i = 0; i < data.movies.length; i += chunkSize) {
                const chunk = data.movies.slice(i, i + chunkSize);
                const carouselItem = document.createElement('div');
                carouselItem.classList.add('carousel-item');
                if (i === 0) {
                    carouselItem.classList.add('active');
                }

                const row = document.createElement('div');
                row.classList.add('row', 'w-100', 'd-flex', 'justify-content-center');

                chunk.forEach(movie => {
                    const carouselMovieCard = createCarouselMovieCard(movie, chunkSize);
                    const col = document.createElement('div');
                    if (chunkSize === 1) {
                        col.classList.add('col-12');
                    } else if (chunkSize === 2) {
                        col.classList.add('col-md-6');
                    } else {
                        col.classList.add('col-md-4');
                    }
                    col.appendChild(carouselMovieCard);
                    row.appendChild(col);
                });

                carouselItem.appendChild(row);
                carouselInner.appendChild(carouselItem);
            }
        })
        .catch(error => console.error('Error fetching movies from carousel.php:', error));
}

function createCarouselMovieCard(movie) {
    const card = document.createElement('div');
    card.classList.add('carousel-movie-card', 'mt-2');
    const truncatedDescription = movie.description.length > 100 ? movie.description.substring(0, 100) + '...' : movie.description;
    card.innerHTML = `
        <img src="${movie.poster}" class="card-img-top" alt="${movie.title}">
        <div class="card-body d-flex flex-column justify-content-between">
            <h4 class="card-title pt-3">${movie.title}</h4>
            <div class="footer-content">
                <p class="card-text">${truncatedDescription}</p>
                <h4 class="card-text"><i class="fa-solid fa-heart"></i> ${movie.rating} ⬝ ${movie.release_date} ⬝ ${movie.minute} min</h4>
                <h4 class="card-text" style="color:#d3d3d3; font-weight: 700;">${movie.genre}      ${movie.genre2}</h4>
            </div>
        </div>
    `;
    card.addEventListener('click', function() {
        window.location.href = `./movies/movieOverview.php?title=${encodeURIComponent(movie.title)}`;
    });
    return card;
}

function getChunkSize() {
    if (window.innerWidth < 768) {
        return 1;
    } else if (window.innerWidth < 1200) {
        return 2;
    }
    else {
        return 3;
    }
}

// Re-fetch movies on window resize to adjust chunk size
window.addEventListener('resize', function() {
    const currentChunkSize = getChunkSize();
    if (currentChunkSize !== chunkSize) {
        chunkSize = currentChunkSize;
        fetchMoviesForCarousel();
    }
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
                window.location.href = `./movies/movieOverview.php?title=${encodeURIComponent(movie.title)}`;
            });

            return movieCard;
        }

        //For Favorite Icon Toggle function
        function toggleFavorite(movieID, favoriteIcon) {
            fetch('./movies/favorites.php', {
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
                fetchMoviesFromFetchMoviesPHP();
                paginationContainer.style.visibility = 'visible';
                document.querySelector('.carousel').style.display = 'block';
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
