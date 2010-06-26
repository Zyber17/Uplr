<?php
session_start();

function allow() {
 		if(!$_SESSION['uid']){
 			//display and error message
 			header('Location: login.php');
 		}
 }
allow();

function getFiles(){
	require './settings.php';
	$con = mysql_connect($host, $user, $password) or die(mysql_error());
	$db = mysql_select_db($database, $con) or die(mysql_error());
	$user = $_SESSION['uid'];
	//echo $user;
	$blah = mysql_query("SELECT * FROM  `files` WHERE  `user` = 1");
	//mysql_fetch_assoc()
	while($file = $blah){
//		$string = mysql_fetch_assoc($file);
//		echo $string['user'];
	echo 'string';
	}

	
	
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="reset.css" />
<link rel="stylesheet" type="text/css" href="index.css" />
<title>Uploady Manager v0.1</title>
</head>
<body>
<div id="mwrapper">
<div id="top">
</div>
<div id="manage">
<?php
getFiles();
?>
</div>
</div>
</body>
</html>