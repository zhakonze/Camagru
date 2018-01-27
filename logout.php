
<?php include_once ('config/ses.php');

session_destroy();
header('location: index.php');