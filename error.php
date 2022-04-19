<!-- Page the user is sent to when sql statement is not properly prepared. -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php  

    if(isset($_SESSION['user_id'])) { 
    ?>
        <ul>
            <li><a href="mainPage.php">Home</a></li>
            <li><a href="userAccount.php">My Account</a></li>
            <li><a href="submitStory.php">Submit Story</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    <?php  } else { ?>
        <ul>
            <li><a href="mainPage.php">Home</a></li>
            <li><a href="login.php">Log In</a></li>
            <li><a href="addUser.php">Create New Account</a></li>
        </ul>
    <?php } 
    
    if (isset($_SESSION['error'])) {
        printf("Failed: %s\n", $_SESSION['error']);
    }
    
    
    ?>
    
    
        
</body>
</html>