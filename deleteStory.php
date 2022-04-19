<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- script to delete story from stories table -->
<?php
    session_start();
    require 'database.php';
    if(isset($_SESSION['user_id'])) {
        if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        //checks if viewStory.php correctly passed a story_id to delete
        if (!isset($_POST["story_id"])) {
            header('Location: mainPage.php');
            exit;
        } else {
            //first need to delete comments with foreign key story_id
            $stmt = $mysqli->prepare("delete from comments where story_id=?");
            if (!$stmt){
                printf("Submission Failed: %s\n", $mysqli->error); 
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            } else {
                $stmt->bind_param('i',$_POST["story_id"]);
                $stmt->execute();
            }
            //then we can delete the story with primary key story_id
            $stmt->close();  
            $stmt2 = $mysqli->prepare("delete from stories where id=?");
            if (!$stmt2){
                printf("Submission Failed: %s\n", $mysqli->error);
                $_SESSION['error'] = $mysqli->error;
                header('Location: error.php');
                exit;
            } else {
                $stmt2->bind_param('i',$_POST["story_id"]);
                $stmt2->execute();
                $stmt2->close();  
                header('Location: mainPage.php');
                exit;
            }
            
            
        }
    } else {
        header("Location: mainPage.php");  
        exit;
    }

?>