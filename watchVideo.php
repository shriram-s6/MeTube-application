<?php
require_once("header.php");
require_once("classes/VideoPlayer.php");
require_once("classes/VideoInfoSection.php");
require_once("classes/CommentArea.php");

if(!isset($_GET["id"])) {
    echo "No url to play the video";
    exit();
}

$video = new Video($connect, $_GET["id"], $user);
$video->increaseViewCount();
?>

<script src="javascript/videoPlayer.js"></script>

<div class="watchLeftColumn">
<?php
    $videoPlayer = new VideoPlayer($video);
    echo $videoPlayer->create(true);

    $videoPlayer = new VideoInfoSection($connect, $video, $user);
    echo $videoPlayer->create();

    $commentArea = new CommentArea($connect, $video, $user);
    echo $commentArea->create();
?>
</div>

<div class="suggestions">

</div>

