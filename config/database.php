<?php

$username = '*****';
$dsn = 'mysql:host=localhost;dbname=camagru;port=8080';
$password = "**********";

try{
	$db = new PDO($dsn, $username, $password);
	//set pdo error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch (PDOException $ex){
	echo "Connection failed".$ex->getMessage();
}
?>
