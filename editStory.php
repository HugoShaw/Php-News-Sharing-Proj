<!-- script updates story in stories table based on user's edits -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Story</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php  
        //user should not arrive at this page if they or not logged in
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
        require 'database.php';
        //SQL command to retrieve what user previously had for the story
        $stmt = $mysqli->prepare("select story_title, story_link, story_body from stories where id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            $_SESSION['error'] = $mysqli->error;
            header('Location: error.php');
            exit;
        }
        //bind params 
        $stmt->bind_param('d',$_POST['story_id']);
        $stmt->execute();

        //bind result
        $stmt->bind_result($story_title, $story_link, $story_body);
        $stmt->fetch();
        $stmt->close();
    ?>
    <div class="stories_list">
        <H1>Edit Story</H1>
        <!-- form that is prefilled with user's previous story information -->
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="token2" value="<?php echo  htmlentities($_SESSION['token']);?>" />
            <input type="hidden" name="story_id" value="<?php echo  htmlentities($_POST['story_id']);?>" />
            <p>
                <label for="title">Story Title: </label>
                <input type="text" name="title" id="title" value="<?php echo  htmlentities($story_title);?>" required/>
            </p>
            <p>
                <label for="url">Story URL: </label>
                <input type="text" name="url" id="url" value="<?php echo  htmlentities($story_link);?>"/>
            </p>
            <p>
                <label for="content">Story Text:</label>
                <textarea id="content" name="content" rows="15" cols="50" required><?php echo  htmlentities($story_body);?></textarea>
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
                    //creates SQL command to update story selected with new information
                    require 'database.php';
                    $stmt = $mysqli->prepare("update stories set story_title=?, story_link=?, story_body=? where id=?");
                    if (!$stmt){
                        printf("Submission Failed: %s\n", $mysqli->error);
                        $_SESSION['error'] = $mysqli->error;
                        header('Location: error.php');
                        exit;
                    } else {
                        //binds parameters
                        $stmt->bind_param('sssi', $_POST["title"], $_POST["url"], $_POST["content"], $_POST['story_id']);
                        $stmt->execute();
                        $stmt->close();
                        header("Location: mainPage.php");  
                        exit;
                    }
                }
            }
        ?>
    </div>
</body>
</html>
