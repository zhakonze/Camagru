<?php opcache_reset();?>
<!DOCTYPE html>
<?php
    include_once ('config/database.php');
    include_once 'utilities.php';
    include_once ('config/ses.php');
    if (!isset($_SESSION['username']))
        header('location: index.php');

try {
    if (isset($_POST['image-url']))
    {
        $overlay = $_POST['watermark'];
        $img = $_POST['image-url'];
        $rand = rand(0, 9999);
        $file_dir = "user_images/";
        $file_name = $_SESSION['username'].$rand.".jpg";
        $img = explode(',', $img);
        $decoded = base64_decode($img[1]);
        file_put_contents($file_dir.$file_name, $decoded);      

        $watermark = imagecreatefrompng($overlay);
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        $image = imagecreatefromjpeg($file_dir.$file_name);
        imagecopy($image, $watermark, 165, 120, 0, 0, $watermark_width, $watermark_height); 
        imagejpeg($image, $file_dir.$file_name);
        imagedestroy($image);
        imagedestroy($watermark);

        $filepath = $file_dir.$file_name;
        $username = $_SESSION['username'];
 
        $insert = "INSERT image (ID, name, username, edit_time)
            VALUE (null, '$filepath', '$username', now())";
    
        $stmt = $db->prepare($insert);
        $stmt->execute();

    }
}
catch(PDOException $ex){
    echo $insert ."<br>". $ex->getMessage();
}


//UPLOAD:
if(isset($_POST['submit'])) {
    $username = $_SESSION['username'];
    $file = $_FILES['file'];
      
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];
    
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    
    $allowed = array('jpg', 'jpeg', 'png');
    
    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if($fileSize < 1000000) {
                $fileNameNew = uniqid('', true).".".$fileActualExt; 
                $fileDestination = 'user_images/'.$fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);
                $insert_query="INSERT INTO image (name, username) VALUES('$fileDestination', '$username')";
                $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $stmt = $db->prepare($insert_query);
                $stmt->execute();
                $result = "<p style='padding: 20px; color: red;'>Upload was successful!</p>";
            }else {
                $result = "<p style='padding: 20px; color: red;'>You have chosen a file of large size!</p>";
            }  
        }else {
          $result =  "<p style='padding: 20px; color: red;'>There was an error while uploading your file!</p>"; 
        }
    }else {
        $result =  "<p style='padding: 20px; color: red;'>You have choosen an invaild file type!</p>";
    }
    
} 


?>
<html>
<head>
<meta charset="utf-8">
<meta content="stuff, to, help, search, engines, not" name="keywords">
<meta content="What this page is about." name="description">
<meta content="Display Webcam Stream" name="title">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style/style.css" />
<title>Camagru.com</title>
<meta name="viewport" content="width=device-width">
</head>

<body>
    <header>
        <nav>
            <div class="main-wrapper">
                <left><img class="image-1" src="logo1.png" /></center>
                <img class="image-5" src="logname.png" />
            <table>
                <div class="leftblock">
                    <div class="cat">
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="gall.php">Gallery</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
            </table><br/>
        <?php if(!isset($_SESSION['username'])): ?>
        <P> You are currently not signed in <a href="log.php">Login</a> Not yet a member? <a href="Reg.php">Register</a> </P>
        <?php else: ?>
        <div class="logA"> You are currently logged in as  <a href="acc.php" style="color: green;"><?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?></a> <a href="logout.php" style="color: red;">Logout</a></div></br>
        <?php endif ?>
    </header>
    <div id="wrapper">
        <?php if(isset($result)) echo $result; ?>
        <?php if(!empty($form_errors)) echo show_errors($form_errors);?>
        <div class="thumbnail"> 
                <canvas id="canvas" width="1280px" height="920px"></canvas>
            <button id="save-image" class="dropbtn">Save to Gallery</button>
            <form action="" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file" style = "display: inline-block;">
                    <input type='hidden' name='username' value="$username">
                    <button type="submit" name="submit" id="save-image" class = "dropbtn" style = "display: inline-block;">Upload</button>
            </form>
            <form method="post" id="image-form">
                <input type="hidden" name="image-url" id="image-url" value="" />
                <input type="hidden" name="watermark" id="watermark" value="" />
            </form>
            <div class="dropdown">
                <button class="dropbtn">Add an effect</button>
                <div class="dropdown-content">
                    <a href="#" id="clown">Clown</a>
                    <a href="#" id="clownf">Clown Face</a>
                    <a href="#" id="gof">Goofy</a>
                    <a href="#" id="wig">Wig</a>
                </div>
            </div>
        </div>
        <div id="container">
            <img src="http://25.media.tumblr.com/893a9f4aff967bc4f0cc61ad43b1ca9a/tumblr_mj9xk2y6iX1r11lhoo1_500.gif" id="flash-image" class="flash-image hide"/>
            <img src="" class="overlay hide" id="overlay">
            <video autoplay="true" id="videoElement"></video>
            <button class="dropbtn" id="capture" id="botton-capture"><b>Capture</b></button>
            <script>
                    var video = document.getElementById('videoElement'),
                    canvas = document.getElementById('canvas'),
                    context = canvas.getContext('2d'),
                    curr_object = [];
                    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

                    if (navigator.getUserMedia) {       
                    navigator.getUserMedia({video: true}, handleVideo, videoError);
                    }
                    function hasClass(el, className) {
                      if (el.classList)
                        return el.classList.contains(className)
                      else
                        return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'))
                    }

                    function addClass(el, className) {
                      if (el.classList)
                        el.classList.add(className)
                      else if (!hasClass(el, className)) el.className += " " + className
                    }

                    function removeClass(el, className) {
                      if (el.classList)
                        el.classList.remove(className)
                      else if (hasClass(el, className)) {
                        var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
                        el.className=el.className.replace(reg, ' ')
                      }
                    }
                    function handleVideo(stream) {
                    video.src = window.URL.createObjectURL(stream);
                    }

                    function videoError(e) {
                    // do something
                    }
                    document.getElementById('capture').addEventListener('click', function() {
                        if (curr_object.length == 0){
                            alert('Please select and overlay');
                        }else{
                        context.drawImage(video, 0, 0, 1280, 920);
                        flash = document.getElementById('flash-image');
                        removeClass(flash, 'hide');
                        setTimeout(function(){
                            addClass(flash, 'hide');
                        }, 500);
                        }
                    });
                    document.getElementById('save-image').addEventListener('click', function(){
                        var img = canvas.toDataURL('image/jpeg');
                        var field = document.getElementById('image-url');
                        var overlay = document.getElementById('watermark');
                        overlay.value = curr_object[0];
                        field.value = img;
                        document.getElementById('image-form').submit();
                    });
                
                    document.getElementById('clown').addEventListener("click", function(){
                        var elem = document.getElementById('overlay');
                        removeClass(elem, "hide");
                        elem.src = "Clown_Mask.png";
                        curr_object[0] = "Clown_mask.png";
                    });
                    document.getElementById('clownf').addEventListener("click", function(){
                        var X = document.getElementById('overlay');
                        removeClass(X, "hide");
                        X.src = "clownFace.png";
                        curr_object[0] = "clownFace.png";
                    })
                    document.getElementById('gof').addEventListener("click", function(){
                        var Y = document.getElementById('overlay');
                        removeClass(Y, "hide");
                        Y.src = "goofy.png";
                        curr_object[0] = "goofy.png";
                    })
                    document.getElementById('wig').addEventListener("click", function(){
                        var Z = document.getElementById('overlay');
                        removeClass(Z, "hide");
                        Z.src = "wig.png";
                        curr_object[0] = "wig.png";
                    })
            </script>
        </div>
    </div>
</body>
<div class="footer">
  <p>Copyright © CaMagRu.com</p>
</div>
</html>






