<!-- Script creates submit story page for News Site -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Story</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php  
        //user should not be able to access this page if they have not logged in 
        if(!isset($_SESSION['user_id'])) {
            header("Location: mainPage.php");  
            exit;
        } else {
            ?>
        <ul>
            <li><a href="mainPage.php">Home</a></li>
            <li><a href="userAccount.php">My Account</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul> 
        <?php  }
        if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
    ?>
    <div class="stories_list">
        <H1>Submit New Story</H1>

        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="token2" value="<?php echo $_SESSION['token'];?>" />
            <p>
                <label for="title">Story Title: </label>
                <input type="text" name="title" id="title" required/>
            </p>
            <p>
                <label for="url">Story URL: </label>
                <input type="text" name="url" id="url"/>
            </p>
            <p>
                <label for="content">Story Text:</label>
                <textarea id="content" name="content" rows="15" cols="50" required></textarea>
            </p>
            <p>
                <input name="submit" type="submit" value="Submit"/>
            </p>
        </form>
        <?php
            if (isset($_POST["submit"])) {
                if(!hash_equals($_SESSION['token'], $_POST['token2'])){
                    die("Request forgery detected");
                }
                //checks if required inputs have been filled
                if (!isset($_POST["title"]) 
                    || empty($_POST["title"])
                    || !isset($_POST["content"])
                    || empty($_POST["content"])) {
                    echo "One or more required inputs missing.";
                //checks if title is too long 
                } else if (strlen($_POST["title"]) > 250) {
                    echo "Title is too long.";
                //checks if url is too long 
                } else if (strlen($_POST["url"]) > 250) {
                    echo "URL is too long.";
                //checks if url is valid
                } else if (strlen($_POST["url"]) > 0 && !filter_var(filter_var($_POST["url"], FILTER_SANITIZE_URL), FILTER_VALIDATE_URL)) {
                    echo "Invalid URL.";
                } else {
                    require 'database.php';
                    //creates SQL command to insert story to stories table
                    $stmt = $mysqli->prepare("insert into stories (user_id, story_title, story_link, story_body) values (?, ?, ?, ?)");
                    if (!$stmt){
                        printf("Submission Failed: %s\n", $mysqli->error);
                        $_SESSION['error'] = $mysqli->error;
                        header('Location: error.php');
                        exit;
                    } else {
                        //binds paramters
                        $stmt->bind_param('isss',$_SESSION['user_id'], $_POST["title"], $_POST["url"], $_POST["content"]);
                        $stmt->execute();
                        $stmt->close();
                        header("Location: mainPage.php");  
                    }
                }
            }
        ?>
    </div>
</body>
</html>
