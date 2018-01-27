<?php opcache_reset();?>
<?php
include_once ('config/database.php');
include_once ('config/ses.php');

if (isset($_POST['comSubmit'])){

    $POST = htmlEntities($_POST['POST']);
    $comment = htmlEntities($_POST['comment']);
    $username = htmlEntities($_SESSION['username']);
    $email = htmlEntities($_POST['email']);
    $image = htmlEntities($_POST['imgusr']);
    $date = htmlEntities($_POST['date']);
    
    try{
    $sqlInsert = "INSERT INTO comments (POST, comment, username, date) VALUES (:POST, :comment, :username, :date)";
            $stmt = $db->prepare($sqlInsert);
        
            $stmt->execute(array(
                    ':POST' => $POST,
                    ':comment' => $comment,
                    ':username' => $username,
                    ':date' => $date
            ));
        
    $check_email_pref = "SELECT email_pref FROM users WHERE username=:username";
    $check_email_stmt = $db->prepare($check_email_pref);
    $check_email_stmt->execute(array(':username' => $image));
    $row = $check_email_stmt->fetch();
    $email_pref = $row['email_pref'];

    if($username != $image && $email_pref === "true"){
        $link = "http://localhost:8080/Camagru/index.php";
        $mailbody = '
                        '.$image.'

                        Someone commented on a picture you posted:
                        '.$comment.'
                        To find out more vist ::'.$link.'';

        mail("$email", "www.noreply@camagru.com - Comment Notification", $mailbody);
    }

            if ($stmt->rowCount() == 1){
                $result = "<p style='padding: 20px; color: green;'>Comment was Successful </p>";
                header("location: gall.php?Comment was Successful");
            }
    }
    catch (PDOException $er){
            $result = "<p style='padding: 20px; color: red'>An error occurred: ".$er->getMessage()." </p>";
    }
    
}

?>