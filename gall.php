<?php
 include_once ('config/database.php');
 include_once 'comment.php';
 include_once ('config/ses.php');
 include_once 'delete.php';
 date_default_timezone_set('Africa/Johannesburg');
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<meta charset="UTF-8">
<title>gallery page</title>
</head>  
<body class="Dec">
    <header class="Pix">
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" /></center>
                <img class="image-5" src="logname.png" />
    </header>
    <center><div class="">
        <h1>Gallery</h1>
        <?php if(!isset($_SESSION['username'])): ?>
        <P> You are currently not signed in <a href="log.php">Login</a> Not yet a member? <a href="Reg.php">Register</a> </P>
        <?php else: ?>
        <div class="logA"> You are currently logged in as  <a href="acc.php" style="color: green;"><?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?></a> <a href="logout.php" style="color: red;">Logout</a></div></br>
        <?php endif ?>
        <p><a href="index1.php" style="color: white;">Back to Home</a></p>
    </div></center>
<div class="mid">
<!--<========================================PAGINATION========================================================>-->
    <?php
                $results_per_page = 5;
		
                $sql = "SELECT * FROM image";
                $stmt = $db->prepare($sql);
                $stmt->execute();

                $number_of_results = $stmt->rowcount();

                if (!isset($_GET['page'])){
                    $page = 1;
                }
                else {
                    $page = $_GET['page'];
                }

                $this_page = ($page - 1) * $results_per_page;

                $sql = "SELECT * FROM image LIMIT ? , ?";
                $stmtp = $db->prepare($sql);
                $stmtp->bindValue(1, $this_page, PDO::PARAM_INT);
                $stmtp->bindValue(2, $results_per_page, PDO::PARAM_INT);
                $stmtp->execute();
  
//<================================================================================================================>-->  
    
//                $stmt = ('SELECT * FROM image;');
//                $stmt = $db->prepare($stmt);
//                $stmt->execute();


                $images = $stmtp->fetchAll(PDO::FETCH_ASSOC);

                
                foreach($images as $image)
                {
                
            ?>
                <?php
                    $stmt = ('SELECT * FROM likeimg WHERE POST = '.$image['ID'].';');
                    $stmt = $db->prepare($stmt);
                    $stmt->execute();
                    $likes = $stmt->rowCount();
                    ?>
                <?php
                    $sqlInsert = 'SELECT username FROM likeimg WHERE POST = :image';
                    $stmt = $db->prepare($sqlInsert);
                    $stmt->execute(array(
                        "image" => $image['ID']));
                    $row = $stmt->fetchAll();
                    $run_bool = 0;
                    foreach ($row as $users){
                        if (in_array($_SESSION['username'], $users))
                            $run_bool = 1;
                    }
                    ?>
                <div class=imageDiv>
                    <?php echo "Post by-".$image['username']?>
                    <?php if ($_SESSION['username'] == $image['username']) {?>
                    <a class=del href="?delete_id=<?php echo $image['name']?>" action="delete.php" type='submit' name='delimg' style="float: right" onclick="return confirm('Are you sure you want to delete this image?')">Delect image</a>
                    <?php } ?>
                    <a   href="<?php echo  $image['name']; ?>"><img class = "profilePic" src="<?php echo $image['name']; ?>"/>
                    </a>
                    <?php
                        if ($run_bool == 0){
                    ?>
                    <a href="like.php?image=<?php echo $image['ID']?>"><img src="like.png"  height="30" width="30"/></a>
                    <?php }else{ ?>
                    <a href="like.php?image=<?php echo $image['ID']?>"><img src="like.png" height="30" width="30" class="grey"/></a>
                    <?php }?>
                    <p class="lik"><?php echo $likes;?></p>
                    <?php if(isset($result)) echo $result; ?>
                    <?php 
                    $sqlInsert = 'SELECT email FROM users WHERE username = :username';
                    $stmt = $db->prepare($sqlInsert);
                    $stmt->execute(array(
                        "username" => $image['username']));
                    $row = $stmt->fetchAll();
                    $email = isset($row[0]) ? $row[0]['email'] : '[deleted]';
                    ?>
                    <?php 
                        echo "<form method='POST' action='comment.php'>
                                <textarea name='comment'></textarea>
                                <input type='hidden' name='POST' value='{$image["ID"]}'>
                                <input type='hidden' name='imgusr' value='{$image["username"]}'>
                                <input type='hidden' name='username' value='$username'>
                                <input type='hidden' name='email' value='$email'>
                                <input type='hidden' name='date' value='".date('Y-m-d H:i:s')."'><br>
                                <button type='submit' name='comSubmit' class = 'dropbtn'>Comment</button>
                              </form>"."<br>";

                               echo  "<div class='comDiv'>";  
                                $stmt = ("SELECT * FROM comments WHERE POST = {$image['ID']};");
                                $stmt = $db->prepare($stmt);
                                $stmt->execute();

                                $com = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                                foreach($com as $comment)
                                {
                                    echo "<div class='comBox'>";
                                        echo $comment['username']." ";
                                        echo $image['ID']." ";
                                        echo $comment['date']."<br>";
                                        echo htmlspecialchars($comment['comment']);
                                    echo "</div>"."<br>";
                                }
                        echo "</div>";
                    ?>
                </div>
            <?php
                        
                }
              
            ?>
    <script>
        function add1(element) {
            var xhtpp;
            var imgDiv = element.parentElement.parentElement;
            var output = imgDiv.querySelector("#output");

            output.value = parseInt(output.value,10) + 1;
            xhttp = new XMLHttpRequest();
            xhttp.open("POST", '/Camagru/like.php');
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            
            xhttp.onreadystatechange = function() {
                if(xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200) {
        
                }
            }
            xhttp.send("POST=0&likes=2");
        }
        
        function myFunction(x) {
            x.classList.toggle("like.png");
        }
    </script>
</div>            
<!--<========================================START PAGINATION========================================================>-->
        <center><div class="clearfix">
        <?php
            $number_of_pages = ceil($number_of_results/$results_per_page);
            for ($page=1; $page <= $number_of_pages ; $page++) { 
                echo '<a href="gall.php?page=' . $page . '">' . $page . '</a>' . "-"; 
            }
        ?>    
        </div></center>
<!--<========================================END PAGINATION========================================================>-->
</body>

</html>
    
    

                