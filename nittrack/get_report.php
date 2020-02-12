<?php

include "connect.php";
$conn = new mysqli($host, $db_user, $db_password, $db_name);

$report_id = $_POST['report_id'];
$pos_date = $_POST['pos_date'];
$end_date = $_POST['end_date'];
$dev_id = $_POST['dev_id'];

if ($report_id == 1)
{
	$sql = "select device_id 'ID urządzenia', lng 'Długość geograficzna', lat 'Szerokość geograficzna', time_added 'Czas dodania' from positions where device_id = '{$dev_id}' and time_added between '{$pos_date}' and '{$end_date}' order by time_added desc";
}

if ($report_id == 2)
{
	$sql = "select id 'ID', speed 'Prędkość', lng 'Długość geograficzna', lat 'Szerokość geograficzna', time_added 'Czas dodania' from positions where speed > 120 and device_id = '{$dev_id}' and time_added between '{$pos_date}' and '{$end_date}'";
}

if ($report_id == 3)
{	
	$sql = "select sum(dst) as 'Suma kilometrów' from (select (@row_number := @row_number +1) as row_number, calculate_distance(a.lng, a.lat, b.lng, b.lat) dst from (SELECT @row_number :=0) as x,
positions a 
join positions b on (a.id = b.id -1)
where (date(b.time_added) between date('{$pos_date}') and date('{$end_date}')) and b.device_id= '{$dev_id}' having row_number != 1) as cvx";		
}

//execute query
$wynik = $conn->query($sql);

echo "<p style=\"font-size:14px;\">Poniżej Twój Raport:<br></p>";
echo "<p>";
echo "<table boder=\"1\"><tr>";

$finfo = mysqli_fetch_fields($wynik);
foreach ($finfo as $val)
{
	echo "<td><strong>";
	echo $val->name;
	echo "</strong></td>";
}
echo "</tr>";

//loop for show data in table
 while ( $row = $wynik->fetch_assoc()) 
 {
    echo "<tr>";
		foreach ($row as $column)
		{
			echo "<td>{$column}</td>";
		}
    echo "</tr>";
 }
 echo "</table>";
 echo "<br>";
 echo "<p style=\"font-size:10px;\">Raport wygenerowano dnia ";
 $date = date('d-m-Y'); echo $date;
 echo ".</p>";  
 
 ?>