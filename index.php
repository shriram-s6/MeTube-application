<?php require_once("header.php"); ?>
<?php require_once("config.php"); ?>
<?php require_once("footer.php"); ?>
<?php require_once("classes/VideoGrid.php"); ?>
<link rel="stylesheet" type="text/css" href="css/videoGrid.css">
<?php 



$videoGrid = new VideoGrid($connect, $user);
echo $videoGrid->create(null);


?>
