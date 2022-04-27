<link rel="stylesheet" type="text/css" href="css/discussions.css">
<?php
//error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("config.php");
require_once("classes/ProfileData.php");

// getting all the discussion topics

$getTopicsQuery = "SELECT topic_id, title, posted_by, posted_time FROM discussion_topics ORDER BY posted_time DESC";
//echo $getTopicsQuery;
$getTopicsQuery = $connect->prepare($getTopicsQuery);
$getTopicsQuery->execute();

if ($getTopicsQuery->rowCount() == 0) {
    $displayMessage = "<p>No topics exist.</p>";
} else {
    $displayMessage = "
        <table>
        <tr>
            <th>Topic Title</th>
            <th>No of posts</th>
        </tr>";

    foreach ($getTopicsQuery->fetchAll(PDO::FETCH_ASSOC) as $topicInfo) {

        $topicId = $topicInfo["topic_id"];

        $topicTitle = $topicInfo["title"];
        $topicCreatedTime = $topicInfo["posted_time"];
        $topicCreatedBy = $topicInfo["posted_by"];

        // get the number of posts

        $noOfPostsQuery = "SELECT COUNT(id) as no_of_posts FROM discussion_post WHERE topic_id = $topicId";
        $noOfPostsQuery = $connect->prepare($noOfPostsQuery);
        $noOfPostsQuery->execute();

        $noOfPosts = $noOfPostsQuery->fetch(PDO::FETCH_ASSOC)["no_of_posts"];

        $displayMessage .= "
        <tr>
        <td><a href='displayTopic.php?topic_id=$topicId'><strong>$topicTitle</strong></a><br>
        Created on $topicCreatedTime by $topicCreatedBy </td>
        <td>$noOfPosts</td>
        </tr>           
        ";

    }

    $displayMessage .= "</table>";
}
?>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Topics in Discussion Forum</title>
</head>
<body>
<h4>Topics in Discussion Forum</h4>
<?php print $displayMessage?>
<br>
<p>Would you like to <a href="newDiscussion.php">add a topic</a>?</p>
</body>
</html>
