<h3>Videos</h3>
<?php
error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
require_once("classes/ProfileData.php");
require_once("classes/VideoGrid.php");
require_once("classes/User.php");

$profileData = new ProfileData($connect, $email);
$user = new User($connect, $email);
$username = $profileData->getUsername();


$videoGrid = new VideoGrid($connect, $user);
echo $videoGrid->create($username, null, null);
?>
<h3>Images</h3>
<?php
$userImageQueryString = "SELECT * FROM file_uploads WHERE uploadedBy = '".$username."' AND fileType = 2";
$userImageQuery = $connect->prepare($userImageQueryString);
$userImageQuery->execute();

$userImage = array();

foreach($userImageQuery->fetchAll() as $row) {
	$image = new Video($connect, $row, $user);
	array_push($userImage, $image);
}

if (count($userImage) != 0) {
	$pictureGrid = new VideoGrid($connect, $user);
	echo $pictureGrid->create(null, null, $userImage);
}




?>

<h3>Audio</h3>
<?php
$userAudioQueryString = "SELECT * FROM file_uploads WHERE uploadedBy = '".$username."' AND fileType = 1";
$userAudioQuery = $connect->prepare($userAudioQueryString);
$userAudioQuery->execute();

$userAudio = array();

foreach($userAudioQuery->fetchAll() as $row) {
	$audio = new Video($connect, $row, $user);
	array_push($userAudio, $audio);
}

if (count($userAudio) != 0) {
	$audioGrid = new VideoGrid($connect, $user);
	echo $audioGrid->create(null, null, $userAudio);
}

?>