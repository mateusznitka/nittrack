<?php
	
	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['password'])))
	{
		header('Location: login.php');
		exit();
	}		
	
	require_once "connect.php";
	
	$conn = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($conn -> connect_errno!=0)
	{
	    echo "Error: ".$conn->connect_errno;
    }
	else
	{
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		
		$sql = sprintf("SELECT * FROM nt_users WHERE login = '%s'",
			mysqli_real_escape_string($conn, $login));
		
		if ($result = @$conn->query($sql))
		{
			$how_many = $result -> num_rows;
			if($how_many>0)
			{
				$row = $result-> fetch_assoc();
				
				if (password_verify($password, $row['password']))
				{
					
					$_SESSION['logged'] = true;
					
					$_SESSION['user'] = $row['username'];
					$_SESSION['id'] = $row['id'];
					$_SESSION['email'] = $row['email'];
					$_SESSION['phone'] = $row['phone'];
					
					unset($_SESSION['error']);
					$result->close();
					header('Location: index.php');
				}
				
				else
				{
					$_SESSION['error'] = '<span style = "color:red; font-size: 12px">Nieprawidłowe hasło</span>';
					header('Location: login.php');
				}
				
			}
			else
			{
				$_SESSION['error'] = '<span style = "color:red; font-size: 12px">Nieprawidłowy login lub hasło</span>';
				header('Location: login.php');
			}
		}

		$conn->close();
	}
?>