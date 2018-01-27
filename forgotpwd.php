<?php
include_once ('config/ses.php');
include_once ('config/database.php');
include_once 'utilities.php';


if (isset($_POST["send"])) {

    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST["email"];
         
    }else{
        echo "email is not valid";
        exit;
    }

    $query = $db->prepare('SELECT email FROM users WHERE email = :email');
    $query->bindParam(':email', $email);
    $query->execute();
    $userExists = $query->fetch(PDO::FETCH_ASSOC);
    $db = null;

     

    if ($userExists["email"]){
        
        $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
        $password = hash('sha512', $salt.$userExists["email"]);
        $pwrurl = "http://localhost:8080/Reset.php?q=".$password;
        $mailbody = "Dear user,\n\nIf this e-mail does not apply to you please ignore it. It appears that you have requested a password reset at our website www.camagru.com\n\nTo reset your password, please click the link below. If you cannot click it, please paste it into your web browser's address bar.\n\n" . $pwrurl . "\n\nThanks,\nThe Administration";

        mail($userExists["email"], "www.noreply@camagru.com - Password Reset", $mailbody);
        $result = "<p style='color: green;'>Your password recovery key has been sent to your e-mail address.</p>";
    }
    else
        $result = "<p style='color: red;'>No user with that e-mail address exists.</p>";
}

?>


<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<title>Forgot Password Page</title>
</head>

<body class="Dec">
    <header class="Pix">
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" /></center>
                <img class="image-5" src="logname.png" />
    </header>
    <center><div class="mainC">
        <h1>Enter your email to Reset password</h1>
<?php if(isset($result)) echo "<b>$result <b>" ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
        <form class="Regform" action="" method="POST">
            Email<br>
            <input type="text" name="email" placehoder="email">
            <br><br>
            <button type="send" name="send" value="send">Send</button>
        </form>    
    </div></center>
<p><a href="index.php">Back to login</a></p>
</body>
</html>