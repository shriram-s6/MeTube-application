<?php 
error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
require_once("header.php");
require_once("classes/ProfileData.php"); 

?>
<div class="newDiscussion">

</div>
<?php

    if (isset($_POST["uploadButton"])) {
        if (!isset($_POST["discussionTitle"]) || !isset($_POST["descriptionInput"])) {
            exit();
        }

        $discussionTitle = $_POST["discussionTitle"];
        $descriptionInput = $_POST["descriptionInput"];

        $query = $connect->prepare("INSERT INTO discussion_post (created_by, title, text) VALUES (:created_by, :title, :text)");

        $createdBy = new ProfileData($connect, $_SESSION["userLoggedIn"]);
        $createdByUserName = $createdBy->getUsername();

        $query->bindParam(":created_by", $createdByUserName);
        $query->bindParam(":title", $discussionTitle);
        $query->bindParam(":text", $descriptionInput);
        $query->execute();

        header("Location: profile.php?email=".$createdBy->getEmail());
    }

?>

<form action='' method='POST' enctype='multipart/form-data'>
    <div class='form-group'>
        <input type='text' class='form-control' id='discussionTitle'name='discussionTitle' placeholder='Discussion Title' required></input>
    </div>    
    <div class='form-group'>
        <textarea class='form-control' id='discussionDescription' rows='6' name='descriptionInput' placeholder='Discussion Description' required></textarea>
    </div>
    <button type='submit' class='btn btn-primary' name='uploadButton'>Post</button>   
</form>
