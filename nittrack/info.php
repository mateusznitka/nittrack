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
			
			//$now_hash = password_hash($now_pass, PASSWORD_DEFAULT);
			
			
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
						<span style="font-size:20px"><b>Informacje ogólne</b></span>
						<br><br>
						Zadaniem systemu jest monitorowanie pozycji i stanu obiektów takich jak samochód, motocykl czy maszyna rolnicza. Jednostka zamontowana w pojeździe wysyła na serwer dane lokalizacyjne, ktore możemy odczytać na interaktywnej mapie. Dzięki temu można sprawdzić, gdzie pojazd aktualnie się znajduje, zobaczyć przebieg tras w danym dniu czy dowiedzieć się, jaki dystans przebył w danym okresie.<br>
						Projekt ten został stworzony na potrzeby pracy dyplomowej inżynierskiej na kierunku Mechatronika na Wojskowej Akademii Technicznej. Praca została zaopiniowana, zrecenzowana i obroniona na ocenę bardzo dobrą 24 września 2019 roku.<br>
						Projekt obecnie jest używany w celach prywatnych i nadal rozwijany.<br><br>
						<span style="font-size:20px"><b>Koncepcja systemu</b></span>
						<br><br>
						Działanie systemu opiera się na o trzy zasadnicze elementy, a więc: <br>
						&emsp;  • jednostkę,<br>
						&emsp;  • serwer, <br>
						&emsp;  • użytkownika.<br>
						Jednostka składa się z mikrokontrolera oraz komunikujących się z nim modułów:<br>
						&emsp; • modułu GPS, <br>
						&emsp; • modułu GSM / GPRS. <br>
						Moduł  GPS,  a  właściwie  moduł  lokalizacji  satelitarnej  odbiera  fale  nawigacyjne i przy pomocy odpowiednich bibliotek możliwe jest odczytanie danych lokalizacyjnych, takich jak szerokość i długość geograficzna, 
						wysokość nad poziomem morza czy ilość satelit, z których w danym  czasie odbierany jest 
						sygnał.  Moduł  GSM  /  GPRS  transmituje  dane  do  serwera  oraz  odbiera  SMS-y  sterujące 
						działaniem urządzenia. 
						Serwer  jest  komputerem  z  oprogramowaniem  pozwalającym  udostępniać  strony 
						internetowe, przechowywać informacje w bazie danych oraz przetwarzać dane z urządzenia 
						i od użytkownika przy pomocy skryptów.   
						Na rysunku 1. został przedstawiony ogólny schemat działania systemu:<br>
						<img src = "img/schema1.png" style="height: 40%;"><span style="font-size:10px">Rys.1 Ogólny schemat dzialania systemu </span><br><br>
						Na rysunku 2. został przedstawiony ogólny schemat budowy systemu:<br>
						<img src = "img/schema2.png" style="height: 30%;"><span style="font-size:10px">Rys.2 Ogólny schemat budowy systemu </span><br><br>
						Jednostka jest wpięta w instalację elektryczną samochodu. Podłączenie jednostki wymaga ingerencji w instalację elektryczną pojazdu. Jeśli zapłon pojazdu jest wyłączony, jednostka jest w trybie uśpienia, co oznacza, że moduł GPS jest wyłączony, a moduł GSM jest w trybie odbierania SMS. Drugim trybem pracy urządzenia jest monitorowanie. Może ono zostać wywołane w dwóch przypadkach:<br>
						&emsp; •	włączenie zapłonu silnika,<br>
						&emsp; •	wysłanie na numer karty SIM urządzenia SMS-a o treści „ON”.<br><br>
						W trybie monitorowania zostaje uruchomiony moduł GPS i po pierwszym poprawnym uzyskaniu pozycji zostaje uruchomiona pętla wysyłania (domyślnie co około 20 sekund) danych lokalizacyjnych na serwer. Przejście z trybu monitorowania do trybu uśpienia możliwe jest poprzez wyłączenie zapłonu silnika.<br>
						Istnieje jeszcze tryb pośredni, kiedy to w trybie uśpienia wyślemy SMS-a o treści „PULL” na numer jednostki. Zostanie wtedy uruchomiony moduł lokalizacyjny i po uzyskaniu poprawnej pozycji zostanie ona wysłana do serwera. W przeciwieństwie do trybu monitorowania, czynność ta odbywa się jednokrotnie i po wysłaniu danych urządzenie ponownie przechodzi w tryb uśpienia.<br>
						Koncepcję działania aplikacji internetowej z punktu widzenia użytkownika opisuje diagram przypadków użycia przedstawiony na rysunku 3. <br><br>
						<img src = "img/diagram1.png" style="height: 100%;"><span style="font-size:10px">Rys.3 Diagram przypadków użycia </span><br><br>
						Na rysunku 4. został przedstawiony diagram wdrożeniowy systemu. Przedstawione zostały elementy systemu wraz z podstawowymi komponentami oraz rodzaj połączeń między urządzeniami.<br><br>
						<img src = "img/diagram2.png" style="height: 70%;"><span style="font-size:10px">Rys.4 Diagram wdrożeniowy systemu</span> <br>


				</div>
				
			</div>
		
		</div>
		
</body>
</html>

<?php
	$polaczenie->close();
?>