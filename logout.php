<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();

//connect to the database so we can check, edit, or insert data to our users table
require './settings.php';
$con = mysql_connect($host, $user, $password) or die(mysql_error());
$db = mysql_select_db($database, $con) or die(mysql_error());

//include out functions file giving us access to the protect() function made earlier
include "./functions.php";

		
		//check if the login session does no exist
		if(!$_SESSION['uid']){
			//if it doesn't display an error message
			echo "<center>You need to be logged in to log out!</center>";
		}else{
			//if it does continue checking
			
			//update to set this users online field to the current time
			mysql_query("UPDATE `users` SET `online` = '".date('U')."' WHERE `id` = '".$_SESSION['uid']."'");
			
			//destroy all sessions canceling the login session
			session_destroy();
			
			//display success message
			header('Location: index.php');
		}
		
		?>