<?php
include_once ('config/database.php');
include_once ('config/ses.php');

if(isset($_GET['delete_id']))
{
    $stmt_select=$db->prepare('SELECT * FROM image WHERE name=:name');
    $stmt_select->execute(array(':name'=>$_GET['delete_id']));
    $imgRow=$stmt_select->fetch(PDO::FETCH_ASSOC);
    unlink($imgRow['name']);
    $stmt_delete=$db->prepare('DELETE FROM image WHERE name =:name');
    $stmt_delete->bindParam(':name', $_GET['delete_id']);
    if($stmt_delete->execute())
    {
        ?>
        <script>
        alert("You are deleted one item");
        window.location.href=('gall.php');
        </script>
        <?php 
    }else
 
    ?>
        <script>
        alert("Can not delete item");
        window.location.href=('gall.php');
        </script>
        <?php 
 
}
 
?>