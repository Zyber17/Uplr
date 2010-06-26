<?php
	$api_key = $_POST['api_key'];
	
	require './settings.php';
	$con = mysql_connect($host, $user, $password) or die(mysql_error());
	$db = mysql_select_db($database, $con) or die(mysql_error());
	
	if(isset($api_key)) {
		$api_test = mysql_query("SELECT * FROM `users` WHERE `api_key` = '".$api_key."'");
		$api_num = mysql_num_rows($api_test);
		if($api_num === 1) {
			$more_data = mysql_fetch_assoc($api_test);
			$user = $more_data['id'];
			if($_POST['file'] != null || $_FILE['file'] != null) {
				$type = $_POST['type'];
				if($type == 'file') {
					$file = $_FILES['file']; //This is our file variable
					$name1 = $file['name'];
					$ran = rand(0, 99999);
					$random = $ran;
					$type = 0;
					
					
					$path_parts = pathinfo($name1);
					$ext = $path_parts['extension'];
					
							if (in_array($ext, $renamable_extensions))
							{
								$ext = 'txt';
								
							}
							else if (in_array($ext, $disallowed_extensions))
							{
								$result = "<return>$ext is not an acceptable file extention</return>";
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
								$return = '<result>The file you are trying to upload is too big.</return><return_code>14</return_code>';
							}
								else
								{
									$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$random."'");
									$found = mysql_num_rows($search);
									
									if ($found == 0)
									{ 
									
										if(!is_uploaded_file($tmp))
										{
											$return = '<result>Could not upload your file at this time, please try again </return><return_code>13</return_code>';
										}	
											else
											{
												if(!move_uploaded_file($tmp, $upload_dir . $name))
												{
													$result = '<return>Could not move the uploaded file.</return>';
												}
													else
													{
													
														 $query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_name`, `short_code`, `size`, `date`, `type`, `user`) VALUES (NULL,'$name1', '$name', '$random', '$size', '$date', '$type', '$user');";
														 
														 mysql_query($query);
														 
														$result = "<return>$pathnew</return><return_code>2</returncode>";
														
													}	
											}
									}
									else
									{
									$result = '<return>couldn\'t upload file</return><return_code>13</return_code>';
									}
								}
						
					
				}else if($type == 'text'){
					if(strlen($_POST['file']) != 0 && strlen($_POST['file']) <= 999999 && strlen($_POST['title']) <= 128) {
						$ran = rand(0, 99999);
						$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$ran."'");
						$found = mysql_num_rows($search);
						
						if ($found == 0)
						{
							$date = date('Y-m-d');
							$title = $_POST['title'];
							$text = $_POST['file'];
							$query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_code`, `text`, `date`, `type`, `user`) VALUES (NULL,'$title', '$ran', '$text', '$date', '3', '$user');";
							mysql_query($query);
							$pathnew = 'http://uplr.me/' . $ran;
							$result = "<return>$pathnew</return><return_code>2</returncode>";
						}else{
						$result = '<return>couldn\'t upload file</return><return_code>13</return_code>';
						}
					}
				}else if($type == 'url'){
					if(strlen($_POST['file']) != 0 && strlen($_POST['file']) > 9999 && strlen($_POST['title']) <= 128) {
						$ran = rand(0, 99999);
						$search = mysql_query("SELECT * FROM `files` WHERE `short_code` = '".$ran."'");
						$found = mysql_num_rows($search);
						
						if ($found == 0)
						{
							$date = date('Y-m-d');
							$title = $_POST['title'];
							$url = $_POST['file'];
							
							
							$query = "INSERT INTO `$database`.`files` (`id`, `name`, `short_code`, `short_url`, `date`, `type`, `user`) VALUES (NULL,'$title', '$ran', '$url', '$date', '4', '$user');";
							mysql_query($query);
							$pathnew = 'http://uplr.me/' . $ran;
							$result = "<return>$pathnew</return><return_code>2</returncode>";
							
						}else{
						$result = '<return>couldn\'t upload file</return><return_code>13</return_code>';
						}
						
					}
				}else{
				$result = "<return>Not a File, Text, or URL upload</return><return_code>11</return_code>";
				}
			}else{
				$result = "<return>No file sent</return><return_code>10</return_code>";
			}
		}else{
		$result = '<return>bad login</return><return_code>01</return_code>';
		}
	}else{
		$result = '<return>no login posted</return><return_code>00</return_code>';
	}
?>

<?php echo '<?xml version="1.0" encoding="ISO-8859-1"?>' . $result; ?>