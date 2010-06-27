<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();

//connect to the database so we can check, edit, or insert data to our users table
require './settings.php';
$con = mysql_connect($host, $user, $password) or die(mysql_error());
$db = mysql_select_db($database, $con) or die(mysql_error());

//include out functions file giving us access to the protect() function made earlier
include "./functions.php";

	
			$one = $_GET['file'];
			if($one) {
				$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$one."'");
				$found = mysql_num_rows($search);
				
				if($found == 1)
				{
					$assoc = mysql_fetch_assoc($search);
					$shortname = $assoc['short_name'];
					
					function update() {
						$update = "UPDATE `files` SET `view` = `view`+1 WHERE `short_code` = '".$_GET['file']."'";
						mysql_query($update);
					}
					
					$type = $assoc['type'];
					if($type == 0){
						update();
						header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
						header("Location: files/$shortname");
					}else if($type == 1){
						update();
						echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						
						<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
							<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
								<title>Photo</title>
								
								<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
								<link rel="stylesheet" type="text/css" href="pic.css" />
								
							</head>
							<body>
								<div id="wrapper">
									<center>
									<script type="text/javascript">
										function width(){
											var width = window.innerWidth;
											var imgw = document.getElementById("img");
											if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i)){
												imgw.style.width = "300px";
											}else{
												if(width > 0) {
													if (imgw.width >= width) {
														var width2 = width - 40;
														imgw.style.width = width2+"px";
													}
												}
											}
										}
									setTimeout(width, 500);
									</script>
									<img id="img" src="files/'.$shortname.'"/><br /><br />
									<a href="files/'.$shortname.'"><button id="org">View Original</button></a>
									<br />
									<a><button id="dark">Dark</button><button id="light">Light</button></a>
									</center>
									
									</div>
							</body>
						</html>';
					}else if($type == 2){
						update();
						echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						
						<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
							<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
								<title>Video</title>
								
								
								
								
							</head>
							<body>
								<div id="wrapper">
									<center>
									<embed width="100%" height="100%" name="plugin" src="files/'.$shortname.'" type="video/quicktime">
									</center>
									
									</div>
							</body>
						</html>';
					}else if($type == 3){
						update();
						$name = $assoc['name'];
						$text_pre = $assoc['text'];
//						echo $text_pre . '<br />';
//						$text_pree = preg_replace(apple, '()', $text_pre);
//						echo $text_pree;
						include_once "markdown.php";
						$text = Markdown($text_pre);
						echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						
						<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
							<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
								<title>Text</title>
								
								<link rel="stylesheet" type="text/css" href="text.css" />
								
								
							</head>
							<body>
								<div id="wrapper">
									<h1>'.$name.'</h1>
									'.$text.'
									
									</div>
							</body>
						</html>';
					}else if($type == 4){
						update();
						$url = $assoc['short_url'];
						header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
						header("Location: $url");
					}else{
					echo 'error';
					}
				}
			}
			
				$error = "Identification, please.";
				//If the user has submitted the form
				if($_POST['submit']){
					//protect the posted value then store them to variables
					$username = protect($_POST['username']);
					$password = protect($_POST['password']);
					
					//Check if the username or password boxes were not filled in
					if(!$username || !$password){
						//if not display an error message
						$error = "<span class='error'>Information</span> missing." ;
					}else{
						//if the were continue checking
						
						//select all rows from the table where the username matches the one entered by the user
						$res = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."'");
						$num = mysql_num_rows($res);
						
						//check if there was not a match
						if($num == 0){
							//if not display an error message
							$error = "Incorrect <span class='error'>Username</span>.";
						}else{
							//if there was a match continue checking
							
							$password2 = md5($password);
							
							//select all rows where the username and password match the ones submitted by the user
							$res = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."' AND `password` = '".$password2."'");
							$num = mysql_num_rows($res);
							
							//check if there was not a match
							if($num == 0){
								//if not display error message
								$error = "Incorrect <span class='error'>Password</span>.";
							}else{
								//if there was continue checking
								
								//split all fields fom the correct row into an associative array
								$row = mysql_fetch_assoc($res);
								
								//check to see if the user has not activated their account yet
								if($row['active'] != 1){
									//if not display error message
									$error = "<span class='error'>Activation</span> required.";
								}else{
									//if they have log them in
									
									//set the login session storing there id - we use this to see if they are logged in or not
									$_SESSION['uid'] = $row['id'];							//show message
									//echo "<span class='error loginerror'>You're in! Have fun!</span>";
									
									//update the online field to 50 seconds into the future
									$time = date('U')+50;
									mysql_query("UPDATE `users` SET `online` = '".$time."' WHERE `id` = '".$_SESSION['uid']."'");
									
									//redirect them to the usersonline page
									header("Location: up.php");
								}
							}
						}
					}
				}else if(isset($_SESSION['uid'])) {
					//header("Location: up.php");
				}else if(!$one) {
					echo '
					<html>
						<head>
							<title>Login</title>
							<link rel="stylesheet" type="text/css" href="style.css" />
						</head>
						<body>
					<form action="index.php" method="post">
								<div id="wrapper" class="loginbox margintop">
									<div class="wrap2 loginbox">
										<div id="in">
												<h1 class="big">'.$error.'</h1>
												<p>
												<span class="left">Username</span><br />
												<input type="text" name="username" class="box"/>
												</p>
												<p>
												<span class="left">Password</span><br />
												<input type="password" name="password" class="box" />
												</p>
												<p>
												<div class="wrap3 left"><input type="submit" name="submit" value="Enter" class="button"/></div><br/>
												</p>
										</div>
									</div>
								</div>
							</form>
							<script type="text/javascript">
							var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
							document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
							</script>
							<script type="text/javascript">
							try {
							var pageTracker = _gat._getTracker("UA-8906457-11");
							pageTracker._trackPageview();
							} catch(err) {}</script>
						</body>
					</html>';
					}
				?>