<?php
error_reporting(E_ERROR | E_PARSE);
require_once("../config.php");
require_once("../classes/Comments.php");
require_once("../classes/User.php");

$userName = $_SESSION["userLoggedIn"];
$videoId = $_POST["videoId"];
$commentId = $_POST["commentId"];

$user = new User($connect, $userName);

$comment = new Video($connect, $commentId, $user, $videoId);

echo $comment->dislike();
