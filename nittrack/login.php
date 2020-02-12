<?php
	session_start();
	
	if((isset($_SESSION['logged'])) && ($_SESSION['logged']==true))
	{
		header('Location: index.php');
		exit();
	}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatibile" content="IE=edge,chrome=1" />
	<title>System monitoringu GPS</title>
	<link rel="stylesheet" href="login_style.css" type="text/css" />
	<link rel="stylesheet" href="css/fontello.css" type="text/css" />
	<link href="https://fonts.googleapis.com/css?family=Barlow" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css"
		integrity="sha512-M2wvCLH6DSRazYeZRIm1JnYyh22purTM+FDB5CsyxtQJYeKq83arPe5wgbNmcFXGqiSH2XR8dT/fJISVA1r/zQ=="
		crossorigin=""/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"
							integrity="sha512-lInM/apFSqyy1o6s89K4iQUKg6ppXEgsVxT35HbzUupEVRh2Eu9Wdl4tHj7dZO0s1uvplcYGmt3498TtHq+log=="
							crossorigin=""></script>	
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
 
	
</head>
	<div id="parent">
		<div id="header">
			<div id="logo">
				<img src="godlo_login.jpg">
			</div>
			<div id="napis">
				SYSTEM MONITORINGU POZYCJI OBIEKTÓW MOBILNYCH
			</div>
			<form id="inputs" method="post" action="get_login.php">
				<input type = "text" placeholder="login" name="login">
				<br/><br/>
				<input type = "password" placeholder="hasło" name="password">
				<br/>
				<?php
					if(isset($_SESSION['error']))
					echo $_SESSION['error'];
					unset($_SESSION['error']);
				?>
				<br/>
				<input type = "submit" value="Zaloguj się">
			</form>
			

			</div>
		</div>
	</div>

</body>
</html>