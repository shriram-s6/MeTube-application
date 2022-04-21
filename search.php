<?php

require_once("header.php");
require_once("classes/VideoGrid.php");
require_once("classes/Video.php");

if ($_GET["term"] == "" || !isset($_GET["term"])) {
	exit();
}

$term = strtolower($_GET["term"]);

$sql = "SELECT * FROM file_uploads WHERE title LIKE '%$term%' OR description LIKE '%$term%'";
$query = $connect->prepare($sql);
$query->execute();

$videos = array();

foreach($query->fetchAll() as $row) {
	$video = new Video($connect, $row, $user);
	array_push($videos, $video);
}

$videosGrid = new VideoGrid($connect, $user);
echo $videosGrid->create(null, null, $videos);

?>