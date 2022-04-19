<!-- Script allows user to view a single story and its comments -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Story</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php  
    // Registered User
    if(isset($_SESSION['user_id'])) { 
    ?>
        <ul>
            <li><a href="mainPage.php">Home</a></li>
            <li><a href="userAccount.php">My Account</a></li>
            <li><a href="submitStory.php">Submit Story</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    <?php  } 
    // Unregistered User
    else { ?>
        <ul>
            <li><a href="mainPage.php">Home</a></li>
            <li><a href="login.php">Log In</a></li>
            <li><a href="addUser.php">Create New Account</a></li>
        </ul>
    <?php } ?>

    <div class="stories_list">
        <?php
            //if user refreshes on this page, then they still have the story_id related to the story they're viewing 
            if (isset($_POST["story_id"])) {
                $_SESSION["story_id"] = $_POST["story_id"];
            }
            //if session variable is not set, the user may be trying to access the script viewStory.php from somewhere other than mainPage.php which is not allowed
            if (!isset($_SESSION["story_id"])) {
                header('Location: mainPage.php');
            }
            $story_id=$_SESSION["story_id"];
            
            // Cross Site Request Forgery Detect
            if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }

            // perform actions 
            require 'database.php';
            $stmt = $mysqli->prepare("select stories.user_id, users.username, stories.story_title, stories.story_link, stories.story_body, stories.update_time, stories.upvotes, stories.downvotes from stories join users on (stories.user_id=users.id) where stories.id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            }
            //bind params 
            $stmt->bind_param('d',$story_id);
            $stmt->execute();

            //bind result
            $stmt->bind_result($user_id, $username, $story_title, $story_link, $story_body, $story_update_time, $story_upvotes, $story_downvotes);
            $stmt->fetch();
            
            printf("<h1>%s</h1>", htmlentities($story_title)); ?>

            <!-- Story Information -->
            <div class="story_info">
            <?php
                printf("<p class='story_info_component'>Author: %s</p>",htmlentities($username)); 
                printf("<p class='story_info_component'>%s</p>",htmlentities(date("m/d/y g:i A", strtotime($story_update_time)))); 
                printf("<p class='story_info_component'>%s Upvotes</p>",htmlentities($story_upvotes));
                printf("<p class='story_info_component'>%s Downvotes</p>",htmlentities($story_downvotes)); 
            ?>
            </div>
            <?php
                printf("<a class='story_link' href='%s'>%s</a>",htmlentities($story_link), htmlentities($story_link)); 
                printf("<p>%s</p>",htmlentities($story_body)); 
            //if the user wrote the story, they can edit or delete the story from this page
            ?>
            <div class="story_actions">
            <?php
                if ($user_id == $_SESSION['user_id']) { ?>
                    <form action="editStory.php" method="POST" class="story_action_component">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="submit" value="Edit Story"/>
                    </form>
                    <form action="deleteStory.php" method="POST" class="story_action_component">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="submit" value="Delete Story"/>
                    </form>
                <?php }
                //if the user is logged in, they can upvote or downvote the article as many times as they want
                if (isset($_SESSION['user_id'])) { ?>
                    <form action="addStoryUpvote.php" method="POST" class="story_action_component">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="hidden" name="story_upvotes" value="<?php echo htmlentities($story_upvotes);?>" />
                        <input type="submit" value="Upvote"/>
                    </form>
                    <form action="addStoryDownvote.php" method="POST" class="story_action_component">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="hidden" name="story_downvotes" value="<?php echo htmlentities($story_downvotes);?>" />
                        <input type="submit" value="Downvote"/>
                    </form>
                <?php } ?>
            </div>
            
           <?php $stmt->close();?>
        <!-- Write Comments -->
        <h1>Comments</h1>
        <?php 
            //if the user is logged in, they can add comments
            if(isset($_SESSION['user_id'])) { ?>
                <form action="addComment.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                    <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                    <p>
                        <textarea id="comment" name="comment" rows="4" cols="50" placeholder="What are your thoughts?" required></textarea>
                    </p>
                    <p>
                        <input name="submitComment" type="submit" value="Comment"/>
                    </p>
                </form>

        <?php } 
        $sort_selection = $_POST['sorting_options']
        ?>
        <!-- Users can sort comments by timestamp and votes -->
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" class="sorter">
            <label for="sorting_options">Sort By:</label>
            <select name="sorting_options" id="sorting_options" onchange="javascript:this.form.submit()">
                <option value="old" <?php if (isset($sort_selection) && $sort_selection=="old") echo "selected";?>>Old</option>
                <option value="new" <?php if (isset($sort_selection) && $sort_selection=="new") echo "selected";?>>New</option>
                <option value="upvotes" <?php if (isset($sort_selection) && $sort_selection=="upvotes") echo "selected";?>>Upvotes</option>
                <option value="downvotes" <?php if (isset($sort_selection) && $sort_selection=="downvotes") echo "selected";?>>Downvotes</option>
            </select>
        </form>
        <?php
            //based on the user's comment sorting selection, the SQL query that pull down the comments changes
            $stmt = $mysqli->prepare("select comments.id, comments.user_id, comments.comment, comments.update_time, users.username, comments.upvotes, comments.downvotes from comments join users on (comments.user_id=users.id) where story_id=?");
            if(isset($_POST['sorting_options'])) { 
                if ($_POST['sorting_options'] == "old") {
                    $stmt = $mysqli->prepare("select comments.id, comments.user_id, comments.comment, comments.update_time, users.username, comments.upvotes, comments.downvotes from comments join users on (comments.user_id=users.id) where story_id=? order by comments.update_time asc");
                } else if ($_POST['sorting_options'] == "new") {
                    $stmt = $mysqli->prepare("select comments.id, comments.user_id, comments.comment, comments.update_time, users.username, comments.upvotes, comments.downvotes from comments join users on (comments.user_id=users.id) where story_id=? order by comments.update_time desc");
                } else if ($_POST['sorting_options'] == "upvotes") {
                    $stmt = $mysqli->prepare("select comments.id, comments.user_id, comments.comment, comments.update_time, users.username, comments.upvotes, comments.downvotes from comments join users on (comments.user_id=users.id) where story_id=? order by comments.upvotes desc");
                } else if ($_POST['sorting_options'] == "downvotes") {
                    $stmt = $mysqli->prepare("select comments.id, comments.user_id, comments.comment, comments.update_time, users.username, comments.upvotes, comments.downvotes from comments join users on (comments.user_id=users.id) where story_id=? order by comments.downvotes desc");
                }
            }
            $stmt->bind_param('i', $story_id);
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            }

            $stmt->execute();
            $stmt->bind_result($comment_ids, $user_ids, $comments, $update_times, $usernames, $upvotes, $downvotes);
            while($stmt->fetch()) { ?>
                <div class="story_info"> 
                    <?php
                    printf("<p class='comment_info_component username'>%s</p>\n",htmlentities($usernames));
                    printf("<p class='comment_info_component'>%s</p>\n",htmlentities(date("m/d/y g:i A", strtotime($update_times))));
                    printf("<p class='comment_info_component'>%s Upvotes</p>\n",htmlentities($upvotes));
                    printf("<p class='comment_info_component'>%s Downvotes</p>\n",htmlentities($downvotes));
                    ?>
                </div>
              
            <div class="story_with_buttons comments_specifcally">
            <?php
                printf("<p class='comment_text name_form'>%s</p>\n",htmlentities($comments));
                //if the user authored the comment, they can edit and delete the comment 
                ?>
                <div class="edit_delete_buttons"> <?php 
                if ($user_ids == $_SESSION["user_id"]) { ?>
                    <form action="editComment.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="submit" value="Edit Comment"/>
                    </form>
                    <form action="deleteComment.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                        <input type="hidden" name="story_id" value="<?php echo htmlentities($story_id);?>" />
                        <input type="submit" value="Delete Comment"/>
                    </form>
            <?php } 
                //if the user is logged in, they can upvote and downvote the comment
                if (isset($_SESSION["user_id"])) { ?>
                    <form action="addCommentUpvote.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                        <input type="hidden" name="comment_upvotes" value="<?php echo htmlentities($upvotes);?>" />
                        <input type="submit" value="Upvote"/>
                    </form>
                    <form action="addCommentDownvote.php" method="POST" class="edit_delete_button">
                        <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                        <input type="hidden" name="comment_id" value="<?php echo htmlentities($comment_ids);?>" />
                        <input type="hidden" name="comment_downvotes" value="<?php echo htmlentities($downvotes);?>" />
                        <input type="submit" value="Downvote"/>
                    </form>
                <?php }?>
                </div> 
            </div>
            <?php
            }
        
        ?>
    </div>
 <script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
        
</body>

</html>

