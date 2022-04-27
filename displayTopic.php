<link rel="stylesheet" type="text/css" href="css/discussions.css">
<?php
error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("config.php");
require_once("classes/ProfileData.php");

if (!$_GET["topic_id"]) {
    header("Location: discussionForum.php");
    exit();
}

// verify the topics
$topicId = $_GET["topic_id"];
$checkTopicsQuery = "SELECT title FROM discussion_topics WHERE topic_id = $topicId";
$checkTopicsQuery = $connect->prepare($checkTopicsQuery);
$checkTopicsQuery->execute();

if ($checkTopicsQuery->rowCount() == 0) {
    $displayMessage = "<p><em>You have selected an invalid topic.</em>
                        Please <a href='discussionForum.php'>try again</a></p>";

} else {
    // get the topic title

    $topicTitle = $checkTopicsQuery->fetch(PDO::FETCH_ASSOC)["title"];

    // get the posts

    $getPostsQuery = "SELECT id, text, posted_time, created_by FROM discussion_post WHERE topic_id = $topicId ORDER BY posted_time";

    $getPostsQuery = $connect->prepare($getPostsQuery);
    $getPostsQuery->execute();

    // creating display string

    $displayMessage = "<p>Topics under <strong>$topicTitle</strong> are:</p> 
    <table>
        <tr>
            <th>Posted By</th>
            <th>Post</th>
        </tr>";


    foreach ($getPostsQuery->fetchAll(PDO::FETCH_ASSOC) as $postInfo) {

        $postedBy = $postInfo["posted_time"];
        $postId = $postInfo["id"];
        $postText = $postInfo["text"];
        $postedTime = $postInfo["posted_time"];
        $postedBy = $postInfo["created_by"];

        $displayMessage .="
        
        <tr>
        <td> $postedBy at $postedTime</td>
        <td>$postText<br><br>
        <a href='postReply.php?post_id=$postId'><strong>Respond to post</strong></a>
        </td>
        </tr>
        ";

    }

    $displayMessage .= "</table>";
}

?>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Posts under the topic</title>
</head>
<body>
<h4>Posts under this topic</h4>
<?php print $displayMessage?>
</body>
</html>

