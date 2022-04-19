<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- script inserts new comment in comments table -->
<?php 
    session_start();
    require 'database.php';
    if(isset($_SESSION['user_id'])) {
        if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        //checks if viewStory.php successfully passed the comment to be added and the related story_id
        if (!isset($_POST["comment"]) || empty($_POST["comment"]) || !isset($_POST["story_id"]) ) {
            header('Location: viewStory.php');
            exit;
        } else {
        //creates SQL statement to insert new comment into comments table
            $stmt = $mysqli->prepare("insert into comments (user_id, story_id, comment) values (?, ?, ?)");
            if (!$stmt){
                printf("Submission Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            } else {
                $stmt->bind_param('iis',$_SESSION['user_id'], $_POST["story_id"], $_POST["comment"]);
                $stmt->execute();
                $stmt->close();  
                header('Location: viewStory.php');
                exit;
            }
        }
    } else {
        header("Location: viewStory.php");  
        exit;
    }
?>