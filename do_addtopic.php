<?php
//error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("config.php");
require_once("classes/ProfileData.php");


if (isset($_POST["uploadButton"])) {
    if (!isset($_POST["discussionTitle"]) || !isset($_POST["descriptionInput"])) {
        header("Location: newDiscussion.php");
        exit();
    }

    $discussionTitle = $_POST["discussionTitle"];
    $descriptionInput = $_POST["descriptionInput"];

    $query = $connect->prepare("INSERT INTO discussion_topics (posted_by, title) VALUES (:created_by, :title)");

    $createdBy = new ProfileData($connect, $_SESSION["userLoggedIn"]);
    $createdByUserName = $createdBy->getUsername();


    $query->bindParam(":created_by", $createdByUserName);
    $query->bindParam(":title", $discussionTitle);
    $query->execute();

    // select the last posted topic id by the user

    $query = $connect->prepare("SELECT topic_id FROM discussion_topics WHERE posted_by=:created_by ORDER BY posted_time DESC LIMIT 1");
    $query->bindParam(":created_by", $createdByUserName);
    $query->execute();

    $topicId = $query->fetch(PDO::FETCH_ASSOC)["topic_id"];

    // adding post

    $querySQL = "INSERT INTO discussion_post (topic_id, created_by, title, text) VALUES ($topicId, '$createdByUserName', '$discussionTitle', '$descriptionInput')";

    $query = $connect->prepare($querySQL);
    $query->execute();

    $message = "<p>The discussion topic <strong style='color: red'>$discussionTitle</strong> has been created.</p>";
}
?>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>New Discussion</title>
</head>
<body>
    <h4>New Topic Added</h4>
    <?php print $message?>
    To view all the discussions <a href="discussionForum.php">Click here</a>
</body>
</html>
