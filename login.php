<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- Script creates login page for News Site -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <ul>
        <!-- click to head to home page (main page) -->
        <li><a href="mainPage.php">Home</a></li>
        <!-- click to head to create a new account (add new user) -->
        <li><a href="addUser.php">Create New Account</a></li>
    </ul> 
    <!-- have an account already? just login  -->
    <div class="stories_list">
        <H1>Login to Simple News Web Site</H1>
        <!-- self-submitting POST form that accepts username input  -->
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            <p>
                <label for="username">Username: </label>
                <input type="text" name="username" id="username" required/>
            </p>
            <p>
                <label for="pswd">Password: </label>
                <input type="password" name="pswd" id="pswd" required/>
            </p>
            <p>
                <input name="login" type="submit" value="Log In"/>
            </p>
        </form>
        <?php
            if (isset($_POST["login"])) {
                if (isset($_POST["username"]) 
                    && !empty($_POST["username"])
                    && isset($_POST["pswd"])
                    && !empty($_POST["pswd"])) {
                    
                    // initialize username and password
                    $username = $_POST["username"];
                    $password_guess = $_POST["pswd"];
                    
                    require 'database.php';
                    $stmt = $mysqli->prepare("select COUNT(*), id, password from users where username=?");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    //bind params 
                    $stmt->bind_param('s',$username);
                    $stmt->execute();

                    //bind result
                    $stmt->bind_result($cnt, $user_id, $pwd_hash);
                    $stmt->fetch();
                    
                    // compare the submitted password to the actual hash password
                    if ($cnt == 1 && password_verify($password_guess,$pwd_hash)){
                        // login
                        $_SESSION["user_id"] = $user_id; //sessions usr & pass
                        // Cross Site Request Forgery Solution 1. post 2. token
                        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
                        // redirect to main page
                        header("Location: mainPage.php");
                        exit;
                    } else{
                        echo "Username or password not recognized.";
                    }   

                } else {
                    echo "Please enter username and password to login.";
                }
            }
        ?>
    </div>
</body>
</html>