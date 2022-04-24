<?php
error_reporting(E_ERROR | E_PARSE);
require_once("../config.php");
require_once("../classes/User.php");
require_once("../classes/Comments.php");

if(isset($_POST["commentText"]) && isset($_POST["postedBy"]) && isset($_POST["videoId"])) {


    $userLoggedInObj = new User($connect, $_SESSION["userLoggedIn"]);

    $commentedBy = $_POST["postedBy"];
    $videoId = $_POST["videoId"];
    $responseTo = 0;
    if (isset($_POST["responseTo"]) && $_POST["responseTo"] != "") {
        $responseTo = $_POST["responseTo"];
    }
    $commentText = trim($_POST["commentText"]);

    $querySQL = "INSERT INTO user_comments (commentedBy, videoId, respondedTo, comment) VALUES ('$commentedBy',$videoId,$responseTo,'$commentText')";
    $query = $connect->prepare($querySQL);
    // $query->bindParam(":postedBy", $commentedBy);
    // $query->bindParam(":videoId", $videoId);
    // $query->bindParam(":responseTo", $responseTo);
    // $query->bindParam(":comment", $commentText);

    $query->execute();

    $newComment = new Comments($connect, $connect->lastInsertId(), $userLoggedInObj, $videoId);
    echo $newComment->create();

} else {
    echo "check the parameters";
}