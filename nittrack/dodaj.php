<?php

	include "connect.php";
	
	$conn = @new mysqli($host, $db_user, $db_password, $db_name); 
	
	if ($conn->connect_errno!=0) 
	{
		echo "Error: ".$conn->connect_errno." Opis: ". $conn->connect_error;
	}
	else
	{
		$lng = $_POST["lng"];
		$lat = $_POST["lat"];
		$speed = $_POST["speed"];
		$gps_time = $_POST["gps_time"];
		$satellites = $_POST["satellites"];
		$course = $_POST["course"];
		$dev_id = $_POST["dev_id"];
		$device_status = "drive";
		$sql = "INSERT INTO positions (device_id, lng, lat, time_added, speed, gps_time, satellites, course, device_status) VALUES 
				('".$dev_id."', '".$lng."','".$lat."', now(), '".$speed."', '".$gps_time."', '".$satellites."', '".$course."', '".$device_status."')";
		$wynik = $conn->query($sql);
		echo "dziala";
		
		$conn->close();
	}
?>