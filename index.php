<?php require_once("header.php"); ?>
<?php require_once("config.php"); ?>
<?php require_once("footer.php"); ?>
<?php require_once("classes/VideoGrid.php"); ?>
<link rel="stylesheet" type="text/css" href="css/videoGrid.css">
<h3 style='text-align: center;'>MeTube</h3>
<br>
<?php 


error_reporting(E_ERROR | E_PARSE);


?>
<span> Sort or filter </span>
<br>
<div class="filters" style="display: flex;">
    
    <form action='' method='POST'>
        <label for='filter'>Sort By:</label>
        <select id='filterType' name='filterType'>
            <option value='--'>--</option>
            <option value='fileSize'>File Size</option>
            <option value='uploadTime'>Upload Time</option>
            <option value='name'>Name</option>
        </select>
        <input type='submit' name='submitFilterBy' value='Sort' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
    </form>

    <form action='' method='POST'>
        <label for='categoryFilter'>Category:</label>
        <select id='categoryFilterType' name='categoryFilterType'>
            <option value='--'>--</option>
            <option value='audioFilter'>Audios</option>
            <option value='videoFilter'>Videos</option>
            <option value='imageFilter'>Images</option>
        </select>
        <input type='submit' name='submitCategoryFilter' value='Browse' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
    </form>
</div>

<?php

if(isset($_POST["submitFilterBy"]) || isset($_POST["categoryFilterType"])) {
    $filterBy = $_POST["filterType"];
    $categoryFilterBy = $_POST["categoryFilterType"];

    if ($filterBy == 'fileSize') {
        $querySQL = "SELECT * FROM file_uploads ORDER BY fileSize LIMIT 18";
        $query = $connect->prepare($querySQL);
        $query->execute();

        $videos = array();

        foreach($query->fetchAll() as $row) {
            $video = new Video($connect, $row, $_SESSION["userLoggedIn"]);
            array_push($videos, $video);
        }

        $videoGrid = new VideoGrid($connect, $user);
        echo $videoGrid->create(null, null, $videos);

    } elseif ($filterBy == 'uploadTime') {
        $querySQL = "SELECT * FROM file_uploads ORDER BY uploadDate LIMIT 18";
        $query = $connect->prepare($querySQL);
        $query->execute();

        $videos = array();

        foreach($query->fetchAll() as $row) {
            $video = new Video($connect, $row, $_SESSION["userLoggedIn"]);
            array_push($videos, $video);
        }

        $videoGrid = new VideoGrid($connect, $user);
        echo $videoGrid->create(null, null, $videos);
    } elseif ($filterBy == 'name') {
        $querySQL = "SELECT * FROM file_uploads ORDER BY title LIMIT 18";
        $query = $connect->prepare($querySQL);
        $query->execute();

        $videos = array();

        foreach($query->fetchAll() as $row) {
            $video = new Video($connect, $row, $_SESSION["userLoggedIn"]);
            array_push($videos, $video);
        }

        $videoGrid = new VideoGrid($connect, $user);
        echo $videoGrid->create(null, null, $videos);
    } elseif ($categoryFilterBy == 'imageFilter' AND $filterBy == NULL) {

        $allImageQueryString = "SELECT * FROM file_uploads WHERE  fileType = 2";
        $allImageQuery = $connect->prepare($allImageQueryString);
        $allImageQuery->execute();

        $allImages = array();

        foreach($allImageQuery->fetchAll() as $row) {
            $image = new Video($connect, $row, $user);
            array_push($allImages, $image);
        }

        if (count($allImages) != 0) {
            $pictureGrid = new VideoGrid($connect, $user);
            echo $pictureGrid->create(null, null, $allImages);
        }

    } elseif ($categoryFilterBy == 'videoFilter' AND $filterBy == NULL) {

        $allVideoQueryString = "SELECT * FROM file_uploads WHERE  fileType = 0";
        $allVideoQuery = $connect->prepare($allVideoQueryString);
        $allVideoQuery->execute();

        $allVideos = array();

        foreach($allVideoQuery->fetchAll() as $row) {
            $image = new Video($connect, $row, $user);
            array_push($allVideos, $image);
        }

        if (count($allVideos) != 0) {
            $pictureGrid = new VideoGrid($connect, $user);
            echo $pictureGrid->create(null, null, $allVideos);
        }

    } elseif ($categoryFilterBy == 'audioFilter' AND $filterBy == NULL) {

        $allAudioQueryString = "SELECT * FROM file_uploads WHERE  fileType = 1";
        $allAudioQuery = $connect->prepare($allAudioQueryString);
        $allAudioQuery->execute();

        $allAudios = array();

        foreach($allAudioQuery->fetchAll() as $row) {
            $image = new Video($connect, $row, $user);
            array_push($allAudios, $image);
        }

        if (count($allAudios) != 0) {
            $pictureGrid = new VideoGrid($connect, $user);
            echo $pictureGrid->create(null, null, $allAudios);
        }

    } else {
        $videoGrid = new VideoGrid($connect, $user);
        echo $videoGrid->create(null, null, null);
    }
} else {
    $videoGrid = new VideoGrid($connect, $user);
    echo $videoGrid->create(null, null, null);
}

?>

<div class="mostWatchedVideos">
	<h3>Most Viewed Media</h3>
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
	<h3>Recently Uploaded Media</h3>
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
