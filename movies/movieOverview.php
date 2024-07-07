<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "seenema";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getMovieDetails($conn, $movieTitle) {
    $sql = "SELECT movieID, title, director, actor, poster, description, rating, genre, release_date, country, imdbVotes FROM movies WHERE title = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $movieTitle);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie = $result->fetch_assoc();
    $stmt->close();
    return $movie;
}

$movieTitle = isset($_GET['title']) ? $_GET['title'] : '';

if (empty($movieTitle)) {
    die("Movie title is missing.");
}

$movie = getMovieDetails($conn, $movieTitle);

if (!$movie) {
    die("Movie not found.");
}

$sqlAccumulatedRating = "SELECT SUM(user_rating) AS accumulated_rating, COUNT(*) AS total_votes FROM seenepoll WHERE movieID = ?";
$stmtAccumulatedRating = $conn->prepare($sqlAccumulatedRating);
$stmtAccumulatedRating->bind_param("i", $movie['movieID']);
$stmtAccumulatedRating->execute();
$resultAccumulatedRating = $stmtAccumulatedRating->get_result();
$ratingData = $resultAccumulatedRating->fetch_assoc();
$accumulatedRating = $ratingData['accumulated_rating'];
$totalVotes = $ratingData['total_votes'];
$averageRating = $totalVotes > 0 ? $accumulatedRating / $totalVotes : 0;

$sqlReviews = "SELECT sp.user_review, sp.review_title, ud.userName, sp.userID, sp.user_rating 
               FROM seenepoll sp
               INNER JOIN userdata ud ON sp.userID = ud.userID 
               WHERE sp.movieID = ?";
$stmtReviews = $conn->prepare($sqlReviews);
$stmtReviews->bind_param("i", $movie['movieID']);
$stmtReviews->execute();
$resultReviews = $stmtReviews->get_result();
$reviews = $resultReviews->fetch_all(MYSQLI_ASSOC);

$loggedInUserID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$userReviewed = false;

foreach ($reviews as $review) {
    if ($review['userID'] == $loggedInUserID) {
        $userReviewed = true;
    }
}

$sqlComments = "SELECT c.commentID, c.comment, c.comment_date, c.userID, u.userName 
                FROM comments c
                INNER JOIN userdata u ON c.userID = u.userID
                WHERE c.movieID = ?";
$stmtComments = $conn->prepare($sqlComments);
$stmtComments->bind_param("i", $movie['movieID']);
$stmtComments->execute();
$resultComments = $stmtComments->get_result();
$comments = $resultComments->fetch_all(MYSQLI_ASSOC);

$stmtAccumulatedRating->close();
$stmtReviews->close();
$stmtComments->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../seenema_img/seenemaLogo.png">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="movieOverview.css">
    <style>
    #rating-container, #comment-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #111;
    padding: 20px;
    border-radius: 10px;
    z-index: 1;
    color: #fff;
    text-align: center;
    width: 100%;
    max-width: 450px;
    align-items: center;
    justify-content: center;
    overflow: visible;
    padding-top: 60px;
    box-sizing: border-box;
    margin-top: -50px;
}
.review-container{
    margin-bottom: 10px;
    background-color: rgb(39, 39, 39);  
    transition: transform 0.3s, box-shadow 0.3s;
    color: #fff;
}
.comment-container{
margin-bottom: 10px;
border: none;
color: #fff;
background-color: rgb(39, 39, 39);
}
.commentsPreview {
    background-color: rgb(32, 32, 32);
    padding: 10px;
    margin-bottom: 10px;
    border-left: 3px solid #8b18b9;
    transition: all 0.3s ease;
}

.commentsPreview:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 8px rgba(255, 0, 208, 1);
}

.close-button {
    background-color: red;
    border-radius: 5px;
    color: #fff;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
}

.close-button:hover {
    color: #f1c40f;
}

/*Rating Stars:*/
.stars {
    display: flex;
    justify-content: center;
    font-size: 1rem;
    position: absolute;
    top: -20px;
    left: 50%;
    direction: rtl;
    transform: translateX(-50%);
    color: #666;
}

.stars input[type="radio"] {
    display: none;
}

.star {
    font-size: 2em;
    color: gray;
    cursor: pointer;
    transition: color 0.3s;
}

.star:hover,
.star:hover ~ .star {
    color: #0ff113;
}

input[type="radio"]:checked ~ label.star {
    color: #0ff113;
}

.stars label:hover {
    transform: scale(1.5);
}
input[type="radio"]:checked ~ label.star:hover,
input[type="radio"]:checked ~ label.star:hover ~ .star {
    color: #0ff113;
}

#review_title, #userReview {
    width: 95%;
    margin: 10px auto;
    display: block;
    background-color: rgb(39, 39, 39);
    color: #fff;
}

#review_title {
    height: 2rem;
}

#userReview {
    height: 4rem;
    overflow: hidden;
}

#userComment {
    width: 100%;
    display: block;
    background-color: rgb(39, 39, 39);
    color: #a9a9a9;
    height: 3rem;
    margin-bottom: 10px;
    overflow-y: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    border: 1px solid rgb(101, 101, 101);
    overflow: hidden;
}

#userComment::-webkit-scrollbar {
    display: none; 
}
.read-more{
    font-weight: 700;
}
.btn-back {
    background-color: #d30b0b;
    border-radius: 10px;
    color: #fff;
    margin-left: 90%;
}

.btn-back:hover {
    background-color: #aa0000;
    color: #fff;
}
    </style>
</head>
<body>
<div class="container">
    <div class="middle">
        <h1 class="mt-4"><?php echo htmlspecialchars($movie['title']); ?></h1>
        <a href="javascript:history.back()" class="btn btn-back mt-4">Back</a>
        <div class="row align-items-end">
            <div class="col-md-4">
                <img src="<?php echo htmlspecialchars($movie['poster']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            </div>
            <div class="col-md-4">
                <p><strong>Movie:</strong> <b><?php echo htmlspecialchars($movie['title']); ?></b></p>
                <p><strong>Director:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
                <p><strong>Lead Actor:</strong> <?php echo htmlspecialchars($movie['actor']); ?></p>
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                <p><strong>IMDb Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?>/10</p>
                <p><strong>Release-Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($movie['country']); ?></p>
                <p><strong>SeenePoll:</strong> <?php echo number_format($averageRating, 1); ?>/10</p>
                <p><strong>IMDb Votes:</strong> <?php echo number_format($movie['imdbVotes']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($movie['description']); ?></p>
                <?php if (!$userReviewed): ?>
                    <button class="btn btn-success" onclick="openRatePopup()">Rate</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="rating-container">
        <button class="close-button" onclick="closeRatingPopup()"><i class="fas fa-times"></i></button>
        <div class="stars">
            <?php for ($i = 10; $i >= 1; $i--): ?>
                <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" onclick="setRating(<?php echo $i; ?>)">
                <label class="star fas fa-star" for="star<?php echo $i; ?>"></label>
            <?php endfor; ?>
        </div>
        <textarea id="review_title" name="review_title" placeholder="Highlight for your review..." style="display: none;" required></textarea>
        <textarea id="userReview" name="user_review" placeholder="Write your review here..." style="display: none;"></textarea>
        <button type="button" class="btn btn-success mt-3" onclick="submitRating()">Submit</button>
    </div>
    <!--User Review and Comments-->
    <div class="row mt-4 user-feedback">
        <div class="col-md-5 feedback-column">
            <h2>❤️ User Reviews</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="review-container">
                    <strong>Reviewed by: <?php echo htmlspecialchars($review['userName']); ?> - ❤️ <?php echo (int)$review['user_rating']; ?>/10</strong>
                    <p style="font-weight:500; font-size:20px;"><?php echo htmlspecialchars($review['review_title']); ?></p>
                    <?php 
                    $reviewText = htmlspecialchars($review['user_review']);
                    if (strlen($reviewText) > 150): ?>
                        <p style="border-bottom: 1px solid rgb(115, 115, 115); color: #c8c8c8;">
                            <span class="review-text"><?php echo substr($reviewText, 0, 150); ?></span>
                            <span class="review-full" style="display:none;"><?php echo $reviewText; ?></span>
                            <span class="read-more" onclick="toggleContent(this)">. . . Read More</span>
                        </p>
                    <?php else: ?>
                        <p style="border-bottom: 1px solid rgb(115, 115, 115); color: #c8c8c8;"><?php echo $reviewText; ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-5 feedback-column">
            <h2>💭 Comments</h2>
            <div class="comment-container">
                <textarea id="userComment" name="user_comment" placeholder="Comment..." oninput="togglePostButton()"></textarea>
                <button class="btn btn-primary" id="postButton" onclick="submitComment()" style="display:none;">Post</button>
                <?php foreach ($comments as $comment): ?>
                    <div id="comment-<?php echo $comment['commentID']; ?>" class="commentsPreview">
                        <div class="comment-item">
                            <p style="font-size:18px; font-weight:600;">
                                <?php echo htmlspecialchars($comment['userName']); ?>: 
                                <em style="font-size:14px; font-weight:400;">(<?php echo htmlspecialchars($comment['comment_date']); ?>)</em>
                                <?php if ($loggedInUserID && $comment['userID'] == $loggedInUserID): ?>
                                    <button class="btn btn-danger btn-sm float-end" onclick="deleteComment(<?php echo $comment['commentID']; ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php endif; ?>
                            </p>
                            <?php 
                            $commentText = htmlspecialchars($comment['comment']);
                            if (strlen($commentText) > 150): ?>
                                <p class="comment-text" style="color: #c8c8c8;">
                                    <span class="comment-text"><?php echo substr($commentText, 0, 150); ?></span>
                                    <span class="comment-full" style="display:none;"><?php echo $commentText; ?></span>
                                    <span class="read-more" onclick="toggleContent(this)">. . . Read More</span>
                                </p>
                            <?php else: ?>
                                <p class="comment-text" style="color: #c8c8c8;"><?php echo $commentText; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
    <script>
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

        // Creating progress bar
        const progressBar = document.createElement('div');
        progressBar.style.position = 'absolute';
        progressBar.style.bottom = '0';
        progressBar.style.left = '0';
        progressBar.style.width = '100%';
        progressBar.style.height = '2px';
        progressBar.style.backgroundColor = '#fff';
        progressBar.style.transition = 'width 1.5s linear';

        notification.appendChild(progressBar);

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

    function storeNotificationAndReload(message, type) {
        localStorage.setItem('notificationMessage', message);
        localStorage.setItem('notificationType', type);
        location.reload();
    }

    const storedMessage = localStorage.getItem('notificationMessage');
    const storedType = localStorage.getItem('notificationType');
    if (storedMessage && storedType) {
        showNotification(storedMessage, storedType);
        localStorage.removeItem('notificationMessage');
        localStorage.removeItem('notificationType');
    }

    function openRatePopup() {
        <?php if (!isset($_SESSION['userID'])): ?>
            storeNotificationAndReload("Please log in to rate this movie.", 'error');
            return;
        <?php endif; ?>

        document.getElementById('rating-container').style.display = 'block';
    }

    function closeRatingPopup() {
        document.getElementById('rating-container').style.display = 'none';
        location.reload();
    }

    function setRating(rating) {
        document.getElementById('userReview').style.display = 'block';
        document.getElementById('review_title').style.display = 'block';
    }

    function submitRating() {
        const movieID = <?php echo $movie['movieID']; ?>;
        const userID = <?php echo isset($_SESSION['userID']) ? $_SESSION['userID'] : 'null'; ?>;
        const rating = document.querySelector('input[name="rating"]:checked');
        const reviewTitle = document.getElementById('review_title').value;
        const review = document.getElementById('userReview').value;

        if (!rating) {
            showNotification("Please select a rating.", 'error');
            return;
        }

        if (userID == null) {
            storeNotificationAndReload("Please log in to rate this movie.", 'error');
            window.location.href = '../src/logout.php';
            return;
        }

        fetch('rate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `movieID=${movieID}&userID=${userID}&rating=${rating.value}&review_title=${encodeURIComponent(reviewTitle)}&review=${encodeURIComponent(review)}`
        })
        .then(response => {
            if (response.ok) {
                storeNotificationAndReload("Rating submitted successfully!", 'success');
            } else {
                return response.text().then(text => { throw new Error(text); });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function togglePostButton() {
        const commentTextarea = document.getElementById('userComment');
        const postButton = document.getElementById('postButton');

        if (commentTextarea.value.trim() !== '') {
            postButton.style.display = 'block';
        } else {
            postButton.style.display = 'none';
        }
    }

    function submitComment() {
        const movieID = <?php echo $movie['movieID']; ?>;
        const userID = <?php echo isset($_SESSION['userID']) ? $_SESSION['userID'] : 'null'; ?>;
        const comment = document.getElementById('userComment').value;

        if (!comment) {
            showNotification("Please write a comment.", 'error');
            return;
        }

        if (userID == null) {
            storeNotificationAndReload("Please log in to comment on this movie.", 'error');
            return;
        }

        fetch('comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `movieID=${movieID}&userID=${userID}&comment=${encodeURIComponent(comment)}`
        })
        .then(response => {
            if (response.ok) {
                document.getElementById('userComment').value = '';
                togglePostButton();
                storeNotificationAndReload("Comment submitted successfully!", 'success');
            } else {
                return response.text().then(text => { throw new Error(text); });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteComment(commentID) {
        if (confirm("Are you sure you want to delete this comment?")) {
            fetch('delete_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `commentID=${commentID}`
            })
            .then(response => {
                if (response.ok) {
                    storeNotificationAndReload("Comment deleted successfully!", 'error');
                } else {
                    throw new Error('Failed to delete comment.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to delete comment. Please try again.', 'error');
            });
        }
    }

    function toggleContent(span) {
        const parent = span.parentNode;
        const textContainer = parent.querySelector('.review-text, .comment-text');
        const fullContainer = parent.querySelector('.review-full, .comment-full');

        if (textContainer.style.display === 'none') {
            textContainer.style.display = 'inline';
            span.textContent = '. . . Read More';
            fullContainer.style.display = 'none';
        } else {
            textContainer.style.display = 'none';
            span.textContent = 'Read Less';
            fullContainer.style.display = 'inline';
        }
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
