<?php

require_once("config.php");
require_once("classes/ProfileData.php");
require_once("classes/VideoGrid.php");

$profileData = new ProfileData($connect, $email);
$username = $profileData->getUsername();


$videoGrid = new VideoGrid($connect, $user);
echo $videoGrid->create($username);


?>