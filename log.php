<?php
include_once ('config/ses.php');
include_once ('config/database.php');
include_once 'utilities.php';

if(isset($_POST['submit'])){

    $form_errors = array();
    $required_fields = array('username', 'password');
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    if (empty($form_errors)) {

        $user = htmlEntities($_POST['username']);
        $password = htmlEntities($_POST['password']);
        $active = htmlEntities($_POST['active']);
        $sqlQuery = "SELECT * FROM users WHERE username = :username";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':username' => $user));

        while ($row = $statement->fetch()){
            $id = $row['ID'];
            $hashed_password = $row['password'];
            $username = $row['username'];
            $active = $row['active'];

            if(password_verify($password, $hashed_password) && $active == '1'){
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                header("location: index1.php");
            }else if(password_verify($password, $hashed_password) && $active == '0'){
                $result = "<p style='padding: 20px; color: red; border: 1px solid gray'>Account has not be activated</p>";
            }
            else{
                $result = "<p style='padding: 20px; color: red; border: 1px solid gray'>Invalid username or password</p>";
            }
        }
    }else{
        if(count($form_errors) == 1){
            $result = "<p style='color: red;'>There was one error in the form </p>";
        }else{
            $result = "<p style='color: red;'>There were " .count($form_errors). " error in the form </p>";
        }
    }
}


?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<meta charset="UTF-8">
<title>Login Page</title>
</head>  

<body class="Dec">
    <header class="Pix">
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" /></center>
                <img class="image-5" src="logname.png" />
    </header>
    <center><div class="mainC">
        <h1>Login</h1>
<?php if(isset($result)) echo $result; ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors);?>
        <form class="Regform" action="" method="POST">
            Username<br>
            <input type="text" name="username" placeholder="Username">
            <br>
            Password<br>
            <input type="password" name="password" placehoder="Password">
            <br><br>
            <input type="hidden" name="active" placehoder="active">
            <br><br>
            <button type="submit" name="submit" value="Signin ">Login</button>
        </form><br>    
    </div></center>
            <center><a href="forgotpwd.php"> <span>Forgot password?</span></a></center>
</body>
</html>