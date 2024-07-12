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
    $sql = "SELECT movieID, title, director, actor, poster, description, rating, genre, genre2, actor2, minute, release_date, country, imdbVotes FROM movies WHERE title = ?";
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
                <div class="movie-info">
                    <p><strong>Movie:</strong> <b><?php echo htmlspecialchars($movie['title']); ?></b></p>
                    <p><strong>Director:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
                    <p><strong>Actors:</strong> <?php echo htmlspecialchars($movie['actor']) . ', ' . htmlspecialchars($movie['actor2']); ?></p>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']) . ', ' . htmlspecialchars($movie['genre2']); ?></p>
                    <p><strong>IMDb:</strong> <?php echo htmlspecialchars($movie['rating']); ?>/10</p>
                    <p><strong>Release:</strong> <em>( <?php echo htmlspecialchars($movie['release_date']); ?> )</em></p>
                    <p><strong>Country:</strong> <?php echo htmlspecialchars($movie['country']); ?></p>
                    <p><strong>SeenePoll:</strong> <?php echo number_format($averageRating, 1); ?>/10</p>
                    <p><strong>IMDb Votes:</strong> <?php echo number_format($movie['imdbVotes']); ?></p>
                    <p><strong>Duration:</strong> <?php echo number_format($movie['minute']); ?> min</p>
                    <p><?php echo htmlspecialchars($movie['description']); ?></p>
                    <?php if (!$userReviewed): ?>
                        <button class="btn btn-success" onclick="openRatePopup()">Rate</button>
                    <?php endif; ?>
                </div>
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
                    <strong class="rounded-pill">Reviewed by: <?php echo htmlspecialchars($review['userName']); ?> - ❤️ <?php echo (int)$review['user_rating']; ?>/10</strong>
                    <p style="font-weight:500; font-size:20px;"><?php echo htmlspecialchars($review['review_title']); ?></p>
                    <?php 
                    $reviewText = htmlspecialchars($review['user_review']);
                    if (strlen($reviewText) > 150): ?>
                        <p style="border-bottom: 1px solid rgb(115, 115, 115); color: #c8c8c8;">
                            <span class="review-text"><?php echo substr($reviewText, 0, 150); ?></span>
                            <span class="review-full" style="display:none;"><?php echo $reviewText; ?></span>
                            <span class="read-more" onclick="toggleContent(this)">... Read More</span>
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
                                    <span class="read-more" onclick="toggleContent(this)">... Read More</span>
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

    //For Notification
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

    //For Censoring Words:
    function censorVulgar(text) {
        const words = text.toLowerCase().split(" ");
        return words.map(word => vulgar.includes(word) ? "****" : word).join(" ");
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
        let reviewTitle = document.getElementById('review_title').value;
        let review = document.getElementById('userReview').value;

        if (!rating) {
            showNotification("Please select a rating.", 'error');
            return;
        }

        if (userID == null) {
            storeNotificationAndReload("Please log in to rate this movie.", 'error');
            window.location.href = '../src/logout.php';
            return;
        }
        // Censor vulgar words
        reviewTitle = censorVulgar(reviewTitle);
        review = censorVulgar(review);

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
        let comment = document.getElementById('userComment').value;

        if (!comment) {
            showNotification("Please write a comment.", 'error');
            return;
        }

        if (userID == null) {
            storeNotificationAndReload("Please log in to comment on this movie.", 'error');
            return;
        }

        // Censor vulgar words
        comment = censorVulgar(comment);

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

    //Vulgar Words:
    const vulgar = [
        "2 girls 1 cup","2g1c","4r5e","5h1t","5hit","a$$","a$$hole","a_s_s","a2m","a54","a55","a55hole","aeolus","ahole","alabama hot pocket","alaskan pipeline","anal","anal impaler","anal leakage","analannie","analprobe","analsex","anilingus","anus","apeshit","ar5e","areola","areole","arian","arrse","arse","arsehole","aryan","ass","ass fuck","ass hole","assault","assbag","assbagger","assbandit","assbang","assbanged","assbanger","assbangs","assbite","assblaster","assclown","asscock","asscracker","asses","assface","assfaces","assfuck","assfucker","ass-fucker","assfukka","assgoblin","assh0le","asshat","ass-hat","asshead","assho1e","asshole","assholes","asshopper","asshore","ass-jabber","assjacker","assjockey","asskiss","asskisser","assklown","asslick","asslicker","asslover","assman","assmaster","assmonkey","assmucus","assmunch","assmuncher","assnigger","asspacker","asspirate","ass-pirate","asspuppies","assranger","assshit","assshole","asssucker","asswad","asswhole","asswhore","asswipe","asswipes","auto erotic","autoerotic","axwound","azazel","azz","b!tch","b00bs","b17ch","b1tch","babe","babeland","babes","baby batter","baby juice","badfuck","ball gag","ball gravy","ball kicking","ball licking","ball sack","ball sucking","ballbag","balllicker","balls","ballsack","bampot","bang","bang (one's) box","bangbros","banger","banging","bareback","barely legal","barenaked","barf","barface","barfface","bastard","bastardo","bastards","bastinado","batty boy","bawdy","bazongas","bazooms","bbw","bdsm","beaner","beaners","beardedclam","beastial","beastiality","beatch","beater","beatyourmeat","beaver","beaver cleaver","beaver lips","beef curtain","beef curtains","beer","beeyotch","bellend","bender","beotch","bestial","bestiality","bi+ch","biatch","bicurious","big black","big breasts","big knockers","big tits","bigbastard","bigbutt","bigger","bigtits","bimbo","bimbos","bint","birdlock","bisexual","bi-sexual","bitch","bitch tit","bitchass","bitched","bitcher","bitchers","bitches","bitchez","bitchin","bitching","bitchtits","bitchy","black cock","blonde action","blonde on blonde action","bloodclaat","bloody","bloody hell","blow","blow job","blow me","blow mud","blow your load","blowjob","blowjobs","blue waffle","blumpkin","boang","bod","bodily","bogan","bohunk","boink","boiolas","bollick","bollock","bollocks","bollok","bollox","bomd","bondage","bone","boned","boner","boners","bong","boob","boobies","boobs","booby","booger","bookie","boong","boonga","booobs","boooobs","booooobs","booooooobs","bootee","bootie","booty","booty call","booze","boozer","boozy","bosom","bosomy","bowel","bowels","bra","brassiere","breast","breastjob","breastlover","breastman","breasts","breeder","brotherfucker","brown showers","brunette action","buceta","bugger","buggered","buggery","bukkake","bull shit","bullcrap","bulldike","bulldyke","bullet vibe","bullshit","bullshits","bullshitted","bullturds","bum","bum boy","bumblefuck","bumclat","bumfuck","bummer","bung","bung hole","bunga","bunghole","bunny fucker","bust a load","busty","butchdike","butchdyke","butt","butt fuck","butt plug","buttbang","butt-bang","buttcheeks","buttface","buttfuck","butt-fuck","buttfucka","buttfucker","butt-fucker","butthead","butthole","buttman","buttmuch","buttmunch","buttmuncher","butt-pirate","buttplug","c.0.c.k","c.o.c.k.","c.u.n.t","c0ck","c-0-c-k","c0cksucker","caca","cahone","camel toe","cameltoe","camgirl","camslut","camwhore","carpet muncher","carpetmuncher","cawk","cervix","chesticle","chi-chi man","chick with a dick","child-fucker","chin","chinc","chincs","chink","chinky","choad","choade","choc ice","chocolate rosebuds","chode","chodes","chota bags","cipa","circlejerk","cl1t","cleveland steamer","climax","clit","clit licker","clitface","clitfuck","clitoris","clitorus","clits","clitty","clitty litter","clogwog","clover clamps","clunge","clusterfuck","cnut","cocain","cocaine","cock","c-o-c-k","cock pocket","cock snot","cock sucker","cockass","cockbite","cockblock","cockburger","cockeye","cockface","cockfucker","cockhead","cockholster","cockjockey","cockknocker","cockknoker","cocklicker","cocklover","cocklump","cockmaster","cockmongler","cockmongruel","cockmonkey","cockmunch","cockmuncher","cocknose","cocknugget","cocks","cockshit","cocksmith","cocksmoke","cocksmoker","cocksniffer","cocksucer","cocksuck","cocksuck ","cocksucked","cocksucker","cock-sucker","cocksuckers","cocksucking","cocksucks","cocksuka","cocksukka","cockwaffle","coffin dodger","coital","cok","cokmuncher","coksucka","commie","condom","coochie","coochy","coon","coonnass","coons","cooter","cop some wood","coprolagnia","coprophilia","corksucker","cornhole","corp whore","cox","crabs","crack","cracker","crackwhore","crack-whore","crap","crappy","creampie","cretin","crikey","cripple","crotte","cum","cum chugger","cum dumpster","cum freak","cum guzzler","cumbubble","cumdump","cumdumpster","cumguzzler","cumjockey","cummer","cummin","cumming","cums","cumshot","cumshots","cumslut","cumstain","cumtart","cunilingus","cunillingus","cunn","cunnie","cunnilingus","cunntt","cunny","cunt","c-u-n-t","cunt hair","cuntass","cuntbag","cuntface","cuntfuck","cuntfucker","cunthole","cunthunter","cuntlick","cuntlick ","cuntlicker","cuntlicker ","cuntlicking","cuntrag","cunts","cuntsicle","cuntslut","cunt-struck","cuntsucker","cut rope","cyalis","cyberfuc","cyberfuck","cyberfucked","cyberfucker","cyberfuckers","cyberfucking","cybersex","d0ng","d0uch3","d0uche","d1ck","d1ld0","d1ldo","dago","dagos","dammit","damn","damned","damnit","darkie","darn","date rape","daterape","dawgie-style","deep throat","deepthroat","deggo","dendrophilia","dick","dick head","dick hole","dick shy","dickbag","dickbeaters","dickbrain","dickdipper","dickface","dickflipper","dickfuck","dickfucker","dickhead","dickheads","dickhole","dickish","dick-ish","dickjuice","dickmilk","dickmonger","dickripper","dicks","dicksipper","dickslap","dick-sneeze","dicksucker","dicksucking","dicktickler","dickwad","dickweasel","dickweed","dickwhipper","dickwod","dickzipper","diddle","dike","dildo","dildos","diligaf","dillweed","dimwit","dingle","dingleberries","dingleberry","dink","dinks","dipship","dipshit","dirsa","dirty","dirty pillows","dirty sanchez","dlck","dog style","dog-fucker","doggie style","doggiestyle","doggie-style","doggin","dogging","doggy style","doggystyle","doggy-style","dolcett","domination","dominatrix","dommes","dong","donkey punch","donkeypunch","donkeyribber","doochbag","doofus","dookie","doosh","dopey","double dong","double penetration","doublelift","douch3","douche","douchebag","douchebags","douche-fag","douchewaffle","douchey","dp action","drunk","dry hump","duche","dumass","dumb ass","dumbass","dumbasses","dumbcunt","dumbfuck","dumbshit","dummy","dumshit","dvda","dyke","dykes","eat a dick","eat hair pie","eat my ass","eatpussy","ecchi","ejaculate","ejaculated","ejaculates","ejaculating","ejaculatings","ejaculation","ejakulate","enlargement","erect","erection","erotic","erotism","escort","essohbee","eunuch","extacy","extasy","f u c k","f u c k e r","f.u.c.k","f_u_c_k","f4nny","facefucker","facial","fack","fag","fagbag","fagfucker","fagg","fagged","fagging","faggit","faggitt","faggot","faggotcock","faggots","faggs","fagot","fagots","fags","fagtard","faig","faigt","fanny","fannybandit","fannyflaps","fannyfucker","fanyy","fart","fartknocker","fastfuck","fat","fatass","fatfuck","fatfucker","fcuk","fcuker","fcuking","fecal","feck","fecker","felch","felcher","felching","fellate","fellatio","feltch","feltcher","female squirting","femdom","fenian","figging","fingerbang","fingerfuck","fingerfuck ","fingerfucked","fingerfucker","fingerfucker ","fingerfuckers","fingerfucking","fingerfucks","fingering","fist fuck","fisted","fistfuck","fistfucked","fistfucker","fistfucker ","fistfuckers","fistfucking","fistfuckings","fistfucks","fisting","fisty","flamer","flange","flaps","fleshflute","flog the log","floozy","foad","foah","fondle","foobar","fook","fooker","foot fetish","footfuck","footfucker","footjob","footlicker","foreskin","freakfuck","freakyfucker","freefuck","freex","frigg","frigga","frotting","fubar","fuc","fuck","f-u-c-k","fuck buttons","fuck hole","fuck off","fuck puppet","fuck trophy","fuck yo mama","fuck you","fucka","fuckass","fuck-ass","fuckbag","fuck-bitch","fuckboy","fuckbrain","fuckbutt","fuckbutter","fucked","fuckedup","fucker","fuckers","fuckersucker","fuckface","fuckfreak","fuckhead","fuckheads","fuckher","fuckhole","fuckin","fucking","fuckingbitch","fuckings","hatta","puta","hijodeputa","landwa","gaand","chak","chaak","randimuji","malfaluwa","lafanga","randwa","raand","chudakkar","chudakar","fuckingshitmotherfucker","fuckme","fuckme ","fuckmeat","fuckmehard","fuckmonkey","fucknugget","xoluwa","fucknut","fucknutt","fuckoff","fucks","fuckstick","fucktard","fuck-tard","fucktards","fucktart","fucktoy","fucktwat","fuckup","fuckwad","fuckwhit","fuckwhore","fuckwit","fuckwitt","fuckyou","fudge packer","fudgepacker","fudge-packer","fuk","fuker","fukker","fukkers","fukkin","fuks","fukwhit","fukwit","fuq","futanari","fux","fux0r","fvck","fxck","gae","gai","gang bang","gangbang","gang-bang","gangbanged","gangbangs","ganja","gash","gassy ass","gay sex","gayass","gaybob","gaydo","gayfuck","gayfuckist","gaylord","gays","gaysex","gaytard","gaywad","gender bender","genitals","gey","gfy","ghay","ghey","giant cock","gigolo","ginger","gippo","girl on","girl on top","girls gone wild","git","glans","goatcx","goatse","god damn","godamn","godamnit","goddam","god-dam","goddammit","goddamn","goddamned","god-damned","goddamnit","goddamnmuthafucker","godsdamn","gokkun","golden shower","goldenshower","golliwog","gonad","gonads","gonorrehea","goo girl","gooch","goodpoop","gook","gooks","goregasm","gotohell","gringo","grope","group sex","gspot","g-spot","gtfo","guido","guro","h0m0","h0mo","ham flap","hand job","handjob","hard core","hard on","hardcore","hardcoresex","he11","headfuck","hebe","heeb","hell","hemp","hentai","heroin","herp","herpes","herpy","heshe","he-she","hitler","hiv","ho","hoar","hoare","hobag","hoe","hoer","holy shit","hom0","homey","homo","homodumbshit","homoerotic","homoey","honkey","honky","hooch","hookah","hooker","hoor","hootch","hooter","hooters","hore","horniest","horny","hot carl","hot chick","hotpussy","hotsex","how to kill","how to murdep","how to murder","huge fat","hump","humped","humping","hun","hussy","hymen","iap","iberian slap","inbred","incest","injun","intercourse","j3rk0ff","jack off","jackass","jackasses","jackhole","jackoff","jack-off","jaggi","jagoff","jail bait","jailbait","jap","japs","jelly donut","jerk","jerk off","jerk0ff","jerkass","jerked","jerkoff","jerk-off","jigaboo","jiggaboo","jiggerboo","jism","jiz","jizm","jizz","jizzed","jock","juggs","jungle bunny","junglebunny","junkie","junky","kafir","kawk","kike","kikes","kill","kinbaku","kinkster","kinky","kkk","klan","knob","knob end","knobbing","knobead","knobed","knobend","knobhead","knobjocky","knobjokey","kock","kondum","kondums","kooch","kooches","kootch","kraut","kum","kummer","kumming","kums","kunilingus","kunja","kunt","kwif","kyke","l3i+ch","l3itch","labia","lameass","lardass","leather restraint","leather straight jacket","lech","lemon party","leper","lesbian","lesbians","lesbo","lesbos","lez","lezbian","lezbians","lezbo","lezbos","lezza","lezzie","lezzies","lezzy","lmao","lmfao","loin","loins","lolita","looney","lovemaking","lube","lust","lusting","lusty","m0f0","m0fo","m45terbate","ma5terb8","ma5terbate","mafugly","make me come","male squirting","mams","masochist","massa","masterb8","masterbat","masterbat3","masterbate","master-bate","masterbating","masterbation","masterbations","masturbate","masturbating","masturbation","maxi","mcfagget","menage a trois","menses","menstruate","menstruation","meth","m-fucking","mick","middle finger","midget","milf","minge","minger","missionary position","mof0","mofo","mo-fo","molest","mong","moo moo foo foo","moolie","moron","mothafuck","mothafucka","mothafuckas","mothafuckaz","mothafucked","mothafucker","mothafuckers","mothafuckin","mothafucking","mothafuckings","mothafucks","mother fucker","motherfuck","motherfucka","motherfucked","motherfucker","motherfuckers","motherfuckin","motherfucking","motherfuckings","motherfuckka","motherfucks","mound of venus","mr hands","mtherfucker","mthrfucker","mthrfucking","muff","muff diver","muff puff","muffdiver","muffdiving","munging","munter","murder","mutha","muthafecker","muthafuckaz","muthafuckker","muther","mutherfucker","mutherfucking","muthrfucking","n1gga","n1gger","nad","nads","naked","nambla","napalm","nappy","nawashi","nazi","nazism","need the dick","negro","neonazi","nig nog","nigaboo","nigg3r","nigg4h","nigga","niggah","niggas","niggaz","nigger","niggers","niggle","niglet","nig-nog","nimphomania","nimrod","ninny","nipple","nipples","nob","nob jokey","nobhead","nobjocky","nobjokey","nonce","nooky","nsfw images","nude","nudity","numbnuts","nut butter","nut sack","nutsack","nutter","nympho","nymphomania","octopussy","old bag","omg","omorashi","one cup two girls","one guy one jar","opiate","opium","oral","orally","organ","orgasim","orgasims","orgasm","orgasmic","orgasms","orgies","orgy","ovary","ovum","ovums","p.u.s.s.y.","p0rn","paddy","paedophile","paki","panooch","pansy","pantie","panties","panty","pastie","pasty","pawn","pcp","pecker","peckerhead","pedo","pedobear","pedophile","pedophilia","pedophiliac","pee","peepee","pegging","penetrate","penetration","penial","penile","penis","penisbanger","penisfucker","penispuffer","perversion","peyote","phalli","phallic","phone sex","phonesex","phuck","phuk","phuked","phuking","phukked","phukking","phuks","phuq","piece of shit","pigfucker","pikey","pillowbiter","pimp","pimpis","pinko","piss","piss off","piss pig","pissed","pissed off","pisser","pissers","pisses","pissflaps","pissin","pissing","pissoff","piss-off","pisspig","playboy","pleasure chest","pms","polack","pole smoker","polesmoker","pollock","ponyplay","poof","poon","poonani","poonany","poontang","poop","poop chute","poopchute","poopuncher","porch monkey","porchmonkey","porn","porno","pornography","pornos","pot","potty","prick","pricks","prickteaser","prig","prince albert piercing","prod","pron","prostitute","prude","psycho","pthc","pube","pubes","pubic","pubis","punani","punanny","punany","punkass","punky","punta","puss","pusse","pussi","pussies","pussy","pussy fart","pussy palace","pussylicking","pussypounder","pussys","pust","puto","queaf","queef","queer","queerbait","queerhole","queero","queers","quicky","quim","racy","raghead","raging boner","rape","raped","raper","rapey","raping","rapist","raunch","rectal","rectum","rectus","reefer","reetard","reich","renob","retard","retarded","reverse cowgirl","revue","rimjaw","rimjob","rimming","ritard","rosy palm","rosy palm and her 5 sisters","rtard","r-tard","rubbish","rum","rump","rumprammer","ruski","rusty trombone","s hit","s&m","s.h.i.t.","s.o.b.","s_h_i_t","s0b","sadism","sadist","sambo","sand nigger","sandbar","sandler","sandnigger","sanger","santorum","sausage queen","scag","scantily","scat","schizo","schlong","scissoring","screw","screwed","screwing","scroat","scrog","scrot","scrote","scrotum","scrud","scum","seaman","seamen","seduce","seks","semen","sex","sexo","sexual","sexy","sh!+","sh!t","sh1t","s-h-1-t","shag","shagger","shaggin","shagging","shamedame","shaved beaver","shaved pussy","shemale","shi+","shibari","shirt lifter","shit","s-h-i-t","shit ass","shit fucker","shitass","shitbag","shitbagger","shitblimp","shitbrains","shitbreath","shitcanned","shitcunt","shitdick","shite","shiteater","shited","shitey","shitface","shitfaced","shitfuck","shitfull","shithead","shitheads","shithole","shithouse","shiting","shitings","shits","shitspitter","shitstain","shitt","shitted","shitter","shitters","shittier","shittiest","shitting","shittings","shitty","shiz","shiznit","shota","shrimping","sissy","skag","skank","skeet","skullfuck","slag","slanteye","slave","sleaze","sleazy","slope","slut","slut bucket","slutbag","slutdumper","slutkiss","sluts","smartass","smartasses","smeg","smegma","smut","smutty","snatch","sniper","snowballing","snuff","s-o-b","sod off","sodom","sodomize","sodomy","son of a bitch","son of a motherless goat","son of a whore","son-of-a-bitch","souse","soused","spac","spade","sperm","spic","spick","spik","spiks","splooge","splooge moose","spooge","spook","spread legs","spunk","steamy","stfu","stiffy","stoned","strap on","strapon","strappado","strip","strip club","stroke","stupid","style doggy","suck","suckass","sucked","sucking","sucks","suicide girls","sultry women","sumofabiatch","swastika","swinger","t1t","t1tt1e5","t1tties","taff","taig","tainted love","taking the piss","tampon","tard","tart","taste my","tawdry","tea bagging","teabagging","teat","teets","teez","terd","teste","testee","testes","testical","testicle","testis","threesome","throating","thrust","thug","thundercunt","tied up","tight white","tinkle","tit","tit wank","titfuck","titi","tities","tits","titt","tittie5","tittiefucker","titties","titty","tittyfuck","tittyfucker","tittywank","titwank","toke","tongue in a","toots","topless","tosser","towelhead","tramp","tranny","transsexual","trashy","tribadism","trumped","tub girl","tubgirl","turd","tush","tushy","tw4t","twat","twathead","twatlips","twats","twatty","twatwaffle","twink","twinkie","two fingers","two fingers with tongue","two girls one cup","twunt","twunter","ugly","unclefucker","undies","undressing","unwed","upskirt","urethra play","urinal","urine","urophilia","uterus","uzi","v14gra","v1gra","vag","vagina","vajayjay","va-j-j","valium","venus mound","veqtable","viagra","vibrator","violet wand","virgin","vixen","vjayjay","vodka","vomit","vorarephilia","voyeur","vulgar","vulva","w00se","wad","wang","wank","wanker","wankjob","wanky","wazoo","wedgie","weed","weenie","weewee","weiner","weirdo","wench","wet dream","wetback","wh0re","wh0reface","white power","whitey","whiz","whoar","whoralicious","whore","whorealicious","whorebag","whored","whoreface","whorehopper","whorehouse","whores","whoring","wigger","willies","willy","window licker","wiseass","wiseasses","wog","womb","woody","wop","wrapping men","wrinkled starfish","wtf","xrated","x-rated","xx","xxx","yaoi","yeasty","yellow showers","yid","yiffy","yobbo","zoophile","zoophilia","zubb","muji","lado","puti","xakka","randi","machikney","valu","kando","asshole", "bastard", "bitch", "cock", "cunt", "dick", "douchebag", "fuck", 
    "motherfucker", "pussy", "shit", "slut", "whore", "twat", "faggot", "nigger", "nigga", 
    "dyke", "retard", "spastic", "wanker", "prick", "cuck", "fag", "cum", "jerk", 
    "arsehole", "bellend", "knob", "piss", "skank", "turd", "mong", "chav", "minger", 
    "tosser", "nonce", "scumbag", "gimp", "bum", "arse", "cocksucker", 
    "motherlicker", "douche", "sucker", "bugger", "shithead", "asswipe", "dickhead", 
    "fuckwit", "muff", "bozo", "assclown", "fucktard", "jackass", "twatwaffle", 
    "cockwomble", "shitbag", "asshat", "dumbass", "shitstain", "nutjob", "dipshit", 
    "pisshead", "fucknugget", "asslicker", "buttmonkey", "knobhead", "lowlife", 
    "douchecanoe", "scrote", "bellend", "cockhead", "shitlord", "thundercunt", 
    "dickwad", "cuntcake", "dickweed", "fuckface", "dickbag", "cockbite", 
    "assmunch", "arsewipe", "twunt", "cockjockey","chut","kameena", "haraami", "kutta", "chutiya", "gadha", "madarchod", "behenchod", 
    "bhosadi", "lund", "launda", "sala", "haramkhor", "haramzada", "fattu", "chor", "bhadwa", "randi", "rundi", "hijra", "chakka", "kuttiya", "chinal", "raandi",
    "bhootni", "bhadwi", "bhosdike", "gandu", "bakchod", "hijre", "chakke", "choot", "suar", "saali", "chikni", "chodd", "choduwa", "randikoxora", "randikoxori",
    "radikoxori", "radikoxora", "chuti", "chut"
];
</script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
