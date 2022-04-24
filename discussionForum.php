<?php 
//error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
require_once("classes/ProfileData.php");


?>

<!DOCTYPE html>
<html lang="en-us">
    <head>
        <title>New Discussion</title>
    </head>
    <body>
    <div class='tabs'>
        <ul class='nav nav-tabs' id='myTab' role='tablist' style='flex-direction: row;'>
            <li class='nav-item'>
                <a class='nav-link active' id='new-discussion-tab' data-toggle='tab' href='#new-discussion' role='tab' aria-controls='new-discussion' aria-selected='true'>New Discussion</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link' id='existing-posts-tab' data-toggle='tab' href='#existing-posts' role='tab' aria-controls='existing-posts' aria-selected='false'>Discussions</a>
            </li>
        </ul>
    </div>
    <div class='tab-content' id='myTabContent'>
        <div class='tab-pane fade show active' id='new-discussion' role='tabpanel' aria-labelledby='new-discussion-tab'>
            <h3 style="color: mediumpurple">Too bored out there? Create a new discussion.</h3>
            <?php require_once('newDiscussion.php');?>
        </div>
        <div class='tab-pane fade' id='existing-posts' role='tabpanel' aria-labelledby='existing-posts-tab'>
            <?php

                $profileData = new ProfileData($connect, $_SESSION["userLoggedIn"]);
                $username = $profileData->getUsername();
                $query = $connect->prepare("SELECT * FROM discussion_post WHERE created_by = :created_by");
                $query->bindParam(":created_by", $username);
                $query->execute();

                foreach($query->fetchAll() as $row) {
                    echo "<div class='discussion-post' style='outline: 1px solid black;'>";
                    echo "<h3>".$row["title"]."</h3>";
                    echo "<p>".$row["text"]."</p>";
                    echo "</div>";
                }
            ?>
        </div>
    </div>
    </body>
</html>
