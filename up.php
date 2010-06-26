<?php
	/**
	 * This function will handle all of our file uploading
	 */
	 
	 session_start();
	 
	 //connect to the database so we can check, edit, or insert data to our users table
	 require 'settings.php';
	 $con = mysql_connect($host, $user, $password) or die(mysql_error());
	 $db = mysql_select_db($database, $con) or die(mysql_error());
	 	
	 $user = $_SESSION['uid'];
	 	
	 function allow($user) {
	 		if(!$user){
	 			//display and error message
	 			header('Location: index.php');
	 		}
	 }
	allow($user);
	
		$user = $_SESSION['uid'];
		$user_code = mysql_query("SELECT * FROM `users` WHERE `id` = '".$user."'");
		$user_assoc = mysql_fetch_assoc($user_code);
		$username = $user_assoc['username'];
		
	
	function uploadFile($username){	
		require 'settings.php';
		$user = $_SESSION['uid'];
		
		$characters = 'acefhijklrstuvwxyz123456789';
		$ran = '';
		 for ($i = 0; $i < 5; $i++) {
		      $ran .= $characters[rand(0, strlen($characters) - 1)];
		 }
		
		if(isset($_POST['upload_button'])){
				$file = $_FILES['file_upload']; //This is our file variable
				$name1 = $file['name'];
				$random = $ran;
				$type = 0;
				
				
				$path_parts = pathinfo($name1);
				$ext = $path_parts['extension'];
				
						if (in_array($ext, $renamable_extensions))
						{
							$ext = 'txt';
							echo '<span class="error">Your php file was changed to a txt for safety reasons.</span>';
						}
						else if (in_array($ext, $disallowed_extensions))
						{
							echo 'No .' . $ext . ' allowed here';
							exit;
						}
						else if (in_array($ext, $photo_extensions)) {
							$type = 1;
							$ran = 'p'.$ran;
						}else if (in_array($ext, $video_extensions)) {
							$type = 2;
							$ran = 'p'.$ran;
						}else{
						$ran = 'f'.$ran;
						}
						
				
				$name2 = $ran.'.'.$ext;
				$name = $name2;
				$tmp = $file['tmp_name'];
				$size = $file['size'];
				$directories = $_SERVER['REQUEST_URI'];
				$path_parts = pathinfo($directories);
				$fileplace = $path_parts['basename'];
				$dpath = explode($fileplace, $directories);
				$date = date('Y-m-d');
				
				
				$max_size = $mb * 1024 * 1024; //megabytes
				//$pathnew = 'http://' . $_SERVER['HTTP_HOST'] . $dpath[0] . $random;
				//$path = 'http://' . $_SERVER['HTTP_HOST'] . $dpath[0] .$upload_dir . $name;
				$pathnew = 'http://uplr.me/' . $random;
				$path = 'http://uplr.me/files/' . $name;		
					
						if($size > $max_size)
						{
							echo '<span class="error">The file you are trying to upload is too big.';
						}
							else
							{
								$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$random."'");
								$found = mysql_num_rows($search);
								
								if ($found == 0)
								{ 
								
									if(!is_uploaded_file($tmp))
									{
										echo '<span class="error"> Could not upload your file at this time, please try again </span>';
									}	
										else
										{
											if(!move_uploaded_file($tmp, $upload_dir . $name))
											{
												echo '<span class="error"> Could not move the uploaded file. </span>';
											}
												else
												{
												
													 $query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_name`, `short_code`, `size`, `date`, `type`, `user`) VALUES (NULL,'$name1', '$name', '$random', '$size', '$date', '$type', '$user');";
													 
													 mysql_query($query);
													 
													echo "
													<div id='uped'><div class='wrap2 uped'><label>File:</label> Uploaded<br /><label>Location: </label><input type='text' value='$pathnew' id='url' onclick='this.focus();this.select();' readonly='readonly' /></div></div><script>var place = document.getElementById('url'); place.focus(); place.select();</script>";
													
												}	
										}
								}
								else
								{
								echo "<span class='error'>Unlucky dude, either our server is full, or the random name wasn't random enough.<br />Try to upload the file again, the random name issue will be addressed in a later version. Thanks!</span>";
								}
							}
					
			}else if(isset($_POST['text_upload'])) {
				$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$ran."'");
				$found = mysql_num_rows($search);
				
				if ($found == 0)
				{
					$date = date('Y-m-d');
					$title = $_POST['title'];
					$text = $_POST['text'];
					$query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_code`, `text`, `date`, `type`, `user`) VALUES (NULL,'$title', '$ran', '$text', '$date', '3', '$user');";
					mysql_query($query);
					$pathnew = 'http://uplr.me/' . $ran;
					echo "
					<div id='uped'><label>File:</label> Uploaded<br /><label>Location:</label><input type='text' value='$pathnew' id='url' onclick='this.focus();this.select();' readonly='readonly' /></div><script>var place = document.getElementById('url'); place.focus(); place.select();</script>";
				}else{
				echo "<span class='error'>Unlucky dude, either our server is full, or the random name wasn't random enough.<br />Try to upload the file again, the random name issue will be addressed in a later version. Thanks!</span>";
				}
			}else if(isset($_POST['url_upload']) || isset($_GET['url'])) {
				$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$ran."'");
				$found = mysql_num_rows($search);
				
				if ($found == 0)
				{
					$date = date('Y-m-d');
					$title = $_POST['title'];
					$url = $_POST['url'];
					if($url == null) {
						if($_GET['url'] != null) {
							$title = $_GET['title'];
							$url = $_GET['url'];
						}
					}
					if($url != null){
					$query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_code`, `short_url`, `date`, `type`, `user`) VALUES (NULL,'$title', '$ran', '$url', '$date', '4', '$user');";
					mysql_query($query);
					$pathnew = 'http://uplr.me/' . $ran;
					echo "
					<div id='uped'><label>File:</label> Uploaded<br /><label>Location:</label><input type='text' value='$pathnew' id='url' onclick='this.focus();this.select();' readonly='readonly' /></div><script>var place = document.getElementById('url'); place.focus(); place.select();</script>";
					}else{echo "<span class='error'>please enter a url</span>";}
				}else{
				echo "<span class='error'>Unlucky dude, either our server is full, or the random name wasn't random enough.<br />Try to upload the file again, the random name issue will be addressed in a later version. Thanks!</span>";
				}
			}else if(isset($_POST['suggest'])){
				mail('beta@zyber17.com', 'Uplr Suggestion', $_POST['suggestions'].'   '.$_SESSION['uid'], 'From: suggest@uplr.me');
			}else if(isset($_POST['vote'])) {
				mail('beta@zyber17.com', 'Uplr Suggestion', $_POST['poll'].'   '.$username, 'From: suggest@uplr.me');
			}
			
		}
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="index.css" />
<script>
	function file() {
		var files = document.getElementById("files");
		var text = document.getElementById("text");
		var link = document.getElementById("link");
		
		files.style.display = "inline";
		text.style.display = "none";
		link.style.display = "none";
	}
	function text() {
		var files = document.getElementById("files");
		var text = document.getElementById("text");
		var link = document.getElementById("link");
		
		files.style.display = "none";
		text.style.display = "inline";
		link.style.display = "none";
	}
	function url() {
		var files = document.getElementById("files");
		var text = document.getElementById("text");
		var link = document.getElementById("link");
		
		files.style.display = "none";
		text.style.display = "none";
		link.style.display = "inline";
	}
	function feedback() {
		var feedback = document.getElementById("feedback");
	//	var poll = document.getElementById("poll");
		var news = document.getElementById("news");
		
		feedback.style.display = "inline";
		//poll.style.display = "none";
		news.style.display = "none";
		
	}
	function poll() {
		var feedback = document.getElementById("feedback");
		var poll = document.getElementById("poll");
		var news = document.getElementById("news");
		
		feedback.style.display = "none";
		poll.style.display = "inline";
		news.style.display = "none";
	}
	function news() {
		var feedback = document.getElementById("feedback");
		//var poll = document.getElementById("poll");
		var news = document.getElementById("news");
		
		feedback.style.display = "none";
		//poll.style.display = "none";
		news.style.display = "inline";
	}
</script>
<title>Uplr</title>
</head>
<body>
<?php
	uploadFile($username);
?>
<div id="wrapper" class="margintop">
	<div class="wrap2">
<div id="top"><div class="wrap2 top"><a class="out" href="logout.php">logout</a></div></div>
<div class="tabs">
	<span class="tab" onclick="file();">Files</span><span class="tab" onclick="text();">Text</span><span class="tab" onclick="url();">URL</span>
</div>
<div id="types">
	<div id="files" class="types">
		<div class="middle">
			<h1 class="big">
			File
			</h1>
			<form action="up.php" method="post"  enctype="multipart/form-data">
				<p class="filebox">
				<input type="file" name="file_upload" id="file"/>
				<div class="wrap3"><input type="submit" name="upload_button" class="button" value="Upload" /></div>
				</p>
			</form>
		</div>
	</div>
	<div id="text" class="types extras">
		<div class="middle">
			<h1 class="big">
			Text
			</h1>
			<form action="up.php" method="post"  enctype="multipart/form-data">
				<p>
				<label>Title</label><br />
				<input type="text" class="box" name="title" value="" />
				</p>
				<p>
				<label>Text</label><br />
				<textarea name="text" class="bigbox"></textarea>
				</p>
				<p class="bottom">
				<div class="wrap3"><input type="submit" name="text_upload" class="button" value="Upload" /></div>
				</p>
			</form>
		</div>
	</div>
	<div id="link" class="types extras">
		<div class="middle">
			<h1 class="big">
			URL
			</h1>
			<form action="up.php" method="post"  enctype="multipart/form-data">
				<p>
					<label>Title</label><br />
					<input type="text" name="title" class="box" value="" />
				</p>
				<p>
					<label>URL</label><br />
					<input type="text" name="url" class="box" value="http://" />
				</p>
				<p class="bottom">
				<div class="wrap3"><input type="submit" name="url_upload" class="button" value="Shorten" /></div>
				</p>
			</form>
		</div>
	</div>
</div>
</div>
</div>
<div id="email">
<div class="wrap2 email">
	<div class="tabs">
		<span class="tab" onclick="news();">News</span><span class="tab" onclick="feedback();">Feedback</span><!--<span class="tab" onclick="poll();">Poll</span>-->
	</div>
	<div id="news" class="types">
		<div class="middle">
			<h1 class="title big">News</h1>
			<p class="smaller">Hey there <?php echo $username; ?>!<br />I just redesigned Uplr (as you can tell). <span onclick="feedback();" class="fakelink">Let me know</span> what you think! I'll be working on cleaning up the code a bit soon so it's even snapper.<br />Anyway, go back to uploading all those files!<br /><br />~The File Master</p>
		</div>
	</div>
	<div id="feedback" class="extras">
		<div class="middle">
		<h1 class="big">Feedback & Suggestions</h1>
		<form action="up.php" method="post"  enctype="multipart/form-data">
			<p>
				<textarea name="suggestions" class="box bigbox"></textarea>
			</p>
			<p class="sub">
				<div class="wrap3"><input type="submit" name="suggest" class="button" value="Submit" /></div>
			</p>
		</form>
	</div>
	</div>
	<!--<div id="poll" class="types extras">
	<div class="middle">
	<h1 class="big">Poll</h1>
	<form action="up.php" method="post"  enctype="multipart/form-data">
		<p class="smaller">
			Should I head towards a <a href="http://dznr.org/">dnzr</a>/<a href="http://idzr.org/">idrz</a>/<a href="http://kttns.org/">kttns</a> website (free & ad free, but elitist and exclusive) or <a href="http://droplr.com/" title="">droplr</a>/<a href="http://www.getcloudapp.com/" title="">cloud.app</a> website (free & ad supported w/ premium ad free and publicly available)?
		</p>
		<p>
			<input type="radio" name="poll" value="Cloud" /> Droplr/Cloud.app
			<br />
			<input type="radio" name="poll" value="Idrz" /> Dnzr/Idrz/Kttns
		</p>
		<p class="sub">
			<input type="submit" name="vote" class="button" value="Submit" />
		</p>
	</form>
	</div>
	</div>-->
	</div>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8906457-11");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>