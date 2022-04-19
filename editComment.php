<!-- script updates comment in comments table based on user input -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Comment</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php  
        //user should not arrive at this page if they or not logged in or if they have not come from viewStory.php
        if(!isset($_SESSION['user_id']) || !isset($_POST['comment_id'])) {
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
        //if user refreshes on this page, then they still have the story_id related to the comment they're editing
        if (isset($_POST["story_id"])) {
            $_SESSION["story_id"] = $_POST["story_id"];
        }
        //if session variable is not set, the user may be trying to access the script from somewhere other than viewStory.php which is not allowed
        if (!isset($_SESSION["story_id"])) {
            header('Location: mainPage.php');
            exit;
        }
        $story_id=$_SESSION["story_id"];
        require 'database.php';
        //SQL command to retrieve what user previously had for the comment
        $stmt = $mysqli->prepare("select comment from comments where id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            $_SESSION['error'] = $mysqli->error;
            header('Location: error.php');
            exit;
        }
        //bind params 
        $stmt->bind_param('d',$_POST['comment_id']);
        $stmt->execute();

        //bind result
        $stmt->bind_result($comment);
        $stmt->fetch();
        $stmt->close();
    ?>
    <div class="stories_list">
        <H1>Edit Comment</H1>
        <!-- form that is prefilled with user's previous comment -->
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="token2" value="<?php echo htmlentities($_SESSION['token']);?>" />
            <input type="hidden" name="comment_id" value="<?php echo htmlentities($_POST['comment_id']);?>" />
            <p>
                <textarea id="comment" name="comment" rows="4" cols="50" required><?php echo  htmlentities($comment);?></textarea>
            </p>
            <p>
                <input name="submitComment" type="submit" value="Update"/>
            </p>
        </form>
        <?php
            if (isset($_POST["submitComment"])) {
                if(!hash_equals($_SESSION['token'], $_POST['token2'])){
                    die("Request forgery detected");
                }
                if (!isset($_POST["comment"]) || empty($_POST["comment"])) {
                    echo "Input is missing.";
                } else {
                    //creates SQL command to update comment 
                    $stmt = $mysqli->prepare("update comments set comment=? where id=?");
                    if (!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        $_SESSION['error'] = $mysqli->error;
                        header('Location: error.php');
                        exit;
                    } else {
                        //binds parameters
                        $stmt->bind_param('si', $_POST["comment"], $_POST['comment_id']);
                        $stmt->execute();
                        $stmt->close();
                        header("Location: viewStory.php");  
                        exit;
                    }
                }
            }
        ?>
   </div>
</body>
</html>
