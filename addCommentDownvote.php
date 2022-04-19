<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- script downvotes comment by one and updates comments table -->
<?php 
    session_start();
    require 'database.php';
    if(isset($_SESSION['user_id'])) {
        if(isset($_SESSION['token']) && isset($_POST['token']) && !hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        //creates SQL statement for update
        $stmt = $mysqli->prepare("update comments set downvotes=? where id=?");
        if (!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            $_SESSION['error'] = $mysqli->error;
            header('Location: error.php');
            exit;
        } else {
            $newDownvotes = $_POST['comment_downvotes'] + 1;
            //binds parameters to SQL statement
            $stmt->bind_param('ii',$newDownvotes, $_POST["comment_id"]);
            $stmt->execute();
            $stmt->close();  
            header('Location: viewStory.php');
            exit;
        }
        
    } else {
        header("Location: viewStory.php");  
        exit;
    }
?>