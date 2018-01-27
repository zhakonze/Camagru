<?php
include_once ('config/database.php');
include_once 'utilities.php';

    if (isset($_POST['submit'])){

        $form_errors = array();
        $required_fields = array('fname', 'lname', 'email', 'username', 'password');
        $form_errors = array_merge($form_errors, check_empty_fields($required_fields));
        $fields_to_check_length = array('username' => 4, 'password' => 6);
        $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));
        $form_errors = array_merge($form_errors, check_email($_POST));
        if (empty($form_errors)){

            $fname = htmlEntities($_POST['fname']);
            $lname = htmlEntities($_POST['lname']);
            $username = htmlEntities($_POST['username']);
            $email = htmlEntities($_POST['email']);
            $pass = htmlEntities($_POST['password']);
            if ((preg_match('/.{6,100}/', $pass) == 0) || (preg_match('/[0-9]/', $pass) == 0) || (preg_match('/[a-zA-Z]/', $pass) == 0))
            {
                $result = "<p style='padding: 20px; color: red'>Password must contain atleast a digit: </p>";
            }
            else{
            $password = $pass;       
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            try{

                $sqlInsert = "INSERT INTO users (fname, lname, username, email, password, join_date)
                      VALUES (:fname, :lname, :username, :email, :password, now())";

                $statement = $db->prepare($sqlInsert);
                $statement->execute(array(
                    'fname' => $fname,
                    'lname' => $lname,
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashed_password
                ));

                $salt = bin2hex(openssl_random_pseudo_bytes(16));    
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $code = substr(str_shuffle($chars), 0, 35);
                $link = "http://localhost:8080/Camagru/verif.php?q=".$code;
                $mailbody = '
                Thanks for signing up!
                
                Your account has been created, you can login with the following credentials after you have activated your account with the key code
                provided below, use the link to connect you to the code fill in form.
                ------------------------
                Username: '.$username.'
                Password: '.$password.'
                Key     : '.$code.'    
                ------------------------

                Please click/copy and paste this link to submit the Key provided above:
                key::'.$link.'';

                mail("$email", "www.noreply@camagru.com - Account Activation", $mailbody);

                
                $query = $db->prepare('UPDATE users SET link = :link WHERE username = :username');
                $query->bindParam(':link', $code);
                $query->bindParam(':username', $username);
                $query->execute();
                
                if ($statement->rowCount() == 1){
                    $result = "<p style='padding: 20px; color: green;'>Please check your Email to verify your account</p>";
                }
            }catch (PDOException $er){
                $result = "<p style='padding: 20px; color: red'>An error occurred: ".$er->getMessage()." </p>";
            }
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
<title>Register Page</title>
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
    <div class="mainC">
        <h1>Register</h1>
        <?php if(isset($result)) echo "<b>$result <b>" ?>
        <?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
        <form class="Regform" action="" method="POST">
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
            <br><br>
            <button type="submit" name="submit" value="Submit">Sign Up</button>
        </form>    
    </div>
<p><a href="index.php" style="color: white;">Back to login</a></p>
</body>
</html>