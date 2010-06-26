<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();

//connect to the database so we can check, edit, or insert data to our users table
require './settings.php';
$con = mysql_connect($host, $user, $password) or die(mysql_error());
$db = mysql_select_db($database, $con) or die(mysql_error());

//include out functions file giving us access to the protect() function made earlier
include "./functions.php";

?>
<html>
	<head>
		<title>Activate</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<?php
		
		//echo md5('other');
		//get the code that is being checked and protect it before assigning it to a variable
		$code = protect($_GET['code']);
		
		//check if there was no code found
		if(!$code){
			//if not display error message
			echo "<span class='error'>Unfortunatly there was an error there!</span>";
		}else{
			//other wise continue the check
			
			//select all the rows where the accounts are not active
			$res = mysql_query("SELECT * FROM `users` WHERE `active` = '0'");
			
			//loop through this script for each row found not active
			while($row = mysql_fetch_assoc($res)){
				//check if the code from the row in the database matches the one from the user
				if($code == md5($row['username']).$row['rtime']){
					//if it does then activate there account and display success message
					$res1 = mysql_query("UPDATE `users` SET `active` = '1' WHERE `id` = '".$row['id']."'");
					echo "<div id='wrapper' class='welcomebox'>
								<div id='win'>
									<h2>Hey There!</h2>
									<p class='welcome'>
										Hey there! We're glad to have you use our service. Though, before you go on your merry way you should know a few things. Right now we have a cap on 5mb per file, but no limit (well 100,000 is our random name generator's limit) on how many files you can upload. We've got a project status page in the works and we've got a few more things to come till we move from pre-beta to beta. The main thing that's coming is a file manager and file view count. Don't worry we're already tracking views, but the page to display them is still in the works. Anyway, <a href='login.php'>go have fun</a> Oh, and have yourself a <a title='Uplr.me' href='javascript:window.location%3D%22http//uplr.me/up.php?bookmarklet=true&title=%22+document.title+%22&url=%22+window.location' class='book'>bookmarklet</a>.
									</p>
								</div>
							</div>";
				}
			}
		}
		
		?>