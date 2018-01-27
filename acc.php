<?php
include_once ('config/database.php');
include_once 'utilities.php';
include_once ('config/ses.php');
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<title>My Account</title>
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
        <h1>My Account</h1>
        <?php if(isset($result)) echo "<b>$result <b>" ?>
        <?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
        <?php
            $query = "
                SELECT fname, lname, username, email FROM users WHERE username = '".$_SESSION['username']."' ";

            try
            {
                $stmt = $db->prepare($query);
                $stmt->execute();
            }
            catch(PDOException $ex)
            {
                die("Failed to run query: " . $ex->getMessage());
            }
            $rows = $stmt->fetchAll();
        ?>
        </tr>
    <?php foreach($rows as $row): ?>
        <tr>
            <td><?php echo "First Name:" ?></td></br>
            <td><?php echo htmlentities($row['fname'], ENT_QUOTES, 'UTF-8'); ?></td></br></br>
            <td><?php echo "Last Name:" ?></td></br>
            <td><?php echo htmlentities($row['lname'], ENT_QUOTES, 'UTF-8'); ?></td></br></br>
            <td><?php echo "Username:" ?></td></br>
            <td><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ?></td></br></br>
            <td><?php echo "Email:" ?></td></br>
            <td><?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?></td></br></br>
        </tr></br>
    <?php endforeach; ?>
    </div></br>
              
<button class="dropbtn" id="capture" id="botton-capture"><a href="acchg.php"><b>Edit Account</b></a></button>
<p><a href="index1.php" style="color: white;">Back to Home</a></p>
</body>
</html>