<?php
	//script converting sql data to geojson
	
	include "connect.php";
	$conn = new mysqli($host, $db_user, $db_password, $db_name);

	if ($conn->connect_errno!=0) 
	{
		echo "Error: ".$conn->connect_errno." Opis: ". $conn->connect_error;
	}
	else		
	{
		$dev_id = $_POST['dev_id']?? '1';
		$pos_date = $_POST['pos_date']?? '2018-12-02';
		$limiter = $_POST['limiter']?? '';
		
		$req = "SELECT id, lng, lat, speed, satellites, course FROM positions where device_id = '{$dev_id}' and date(time_added) = date('{$pos_date}') order by id desc $limiter";
		
		$geojson = array(
		   'type'      => 'FeatureCollection',
		   'features'  => array()
		);
		
		$sql = $conn -> query($req);

		while ($row = mysqli_fetch_assoc($sql)) {
			$feature = array(
				'type' => 'Feature', 
				'geometry' => array(
						'type' => 'Point',
						'coordinates' => array($row['lng'], $row['lat']),
						'properties' => array(
						'name' => 'Pozycja',
						'status' => 'aktywny',
						'speed' => array($row['speed']),
						'satellites' => array($row['satellites'])
						)
					)
				);
			array_push($geojson['features'], $feature);
		}
		header('Content-type: application/json');
		echo json_encode($geojson, JSON_NUMERIC_CHECK);
		$conn->close();

	}

?>