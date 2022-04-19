<!-- Author: Wenxin (Hugo) Xue &　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- Need to run this code before wustl_inst can perform any other query on news database -->
<!-- this script is required by other scripts -->
<?php
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'news');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	$_SESSION['error'] = $mysqli->connect_error;
    header('Location: error.php');
	exit;
}
?>