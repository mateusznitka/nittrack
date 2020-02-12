<?php

$haslo = $_POST['new_pass'];
$confirm_pass = $_POST['confirm_pass'];

$haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);
echo $haslo_hash;
exit();
 
?>