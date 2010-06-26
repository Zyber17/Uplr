<?php
echo '
<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
<form action="index.php" method="post">
			<div id="wrapper" class="loginbox">
				<div id="header">
					<h1>Uplr.me</h1>
				</div>
				<div id="in">
						<p>
						<span class="left">Username</span><br />
						<input type="text" name="username" class="box"/>
						</p>
						<p>
						<span class="left">Password</span><br />
						<input type="password" name="password" class="box" />
						</p>
						<p>
						<input type="submit" name="submit" value="Login" class="button"/><br/>		
						<span class="right"><a href="register.php">Register</a> | <a href="forgot.php">Forgot Pass</a></span>
						</p>
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
?>