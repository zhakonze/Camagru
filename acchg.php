<?php
include_once ('config/database.php');
include_once 'utilities.php';
include_once 'comment.php';
include_once ('config/ses.php');


if (isset($_POST['save']) && isset($_SESSION['username'])){
        
        $form_errors = array();
        $required_fields = array('fname', 'lname', 'email', 'username', 'password');
        $form_errors = array_merge($form_errors, check_empty_fields($required_fields));
        $fields_to_check_length = array('username' => 4, 'password' => 6);
        $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));
        $form_errors = array_merge($form_errors, check_email($_POST));

        if (empty($form_errors)){
            
            $ID = $_SESSION['id'];
            $fname = htmlEntities($_POST['fname']);
            $lname = htmlEntities($_POST['lname']);
            $username = htmlEntities($_POST['username']);
            $email = htmlEntities($_POST['email']);
            $password = htmlEntities($_POST['password']);
            $email_pref = "true";
            if (isset($_POST['email_pref'])){
                $email_pref = "false";
            }
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            try{
        
                $stmt = $db->prepare('UPDATE users SET fname = :fname, lname = :lname, username = :username, email = :email, email_pref = :email_pref, password = :hashed_password WHERE ID = :ID');
                $stmt->bindParam(':fname',$fname);
                $stmt->bindParam(':lname',$lname);
			    $stmt->bindParam(':username',$username);
			    $stmt->bindParam(':email',$email);
                $stmt->bindParam(':email_pref', $email_pref);
			    $stmt->bindParam(':hashed_password',$hashed_password);
			    $stmt->bindParam(':ID',$ID);
                $stmt->execute();
                
                $mailbody = '
                Changes were Made to your account!
                
                Your account has been updated, you can login with the following new credentials.
                ------------------------
                Username: '.$username.'
                Password: '.$password.'   
                ------------------------

                Thank you!';

                mail("$email", "www.noreply@camagru.com - Account updated", $mailbody);
   
                $result = "<p style='padding: 20px; color: green;'>Account was successfully updated!</p>";
            
            }catch (PDOException $er){
                $result = "<p style='padding: 20px; color: red'>An error occurred: ".$er->getMessage()." </p>";
            }
        }
        else{
            if(count($form_errors) == 1){
                $result = "<p style='color: red;'> There was 1 error in the form<br>";
            }else{
                $result = "<p style='color: red;'> There were " .count($form_errors). " error in the form <br>";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<title>Account Update</title>
</head>

<body class="Dec">
    <header class="Pix">
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" />
                <img class="image-5" src="logname.png" />
                </left>
            </div>
        </nav>
    </header>
        <?php if(!isset($_SESSION['username'])): ?>
        <P> You are currently not signed in <a href="log.php">Login</a> Not yet a member? <a href="Reg.php">Register</a> </P>
        <?php else: ?>
        <div class="logA"> You are currently logged in as  <a href="acc.php" style="color: green;"><?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?></a> <a href="logout.php" style="color: red;">Logout</a></div></br>
    <?php endif ?></br>
    <div class="mainC">
        <h1>Account Update</h1>
        <?php if(isset($result)) echo "<b>$result <b>" ?>
        <?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
        <form class="Regform" method="POST">
            First name<br>
            <input type="text" name="fname" placeholder="First name" >
            <br>
            Last name<br>
            <input type="text" name="lname" placeholder="Last name">
            <br>
            Email<br>
            <input type="text" name="email" placehoder="E-mail">
            <br>
            Username<br>
            <input type="text" name="username" placeholder="Username">
            <br>
            Password<br>
            <input type="password" name="password" placehoder="Password">
            <br></br>
            <input type="checkbox" name="email_pref" value="Notify" style="height: 1.5vh; width: 1.5vw;">Do not send me emails</br></br>
            <button type="submit" name="save" value="save">Save</button>
        </form>    
    </div>
<p><a href="index.php" style="color: white;">Back to login</a></p>
</body>
</html>