<?php
	include "connect.php";
	$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

	$dev_id = $_POST['dev_id'];
	
	$dev_status = "SELECT * from devices where id = '$dev_id'";
	$distance = "select concat((round(sum(dst), 2)), ' km') as 'distance' from (select (@row_number := @row_number +1) as row_number, calculate_distance(a.lng, a.lat, b.lng, b.lat) dst from (SELECT @row_number :=0) as x,
					positions a 
					join positions b on (a.id = b.id -1)
					where (date(b.time_added)) = date(now()) and b.device_id= '$dev_id' having row_number != 1) as cvx";
	
	$active = "SELECT timestampdiff(minute, time_added, now()) as dif from positions where device_id = '$dev_id' order by id desc limit 1";

	$sqli = $polaczenie -> query($dev_status);
	$rowx = $sqli -> fetch_assoc();	
	
	$sqld = $polaczenie -> query($distance);
	$rowd = $sqld -> fetch_assoc();	
	
	$sqlf = $polaczenie -> query($active);
	$rowf = $sqlf -> fetch_assoc();
	
	
	$token = $rowx['token'];
	$type = $rowx['type'];
	$name = $rowx['name'];
	$daily_distance = $rowd['distance'];
	$dif = intval($rowf['dif']);
	
	if ($dif < 10)
	{
		$dif = "Aktywny";
	}
	
	if ($dif >= 10 && $dif <= 60)
	{
		$dif = "Nieaktywny od ".$dif." minut";
	}
	
	if ($dif > 60 && $dif <= 1440)
	{
		$dif = "Nieaktywny od ".round(($dif / 60))." godzin";
	}
	
	if ($dif >= 1440)
	{
		$dif = "Nieaktywny od ".round(($dif / 1440))." dni";
	}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
</head>
	
<body>
		Status urządzenia:<br/>
		
		<div class="table-responsive" id="wyniki_table">
	   <table class="table table-bordered" width="100%">
		<tr>
		 <td width="40%" align="right"><b>Urządzenie</b></td>
		 <td width="60%"><?php echo $name; ?></td>
		</tr>
		<tr>
		 <td width="40%" align="right"><b>Status</b></td>
		 <td width="60%"><?php echo $dif; ?></td>
		</tr>
		<tr>
		 <td width="40%" align="right"><b>Dystans dzienny</b></td>
		 <td width="60%"><?php echo $daily_distance; ?></td>
		</tr>
	   </table>
	   </div>
	</body>
</html>