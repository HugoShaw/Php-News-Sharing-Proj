<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- Script creates the main page of the site where users can see all stories at once -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Simple News Web Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        unset($_SESSION["story_id"]); //resets the story_id from viewStory.php

        // Show different navigation menus based on whether the user is logged in or not
        // Registered User
        if(isset($_SESSION['user_id'])) { 
            //SQL command to get the username of logged in user
            require 'database.php';
            $stmt = $mysqli->prepare("select username from users where id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            //bind params 
            $stmt->bind_param('i',$_SESSION['user_id']);
            $stmt->execute();

            //bind result
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();
    ?>
        <ul>
            <li><p id="logged_in_as"><?php echo htmlentities("You are logged in as $username");?></p></li>
            <li><a href="userAccount.php">My Account</a></li>
            <li><a href="submitStory.php">Submit Story</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <!-- Unregistered Users  -->
    <?php  } else { ?>
        <ul>
            <li><a href="login.php">Log In</a></li>
            <li><a href="addUser.php">Create New Account</a></li>
        </ul>
    <?php } 
        $sort_selection = $_POST['sorting_options']
    ?>
    <div class="stories_list">
        <h1>Stories</h1>
        <!-- Form to sort the stories by timestamp and votes -->
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" class="sorter">
            <label for="sorting_options">Sort By:</label>
            <!-- onchange: once the sort option changes, the sorting changes  -->
            <select name="sorting_options" id="sorting_options" onchange="javascript:this.form.submit()">
                <option value="old" <?php if (isset($sort_selection) && $sort_selection=="old") echo "selected";?>>Old</option>
                <option value="new" <?php if (isset($sort_selection) && $sort_selection=="new") echo "selected";?>>New</option>
                <option value="upvotes" <?php if (isset($sort_selection) && $sort_selection=="upvotes") echo "selected";?>>Upvotes</option>
                <option value="downvotes" <?php if (isset($sort_selection) && $sort_selection=="downvotes") echo "selected";?>>Downvotes</option>
            </select>
        </form>
        
        <!-- Unregistered or Registered can view stories -->
        <?php
            require 'database.php';
            //change the SQL query based on the sort seletected in the form above 
            $stmt = $mysqli->prepare("select stories.id, users.username, stories.story_title, stories.upvotes, stories.downvotes, stories.update_time from stories join users on (stories.user_id=users.id)");
            if(isset($_POST['sorting_options'])) { 
                if ($_POST['sorting_options'] == "old") {
                    $stmt = $mysqli->prepare("select stories.id, users.username, stories.story_title, stories.upvotes, stories.downvotes, stories.update_time from stories join users on (stories.user_id=users.id) order by stories.update_time asc");
                } else if ($_POST['sorting_options'] == "new") {
                    $stmt = $mysqli->prepare("select stories.id, users.username, stories.story_title, stories.upvotes, stories.downvotes, stories.update_time from stories join users on (stories.user_id=users.id) order by stories.update_time desc");
                } else if ($_POST['sorting_options'] == "upvotes") {
                    $stmt = $mysqli->prepare("select stories.id, users.username, stories.story_title, stories.upvotes, stories.downvotes, stories.update_time from stories join users on (stories.user_id=users.id) order by stories.upvotes desc");
                } else if ($_POST['sorting_options'] == "downvotes") {
                    $stmt = $mysqli->prepare("select stories.id, users.username, stories.story_title, stories.upvotes, stories.downvotes, stories.update_time from stories join users on (stories.user_id=users.id) order by stories.downvotes desc");
                }
            }
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->execute();

            $stmt->bind_result($story_ids, $usernames, $story_titles, $story_upvotes, $story_downvotes, $story_update_times);

            
            while($stmt->fetch()) { 
                //shortens the title if it is larger than 50 characters
                if (strlen($story_titles) > 50 ) {
                    $shortened_title = substr($story_titles, 0, 50).'...';
                } else {
                    $shortened_title = $story_titles;
                }
                
            ?>
            <!-- creates a form for each story that will trigger viewStory.php when clicked, passing on the story_id of the story clicked -->
                <form action="viewStory.php" method="POST" class="articles">
                    <input type="hidden" name="token" value="<?php echo htmlentities($_SESSION['token']);?>" />
                    <input type="hidden" name="story_id" value="<?php echo htmlentities($story_ids);?>" />
                    <input class="article_names" name="submit" type="submit" value="<?php echo htmlentities($shortened_title);?>"/>
                    <div class="story_info">
                        <p class="story_info_component">Author: <?php echo htmlentities($usernames);?><p>
                        <p class="story_info_component"><?php echo htmlentities(date("m/d/y g:i A", strtotime($story_update_times)));?><p>
                        <p class="story_info_component"><?php echo htmlentities($story_upvotes);?> Upvotes<p>
                        <p class="story_info_component"><?php echo htmlentities($story_downvotes);?> Downvotes<p>
                    </div>
                </form>
            
            <?php
            } $stmt->close();
            ?>
    </div>
</body>
</html>
