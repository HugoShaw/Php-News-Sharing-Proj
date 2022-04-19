<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- script deletes comment from comments table -->
<?php
    session_start();
    require 'database.php';
    if(isset($_SESSION['user_id'])) {
        if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
         //checks if viewStory.php successfully passed the comment to be deleted
        if (!isset($_POST["comment_id"])) {
            header('Location: viewStory.php');
            exit;
        } else {
            //creates SQL command to delete comment
            $stmt = $mysqli->prepare("delete from comments where id=?");
            if (!$stmt){
                printf("Submission Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            } else {
                //binds parameters
                $stmt->bind_param('i',$_POST["comment_id"]);
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