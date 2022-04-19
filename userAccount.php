<!-- script creates a user profile that allows user to see all the scripts they have created or commented on  -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Account</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <ul>
        <li><a href="mainPage.php">Home</a></li>
        <li><a href="submitStory.php">Submit Story</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    <div class="stories_list">
        <h1>Stories</h1>
        <?php 
            //user should not be able to see page if they have not logged in 
            if(!isset($_SESSION['user_id'])) {
                header("Location: mainPage.php");  
                exit;
            }
            require 'database.php';
            //creates SQL command that pulls all stories authored by user
            $stmt = $mysqli->prepare("select id, story_title from stories where user_id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            }
            $stmt->bind_param('i', $_SESSION["user_id"]);
            
            $stmt->execute();

            $stmt->bind_result($ids, $titles);
            while($stmt->fetch()) { 
                //limits titles shown to 50 characters
                if (strlen($titles) > 50 ) {
                    $shortened_title = substr($titles, 0, 50).'...';
                } else {
                    $shortened_title = $titles;
                }    
            ?>
            <!-- user will be able to edit or delete stories from this page -->
            <div class="story_with_buttons">
                <form action="viewStory.php" method="POST" class="name_form">
                    <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                    <input type="hidden" name="story_id" value="<?php echo htmlentities($ids);?>" />
                    <input class="article_names" name="submit" type="submit" value="<?php echo htmlentities($shortened_title);?>"/>
                </form>
                <div class="edit_delete_buttons">
                    <form action="editStory.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($ids);?>" />
                        <input type="submit" value="Edit Story"/>
                    </form>
                    <form action="deleteStory.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($ids);?>" />
                        <input type="submit" value="Delete Story"/>
                    </form>
                </div>
            </div>
            <?php
            } 
            $stmt->close();
        ?>
        <h1>Comments</h1>   
        <?php 
            //creates SQL command that pulls all comments authored by user
            $stmt = $mysqli->prepare("select comments.id, stories.id, stories.story_title, comments.comment from comments join stories on (comments.story_id=stories.id) where comments.user_id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
            }
            $stmt->bind_param('i', $_SESSION["user_id"]);
            
            $stmt->execute();

            $stmt->bind_result($comment_ids, $story_ids, $story_titles, $comments);
            
            while($stmt->fetch()) { 
                //limits titles shown to 50 characters
                if (strlen($story_titles) > 50 ) {
                    $shortened_title = substr($story_titles, 0, 50).'...';
                } else {
                    $shortened_title = $story_titles;
                }
            ?>
            <!-- user will be able to edit and delete comments from this page -->
                <form action="viewStory.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                    <input type="hidden" name="story_id" value="<?php echo htmlentities($story_ids);?>" />
                    <input class="article_names" name="submit" type="submit" value="<?php echo htmlentities($shortened_title);?>"/>
                </form>
                <div class="story_with_buttons comments_specifcally">
                    <p class="comment name_form"><?php echo $comments;?></p>
                    <div class="edit_delete_buttons">
                        <form action="editComment.php" method="POST" class="edit_delete_button">
                            <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                            <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                            <input type="hidden" name="story_id" value="<?php echo htmlentities($story_ids);?>" />
                            <input type="submit" value="Edit Comment"/>
                        </form>
                        <form action="deleteComment.php" method="POST" class="edit_delete_button">
                            <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                            <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                            <input type="hidden" name="story_id" value="<?php echo htmlentities($story_ids);?>" />
                            <input type="submit" value="Delete Comment"/>
                        </form>
                    </div>
                </div>
            <?php
            } 
            $stmt->close();
        ?>
    </div>
</body>
</html>
