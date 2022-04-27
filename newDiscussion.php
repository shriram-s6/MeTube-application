<?php 
//error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
require_once("header.php");
require_once("classes/ProfileData.php"); 

?>
<div class="newDiscussion">

</div>

<form action='do_addtopic.php' method='POST' enctype='multipart/form-data'>
    <div class='form-group'>
        <input type='text' class='form-control' id='discussionTitle'name='discussionTitle' placeholder='Discussion Title' required></input>
    </div>
    <div class='form-group'>
        <textarea class='form-control' id='discussionDescription' rows='6' name='descriptionInput' placeholder='Discussion Description' required></textarea>
    </div>
    <button type='submit' class='btn btn-primary' name='uploadButton'>Post</button>
</form>
