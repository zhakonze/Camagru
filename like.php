<?php
include_once ('config/database.php');
include_once ('config/ses.php');

$image_id = htmlEntities($_GET['image']);
$username = htmlEntities($_SESSION['username']);
$date = date('Y-m-d H:i:s');
    
    $sqlInsert = 'SELECT username FROM likeimg WHERE POST = '.$image_id;
    $stmt = $db->prepare($sqlInsert);
    $stmt->execute();
    $row = $stmt->fetchAll();
    $run_bool = 0;
    foreach ($row as $users){
        if (in_array($username, $users))
            $run_bool = 1;
    }
    if ($run_bool == 0){
    $sqlInsert = "INSERT INTO likeimg (username, POST, date) VALUES (:username, :POST, :date)" ;
    $stmt = $db->prepare($sqlInsert);
    $stmt->execute(array(
                ':username' => $username,
                ':POST' => $image_id,
                ':date' => $date
    ));
    }else{
        $del = 'DELETE FROM likeimg WHERE username = :username AND POST = :POST';
        $stmt = $db->prepare($del);
        $stmt->execute(array(':username' => $username, ':POST' => $image_id));
    }
    header('Location: http://localhost:8080/Camagru/gall.php');
?>