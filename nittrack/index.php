<?php
	
	session_start();
	
	if (!isset($_SESSION['logged']))
	{
		header('Location: login.php');
		exit();
	}
	
	include "connect.php";
	$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
	
	if(isset($_POST['now_pass']))
	{
		if (!empty($_POST['now_pass']))
		{			
			$now_pass = $_POST['now_pass'];
			$new_pass = $_POST['new_pass'];
			$confirm_pass = $_POST['confirm_pass'];
			$email = $_POST['email'];
			$phone = $_POST['phone'];

			$sqlx = "SELECT * FROM nt_users WHERE id = '{$_SESSION["id"]}'";
			
			$queryx = @$polaczenie -> query($sqlx);
			
			$rowx = $queryx -> fetch_assoc();			
			
			if(password_verify($now_pass, $rowx['password']))
			{
				if (!empty($_POST['phone']))
				{
					$polaczenie->query("UPDATE nt_users SET phone =  '{$phone}' where id = '{$_SESSION["id"]}'");
					$_SESSION['e_haslo']='<div id="changestext">Zmiany będą widoczne po przelogowaniu.</div>';
				}				
				
				if (!empty($_POST['email']))
				{
					$polaczenie->query("UPDATE nt_users SET email =  '{$email}' where id = '{$_SESSION["id"]}'");
					$_SESSION['e_haslo']='<div id="changestext">Zmiany będą widoczne po przelogowaniu.</div>';
				}

				if (!empty($_POST['new_pass']))
				{
					if($new_pass != $confirm_pass)
					{
						$_SESSION['e_haslo']='<span style = "color:red; font-size: 12px">Podane hasła nie sa zgodne.</span>';
					}
					else
					{
						$new_hash_password = password_hash($new_pass, PASSWORD_DEFAULT);
						$polaczenie->query("UPDATE nt_users SET password = '{$new_hash_password}' where id = '{$_SESSION["id"]}'");
						
						$_SESSION['e_haslo']='<div id="changestext">Zmiany będą widoczne po przelogowaniu.</div>';
					}
				}
				
			}
			
			else
			{
				$_SESSION['e_haslo']='<span style = "color:red; font-size: 12px">Nieprawidłowe hasło.</span>';
			}
			
		}
		if(empty($_POST['now_pass']))
		{
			$_SESSION['e_haslo']='<span style = "color:red; font-size: 12px">Wpisz obecne hasło, aby potwierdzić.</span>';
		}
	}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatibile" content="IE=edge,chrome=1" />
	<title>System monitoringu GPS</title>
	<link rel="stylesheet" href="style.css" type="text/css" />
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
	
<body>
		<div id="popup1" class="settingwindow">
			<div class = "popup">
				<p style ="font-size: 20px; font-weight: 700;"> Ustawienia użytkownika</p><a class="close" href="#">&times;</a>
				<div id = "settingx">
					<form id="settingf" method="post">
						<b>Dane kontaktowe</b></br>
						Adres e-mail:</br> 
							<input type = "text" name = "email" id="email" placeholder="<?php echo $_SESSION['email'] ?>" /></br>
						Telefon: </br>
							<input type = "text" name = "phone" id="phone" placeholder="<?php echo $_SESSION['phone'] ?>"/></br>
						</br><b>Zmiana hasła</b></br>
						Nowe hasło: </br>
							<input type = "password" name = "new_pass" id="new_pass" /></br>
						Potwierdź hasło: </br>
							<input type = "password" name = "confirm_pass" id="confirm_pass" />
							</br></br>
						<b>Aby potwierdzić zmiany, wpisz obecne hasło:</b><br/>
						<input type = "password" name = "now_pass" id="now_pass" /></br>
							<?php
								if(isset($_SESSION['e_haslo']))
								echo $_SESSION['e_haslo'];
								unset($_SESSION['e_haslo']);
							?>
							<br/>

						<input type = "submit" value="Zastosuj zmiany" class="submitsettings"/>
					</form>
				</div>
			</div>
		</div>
	
	<div class="wrapper">

		
		<div class="header">
		
			<div class="logoz">
				<img src=godlo.png>
			</div>
			
			<div id="menu">
				<a href = "index.php"><div class="option"><i class="icon-location"></i>Mapa</div></a>
				<a href = "reports.php"><div class="option"><i class="icon-chart-line"></i> Raporty</div></a>
				<a href = "device.php"><div class="option"><i class="icon-wrench"></i> Urządzenie</div></a>
				<a href = "info.php"><div class="option"><i class="icon-info-circled"></i> Informacje</div></a>
				
			</div>
			
				<div class="logout">
						
						<div class="logout_left">
							<div class="user_ico">
								<img src=user_ico.png>
							</div>
							
							<div class="username">
								<?php
									echo $_SESSION['user'];
								?>
							</div>	

						</div>

						
						<div class="logout_right">

							<a href = "#popup1">
							<button class="usr_btnx">
									<i class="icon-cog"></i>
							</button>					
							</a>
							
							<a href = "logout.php">
							<button class="usr_btnx">
								<i class="icon-export"></i>
							</button>
							</a>							
						</div>
					
				</div>
				
		</div>
				

		
		<div class="content">
		
			<div class="leftbar">
				
				<div id="lefttext">			
					Wyświetlanie pozycji:
				</div>

				
				<div class="selector">
				
				<form id="test" method = "post" name = "test">
					Wybierz urządzenie:
					<select name = "dev_id" id = "dev_id">
					
						<?php 
						$device = mysqli_query($polaczenie, "SELECT devices.id, devices.type, devices.name, devices_assign.user_id FROM devices, devices_assign where devices.id = devices_assign.device_id and devices_assign.user_id = '{$_SESSION["id"]}'");
						while ($rowx = $device->fetch_assoc()){
						echo "<option value=\"".$rowx['id']."\">" . $rowx['name'] . "</option>";
						}
						?>
					</select>
					
					<br/><br/>
					Wybierz datę: </label><input id="pos_date" type="date" value="<?php date_default_timezone_set('Europe/Warsaw'); $date = date('Y-m-d'); echo $date; ?>"/>
					<input type="checkbox" id="limiter" name="limiter" value="limit 1"/>Wyświetl tylko ostatnią pozycję <br/><br/>
					<input type = "button" value="Zastosuj" id="submitdata" onclick="SubmitFormData(); markers.clearLayers();ShowStatus();"/>
					</form>

					
					<script>
						function ShowStatus() {
						//var type = $("#type").val();
						var dev_id = $("#dev_id").val();
						
						
						//$.post("submit.php", {type: type, dev_id: dev_id},
						$.post("submit.php", {dev_id: dev_id},
						function(data) {
						 $('#wyniki').html(data);
						 
						 //$('#test')[0].reset();
						});
						//$('#wyniki_table').css("display", "block");
						}
					

					</script>
					
				</div>
				
				<div class="selector">
					<form id="test2" method = "post" name = "test2">
					Ostatnie znane pozycje:
					<input type = "button" value="Wyświetl wszystkie urządzenia" id="showall" onclick="ShowAll(); markers.clearLayers();"/>
					</form>
				</div>
				
				<div id="wyniki">
				Status urządzenia
				</div>
				
				</div>
			
			<div class="map">
			

				<div id="mapid">	
					
					<script>

						
						var markers = L.layerGroup();;
						//dodanie punktow z pliku geojso
						
						var sourcemap = L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'});
						
						var mymap = L.map('mapid', 
						{
							center: [52.225, 21.004],
							zoom: 12,
							layers: [markers, sourcemap]
						}
						);
						
						function onEachFeature(feature, layer) {
							var popupContent = "<p><b>Typ: </b>" +
									feature.geometry.properties.name + "<br/><b> Koordynaty: </b>" + feature.geometry.coordinates +  "<br/><b>Prędkość: </b>" + feature.geometry.properties.speed + "<br/><b>Ilość satelit: </b>" + feature.geometry.properties.satellites + "</p>";

							if (feature.properties && feature.properties.popupContent) {
								popupContent += feature.properties.popupContent;
							}

							layer.bindPopup(popupContent);
						}
						
						
						function onEachFeature2(feature, layer) {
							var popupContent = "<p><b>Urządzenie: </b>" +
									feature.geometry.properties.name + "<br/><b> Koordynaty: </b>" + feature.geometry.coordinates +  "<br/><b>Prędkość: </b>" + feature.geometry.properties.speed + "<br/><b>Ilość satelit: </b>" + feature.geometry.properties.satellites + "<br/><b>Status: </b>" + feature.geometry.properties.status + "<br/><b>Typ urządzenia: </b>" + feature.geometry.properties.dev_type + "</p>";

							if (feature.properties && feature.properties.popupContent) {
								popupContent += feature.properties.popupContent;
							}

							layer.bindPopup(popupContent);
						}
						
						
						function SubmitFormData() 
						{
						
							var dev_id = $("#dev_id").val();
							var pos_date = $("#pos_date").val();
							var limiter = $("input[type=checkbox]:checked").val();
							//var wyniki_table = $('#wyniki_table').css("display", "block");
							
							
							
							$.post("convert.php", {dev_id: dev_id, pos_date: pos_date, limiter: limiter}, function(data)
							{
								//$('#wyniki').html(data);
								$('#test')[0].reset();
								
								
								// add GeoJSON layer to the map once the file is loaded
								markers = L.geoJson
								(data, 
								{
									style: function (feature) 
									{
										return feature.properties && feature.properties.style;
									}, 
									
									onEachFeature: onEachFeature,
									
									pointToLayer: function (feature,latlng) 
									{
										return L.circleMarker
										(latlng, 
											{
												radius: 7,
												fillColor: "#ff7800",
												color: "#330000",
												weight: 1,
												opacity: 1,
												fillOpacity: 0.8
											}
										);
									}
								}).addTo(mymap);
							}, 'json');
							
						}
						
						
						function ShowAll() 
						{
						
							$.post("convert2.php", function(data)
							{								
								
								// add GeoJSON layer to the map once the file is loaded
								markers = L.geoJson
								(data, 
								{
									style: function (feature) 
									{
										return feature.properties && feature.properties.style;
									}, 
									
									onEachFeature: onEachFeature2,
									
									pointToLayer: function (feature,latlng) 
									{
										return L.circleMarker
										(latlng, 
											{
												radius: 10,
												fillColor: "#73c105",
												color: "#330000",
												weight: 1,
												opacity: 1,
												fillOpacity: 0.9
											}
										);
									}
								}).addTo(mymap);
							}, 'json');
							
						}
						
						
					</script>
					
				</div>
				
			</div>

			
		</div>

	
	</div>

				
</body>
</html>

<?php
	$polaczenie->close();
?>