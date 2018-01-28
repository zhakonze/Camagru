<?php

include_once ('database.php');
$user = '*******';
$password = '************';
$dsn = 'mysql:host=localhost';
try{
	$concreate = new PDO($dsn, $user, $password);

	$concreate->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
	echo '<h3>Connection Failed '.$e->getMessage().'</h3>';
}

$create = "CREATE DATABASE IF NOT EXISTS `camagru`";
$create_stmt = $concreate->prepare($create);

$create_stmt->execute();
$table = "CREATE TABLE IF NOT EXISTS `comments` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`POST` int(11) NOT NULL,
    `comment` text NOT NULL,
    `username` int(11) NOT NULL,
	`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$table_stmt = $db->prepare($table);
$table_stmt->execute();

$table = "CREATE TABLE IF NOT EXISTS `image` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(256) NOT NULL,
	`username` varchar(256) NOT NULL,
    `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_stmt = $db->prepare($table);
$table_stmt->execute();

$table = "CREATE TABLE IF NOT EXISTS `likeimg` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(100) NOT NULL,
    `POST` int(11) NOT NULL,
	`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_stmt = $db->prepare($table);
$table_stmt->execute();

$table = "CREATE TABLE IF NOT EXISTS `users` (
    `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,	
    `fname` varchar(50) NOT NULL,
    `lname` varchar(50) NOT NULL,
    `username` varchar(50) NOT NULL,
    `email` varchar(60) NOT NULL,
    `password` varchar(255) NOT NULL,
    `join_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `active` int(1) DEFAULT '0',
    `link` varchar(256)  NULL,
    `email_pref` varchar(6) DEFAULT 'true',
	PRIMARY KEY (`ID`),
	UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$table_stmt = $db->prepare($table);
$table_stmt->execute();
?>
