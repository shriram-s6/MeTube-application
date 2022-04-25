<?php
error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("classes/VideoGrid.php");
require_once("classes/Video.php");

if ($_GET["term"] == "" || !isset($_GET["term"])) {
	exit();
}

$term = strtolower($_GET["term"]);

$searchTermQuerySQL = "SELECT * FROM search_items WHERE search_term LIKE '%$term%'";
$searchTermQuery = $connect->prepare($searchTermQuerySQL);
$searchTermQuery->execute();

if ($searchTermQuery->rowCount() > 0) {
	$currentCount = $searchTermQuery->fetchAll()[0]["search_count"];
	$newCount = $currentCount + 1;
	if ($newCount <= 17) {
		$searchTermUpdateQuerySQL = "UPDATE search_items SET search_count = $newCount WHERE search_term  LIKE '%$term%'";
		$searchTermUpdateQuery = $connect->prepare($searchTermUpdateQuerySQL);
		$searchTermUpdateQuery->execute();
	}
} else {
	$searchTermInsertQuerySQL = "INSERT INTO search_items (search_term) VALUES ('".$term."')";
	$searchTermInsertQuery = $connect->prepare($searchTermInsertQuerySQL);
	$searchTermInsertQuery->execute();
}

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