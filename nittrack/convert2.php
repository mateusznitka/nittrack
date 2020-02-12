<?php

	session_start();
	
	if (!isset($_SESSION['logged']))
	{
		header('Location: login.php');
		exit();
	}

	include "connect.php";
	$conn = new mysqli($host, $db_user, $db_password, $db_name);


		$user_id = $_SESSION["id"];
		
		$zapytanie = "SELECT positions.id, devices.name name, devices.type type, positions.device_id, positions.lng, positions.lat, positions.time_added, positions.speed, positions.satellites, positions.device_status
						FROM positions, devices, devices_assign where positions.id IN 
						(
							SELECT max(id)
							FROM positions
							GROUP by device_id
						) and positions.device_id = devices.id and devices.id = devices_assign.device_id and devices_assign.user_id = '{$user_id}'";
		
		$geojson = array(
		   'type'      => 'FeatureCollection',
		   'features'  => array()
		);
		$sql = $conn -> query($zapytanie);

		while ($row = mysqli_fetch_assoc($sql)) {
			$feature = array(
				'type' => 'Feature', 
				'geometry' => array(
					'type' => 'Point',
					'coordinates' => array($row['lng'], $row['lat']),
				'properties' => array(
				'name' => array($row['name']),
				'status' => array($row['device_status']),
				'speed' => array($row['speed']),
				'satellites' => array($row['satellites']),
				'dev_type' => array($row['type'])
				)
				)
				);
			array_push($geojson['features'], $feature);
		}
		header('Content-type: application/json');
		echo json_encode($geojson, JSON_NUMERIC_CHECK);
		$conn->close();

?>
