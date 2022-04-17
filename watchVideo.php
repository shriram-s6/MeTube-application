<?php
require_once("header.php");
require_once("classes/VideoPlayer.php");
require_once("classes/VideoInfoSection.php");


if(!isset($_GET["id"])) {
    echo "No url passed to play";
    exit();
}

$video = new Video($connect, $_GET["id"], $user);
$video->increaseViewCount();
?>

<script src="javascript/videoPlayerActions.js"></script>

<div class="watchLeftColumn">
<?php
    $videoPlayer = new VideoPlayer($video);
    echo $videoPlayer->create(true);

    $videoPlayer = new VideoInfoSection($connect, $video, $user);
    echo $videoPlayer->create();
?>
</div>

<div class="suggestions">

</div>

<?php require_once("footer.php"); ?>
