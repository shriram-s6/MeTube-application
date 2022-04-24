<?php require_once("header.php"); ?>
<?php require_once("config.php"); ?>
<?php require_once("footer.php"); ?>
<?php require_once("classes/VideoGrid.php"); ?>
<link rel="stylesheet" type="text/css" href="css/videoGrid.css">
<h3 style='text-align: center;'>MeTube</h3>
<br>
<?php 

//error_reporting(E_ERROR | E_PARSE);


$videoGrid = new VideoGrid($connect, $user);
echo $videoGrid->create(null, null, null);

?>

<div class="mostWatchedVideos">
	<h3>Most Watched Videos</h3>
</div>

<?php

$mostWatchedVideos = array();

$mostWatchedQuery = $connect->prepare("SELECT * FROM file_uploads ORDER BY views DESC LIMIT 6");
$mostWatchedQuery->execute();

foreach($mostWatchedQuery->fetchAll() as $row) {
    $video = new Video($connect, $row, $user);
    array_push($mostWatchedVideos, $video);
}

$mostWatchedVideoGrid = new VideoGrid($connect, $user);
echo $mostWatchedVideoGrid->create(null, null, $mostWatchedVideos);


?>

<div class="mostRecentVideos">
	<h3>Recently Uploaded Videos</h3>
</div>

<?php

$mostRecentVideos = array();

$mostRecentQuery = $connect->prepare("SELECT * FROM file_uploads ORDER BY uploadDate DESC LIMIT 6");
$mostRecentQuery->execute();

foreach($mostRecentQuery->fetchAll() as $row) {
    $video = new Video($connect, $row, $user);
    array_push($mostRecentVideos, $video);
}

$mostRecentVideoGrid = new VideoGrid($connect, $user);
echo $mostRecentVideoGrid->create(null, null, $mostRecentVideos);


?>
