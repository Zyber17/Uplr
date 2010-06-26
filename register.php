<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();

//connect to the database so we can check, edit, or insert data to our users table
require './settings.php';
$con = mysql_connect($host, $user, $password) or die(mysql_error());
$db = mysql_select_db($database, $con) or die(mysql_error());

//include out functions file giving us access to the protect() function
include "./functions.php";

?>
<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<?php
		$error = "Welcome,<br />friend!";
		//Check to see if the form has been submitted
		if(isset($_POST['submit'])){
			
			//protect and then add the posted data to variables
			$username = protect($_POST['username']);
			$password = protect($_POST['password']);
			$passconf = protect($_POST['passconf']);
			$email = protect($_POST['email']);
			$acode = protect($_POST['code']);
			$checkbox = $_POST['terms'];
			
			//check to see if any of the boxes were not filled in
			if(!$username || !$password || !$passconf || !$email || !$acode){
				//if any weren't display the error message
				$error = "<span class='error'>Information</span> missing.";
			}else{
				//if all were filled in continue checking
				
				//Check if the wanted username is more than 32 or less than 3 charcters long
				if(strlen($username) > 32 || strlen($username) < 3){
					//if it is display error message
					$error = "Too long/short <span class='error'>username</span>.";
				}else{
					//if not continue checking
					
					//select all the rows from out users table where the posted username matches the username stored
					$res = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."'");
					$num = mysql_num_rows($res);
					
					//check if theres a match
					if($num == 1){
						//if yes the username is taken so display error message
						$error =  "<span class='error'>Username</span> taken.";
					}else{
						//otherwise continue checking
						
						//check if the password is less than 5 or more than 32 characters long
						if(strlen($password) < 5 || strlen($password) > 32){
							//if it is display error message
							$error = "Too long/short <span class='error'>password</span>.";
						}else{
							//else continue checking
							
							//check if the password and confirm password match
							if($password != $passconf){
								//if not display error message
								$error = "Dissimilar <span class='error registererror'>passwords</span>.";
							}else{
								//otherwise continue checking
								
								//Set the format we want to check out email address against
								$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
								
								//check if the formats match
					            if(!preg_match($checkemail, $email)){
					            	//if not display error message
					                $error = "Invalid <span class='error'>email</span>.";
					            }else{
					            	//if they do, continue checking
					            	
					            	//select all rows from our users table where the emails match
					            	$res1 = mysql_query("SELECT * FROM `users` WHERE `email` = '".$email."'");
					            	$num1 = mysql_num_rows($res1);
					            	
					            	//if the number of matchs is 1
					            	if($num1 == 1){
					            		//the email address supplied is taken so display error message
										$error = "Used <span class='error'>email</span>.";
									}else{
										//keep on chechking
										
										//check the signup code
										$codecheck = mysql_query("SELECT * FROM `codes` WHERE `code` = '".$acode."'");
										$code1 = mysql_num_rows($codecheck);
										
										//if the code exists
										if($code1 != 1) {
											//If the code wasn't found
											$error = 'Bad <span class="error">activation code</span>.';
										}else{
											$code2 = mysql_query("SELECT * FROM `codes` WHERE `code` = '".$acode."' AND `used` = '0'");
											$codecode = mysql_num_rows($code2);
											if($codecode != 1){
												$error = 'Used <span class="error">activation code</span>.';
											}else{
												if(!$checkbox) {
													$error = 'Please agree to the <span class="error">Terms</span>.';
												}else{
												
													//finally, otherwise register there account
													
													//time of register (unix)
													$registerTime = date('U');
													
													//make a code for our activation key
													$code = md5($username).$registerTime;
													
													$password2 = md5($password);
													
													//insert the row into the database
													$res2 = mysql_query("INSERT INTO `users` (`username`, `password`, `email`, `rtime`) VALUES('".$username."','".$password2."','".$email."','".$registerTime."')");
													$decode = mysql_query("UPDATE `codes` SET `used` = '1' WHERE `code` = '".$acode."'");
													
													//send the email with an email containing the activation link to the supplied email address
													mail($email, $INFO['chatName'].' registration confirmation', "Thank you for registering to us ".$username.",\n\nHere is your activation link. If the link doesn't work copy and paste it into your browser address bar.\n\nhttp://uplr.me/activate.php?code=".$code, 'From: welcome@uplr.me');
													
													//display the success message
													$error = "<span class='error'>Success!</span> Check your inbox.";
												}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		
		
		?><div id="wrapper" class="registerbox margintop">
			<div class="wrap2 registerbox">
				<form action="register.php" method="post">
					<div id="in">
					<h1 class="big"><?php echo $error; ?></h1>
					<p>
						<span class="left">Username</span><br />
						<input type="text" class="box" name="username" />
					</p>
					<p>
						<span class="left">Password</span><br />
						<input type="password" class="box" name="password" />
					</p>
					<p>
						<span class="left">Confirm Password</span><br />
						<input type="password" class="box" name="passconf" />
					</p>
					<p>
						<span class="left">Email</span><br />
						<input type="text" name="email" class="box" size="25"/>
					</p>
					<p>
						<span class="left">Invite code</span><br />
						<input type="text" class="box" name="code"/>
					</p>
					<p>
						<span class="left"><a href="terms.html" title="Terms of Service">Terms of Service</a></span><br />
						<span class="left"><input type="checkbox" name="terms" value="true" /> I agree to the Terms of Service</span>
					</p>
					<p>
						<div class="wrap3"><input type="submit" name="submit" class="button" value="Register" /></div><br />
					</p>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>