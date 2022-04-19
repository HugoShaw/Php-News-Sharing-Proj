<!-- Script creates logout page for News Site -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Logout</title>
        <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
        <meta name="email" content="hugo@wustl.edu" />
        <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
        <meta name="email" content="hugo@wustl.edu" />
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            $_SESSION = array(); //unsets all of the session variables

            //code citation: https://www.php.net/manual/en/function.session-destroy.php
            //deletes the session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy(); //destroys session
        ?>
        <!-- Button to return user to login page -->
        <p>You have been logged out.</p>
        <form action="mainPage.php">
            <input type="submit" value="Return to Main Page"/>
        </form>
    </body>
</html>