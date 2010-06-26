<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();

###################################
#                                 #
#     Sends MD5 of password       #
#                                 #
#     needs to send user to       #
#  a reset password page instead  #
#                                 #
###################################

//connect to the database so we can check, edit, or insert data to our users table
require './settings.php';
$con = mysql_connect($host, $user, $password) or die(mysql_error());
$db = mysql_select_db($database, $con) or die(mysql_error());

//include out functions file giving us access to the protect() function made earlier
include "./functions.php";

?>
<html>
	<head>
		<title>Forogt Password</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<?php
		$error = 'Forgot Something?';
		//Check to see if the forms submitted
		if($_POST['submit']){
			//if it is continue checks
			
			//store the posted email to variable after protection
			$email = protect($_POST['email']);
			
			//check if the email box was not filled in
			if(!$email){
				//if it wasn't display error message
				$error = "<span class='error'>Information</span> missing.";
			}else{
				//else continue checking
				
				//set the format to check the email against
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				
				//check if the email doesnt match the required format
	            if(!preg_match($checkemail, $email)){
	            	//if not then display error message
	                $error = "Invalid <span class='error'>email</span>.";
	            }else{
	            	//otherwise continue checking
	            	
	            	//select all rows from the database where the emails match
	            	$res = mysql_query("SELECT * FROM `users` WHERE `email` = '".$email."'");
	            	$num = mysql_num_rows($res);
	            	
	            	//check if the number of row matched is equal to 0
	            	if($num == 0){
	            		//if it is display error message
						$error = "<span class='error'>Nonexistant</span> email.";
					}else{
						//otherwise complete forgot pass function
						
						//split the row into an associative array
						$row = mysql_fetch_assoc($res);
						
						//send email containing their password to their email address
						mail($email, 'Forgotten Password', "Here is your password: ".$row['password']."\n\nPlease try not too lose it again!", 'From: forgot@uplr.me');
						
						//display success message
						$error= "<span class='error'>Email</span> sent.";
					}
				}
			}
		}
		
		?>
		<div id="wrapper" class="forgotbox margintop">
			<div class="wrap2 forgotbox">
			 	<div id="in">
					<form action="forgot.php" method="post">
						<h1 class="big"><?php echo $error; ?></h1>
						<p>
							<span class="left">Email</span><br />
							<input type="text" class="box" name="email" />
						</p>
						<p>
							<div class="wrap3"><input type="submit" name="submit" class="button" value="Send" /></div><br />
						</p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>