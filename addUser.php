<!-- script adds new user to the users table -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registration</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
    <meta content="">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <ul>
        <li><a href="mainPage.php">Home</a></li>
        <li><a href="login.php">Log In</a></li>
    </ul> 
    <div class="stories_list">
    <H1>Create New Account</H1>
    <!-- self-submitting POST form that accepts new users input  -->
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
            <label for="username">Username: </label>
            <input type="text" name="username" id="username" required/>
        </p>
        <p>
            <label for="password">Password: </label>
            <input type="password" name="password" id="password" required/>
        </p>
	    <p>
		    <input name="register" type="submit" value="Register"/>
	    </p>
    </form>

    <?php
        if (isset($_POST['register'])){

            $username = $_POST["username"];
            $password = $_POST["password"];
            require 'database.php';
            //check if username and password are valid in content/size
            if (!preg_match('/^[\w_\-]+$/', $username) || strlen($username) > 50){
                echo "Invalid username";
            } else if (strlen($password) > 50){
                echo "Invalid password";
            } else {
                $hash_pass = password_hash($password, PASSWORD_BCRYPT);
                //checks if username already taken
                $stmt = $mysqli->prepare("select COUNT(*) from users where username=?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    $_SESSION['error'] = $mysqli->error;
                    header('Location: error.php');
                    exit;
                }
                //bind params 
                $stmt->bind_param('s',$username);
                $stmt->execute();

                //bind result
                $stmt->bind_result($cnt);
                $stmt->fetch();
                $stmt->close();
                if ($cnt > 0) {
                    echo "Username already exists.";
                //if not, user-selected username and password are added to users table
                } else {
                    $stmt2 = $mysqli->prepare("insert into users (username, password) values (?,?)");
                    if (!$stmt2){
                        printf("Submission Failed: %s\n", $mysqli->error);
                        $_SESSION['error'] = $mysqli->error;
                        header('Location: error.php');
                        exit;
                    }
    
                    $stmt2->bind_param('ss', $username, $hash_pass);
                    $stmt2->execute();
                    $stmt2->close();
                    header("Location: login.php"); 
                    exit; 
                }
            } 
        }
    ?>
    </div>
</body>
</html>

