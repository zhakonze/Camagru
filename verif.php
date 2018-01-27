<?php
// Connect to MySQL
include_once ('config/ses.php');
include_once ('config/database.php');
include_once 'utilities.php';

if (isset($_POST["verify"]) && !empty($_POST["link"]))
{
    $link = htmlEntities($_POST["link"]);
    $username = htmlEntities($_POST["username"]);
    $hash = htmlEntities($_GET["q"]);
    $query = $db->prepare('SELECT link FROM users WHERE username = :username');
    $query->execute(array(':username' => $username));
    $linkExists = $query->fetch(PDO::FETCH_ASSOC);
    $link_check = $linkExists['link'];

    if ($link_check != $link)
    {
        $result =  "<p style='color: red;'>Your password reset key is invalid.<\p>";
    }
    else {
        $query = $db->prepare("UPDATE users SET active = '1' WHERE link = :link");
        $query->execute(array(':link' => $link));
        $result = "<p style='color: green;'>Account is now active</p>";
    }
}
?>



<html>
<head>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <title>Account Activation Page</title>
</head>

<body class="Dec">
    <header class="Pix">
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" /></left>
                <img class="image-5" src="logname.png" />
            </div>
    </header>
        <center><div class="mainC">
        <h1>Account Activation</h1>
<?php if(isset($result)) echo "<b>$result <b>" ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
        <?php echo '
        <form class="Regform" action="" method="POST">
            Username<br>
            <input type="text" name="username" placehoder="username">
            <br>
            Enter key sent to your in email<br>
            <input type="text" name="link" placehoder="E-mail">
            <br><br>
            <input type="hidden" name="q" value="';
            if (isset($_GET["q"])) {
                echo $_GET["q"];
            }
                echo '" /><input style="margin: 10px;" type="submit" name="verify" value=" Verify Activation " /></form>'; 
        ?>   
    </div></center>
<p><a href="log.php" style="color: blue;">Go to login</a></p>
</body>
</html>