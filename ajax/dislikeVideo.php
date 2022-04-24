<?php
error_reporting(E_ERROR | E_PARSE);
require_once("../config.php");
require_once("../classes/Video.php");
require_once("../classes/User.php");

$userName = $_SESSION["userLoggedIn"];
$videoId = $_POST["videoId"];
$user = new User($connect, $userName);

$video = new Video($connect, $videoId, $user);

echo $video->dislike();
