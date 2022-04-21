<h3>Videos</h3>
<?php

require_once("config.php");
require_once("classes/ProfileData.php");
require_once("classes/VideoGrid.php");

$profileData = new ProfileData($connect, $email);
$username = $profileData->getUsername();


$videoGrid = new VideoGrid($connect, $user);
echo $videoGrid->create($username, null, null);
?>
<h3>Images</h3>
<?php
$userImageQuery = $connect->prepare("SELECT * FROM file_uploads WHERE uploadedBy = :username AND fileType = 2");
$userImageQuery->bindParam(":username", $username);
$userImageQuery->execute();

$userImage = array();

foreach($userImageQuery->fetchAll() as $row) {
	$image = new Video($connect, $row, $user);
	array_push($userImage, $image);
}

$pictureGrid = new VideoGrid($connect, $user);
echo $pictureGrid->create(null, null, $userImage);


?>

<h3>Audio</h3>
<?php
$userAudioQuery = $connect->prepare("SELECT * FROM file_uploads WHERE uploadedBy = :username AND fileType = 1");
$userAudioQuery->bindParam(":username", $username);
$userAudioQuery->execute();

$userAudio = array();

foreach($userAudioQuery->fetchAll() as $row) {
	$audio = new Video($connect, $row, $user);
	array_push($userAudio, $audio);
}

$audioGrid = new VideoGrid($connect, $user);
echo $audioGrid->create(null, null, $userAudio);


?>