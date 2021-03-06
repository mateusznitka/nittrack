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

		<div class="content_r">
		
			<div class ="leftbar">
			
				<div id="menuleft">			
					
					<a href = "info.php" style="text-decoration: none;"><div class="optionL"><i class="icon-info-circled"></i> Informacje o systemie</div></a>
					<a href = "contact.php" style="	text-decoration: none;"><div class="optionL"><i class="icon-mail-alt"></i> Kontakt</div></a>
					<a href = "help.php" style="	text-decoration: none;"><div class="optionL"><i class="icon-help-circled"></i> FAQ</div></a>
					
				</div>
				
			</div>
			
			<div class = "info_content">
				
				<div id="test3">
					
					<details>
						<summary>Co to za strona?</summary>
						<p>To aplikacja systemu lokalizacji satelitarnej pojazdów. Więcej przeczytasz w zakładce <a href="info.php">Informacje</a>. </p>
					</details>
					<details>
						<summary>Gdzie to można kupić?</summary>
						<p>Póki co nigdzie. Jest to system stworzony na potrzeby pracy dyplomowej oraz do użytku prywatnego. Nie wykluczam jednak udostępnienia systemu w jakiejś formie w przyszłości. </p>
					</details>						
					<details>
						<summary>Jak się tym obsługiwać?</summary>
						<p>W zakładce <b>Mapa</b> możesz wyświetlać na interaktywnej mapie zarejestrowane pozycje danego pojazdu w wybranym dniu. W tym celu z paska po lewej wybierz pojazd, datę i przyciśnij "Zastosuj". Jeśli pojazd był aktywny w danym dniu, na mapie pojawią się kropki wyświetlające przejechaną trasę. Pojawi się również informacja o statusie urządzenia - jego dystansie dziennym oraz czasie od ostatniej aktywności. Dodatkowo możesz wyświetlić pozycje wszystkich dostępnych pojazdów klikając "wyświetl wszystkie urządzenia". Każdą wyświetloną na mapie pozycję możesz kliknąć aby uzyskać więcej szczegółów. </p>
						<p>W zakładce <b>Raporty</b> możesz generować różne raporty dotyczące danego okresu czasu. Narazie to kilka przykładowych raportów, będzie ich więcej. </p>
						<p>Zakładka <b>Urządzenie</b> to ustawienia jednostki lokalizującej, ale obecnie nie jest dostępna do użytku.</p>
					</details>						
					<details>
						<summary>Czy system jest rozwijany?</summary>
						<p>Mam wiele planów dotyczących systemu. Zarówno dotyczących funkcjonalności aplikacji jak i systemu oraz wielu innych aspektów. Na potrzeby pracy inżynierskiej był rozwijany dosyć intensywnie, teraz to zajęcie po godzinach, więc tempo rozwoju jest raczej powolne.</p>
					</details>						
					<details>
						<summary>Czy jest mobilna wersja aplikacji?</summary>
						<p>Na tę chwile nie. Aplikacja wprawdzie jest responsywna na rozdzielczościach spotykanych w komputerach, ale nie jest przystosowana do urządzeń mobilnych.</p>
					</details>	
					<details>
						<summary>Jakie technologie zostały wykorzystane w projekcie?</summary>
						<p>Za backend aplikacji odpowiada PHP7, za frontend HTML5+CSS3. Skrypty obsługujące mapę, wyświetlanie punktów oraz niektóre elementy strony to JavaScript z bibliotekami Leaflet i jQuery. Baza danych została oparta na silniku MariaDB. Całość znajduje się na serwerze z Debianem 9. Program jednostki lokalizacyjnej stworzony został w środowisku Arduino IDE, a więc języku zbliżonym do C/C++.</a></p>
					</details>						
					<details>
						<summary>Coś ewidentnie nie działa / tak się tego nie robi.</summary>
						<p>Jeśli zauważyłeś buga lub uważasz, że coś można zrobić lepiej / prościej / bardziej profesjonalnie - napisz do mnie - <a href="contact.php">kontakt</a></p>
					</details>						
					

				</div>
				
			</div>
		
		</div>
		
</body>
</html>

<?php
	$polaczenie->close();
?>