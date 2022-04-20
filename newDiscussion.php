<?php require_once("config.php") ?>
<?php require_once("classes/discussionDetailsProvider.php") ?>
<div class="newDiscussion">

</div>
<?php
    $discussionFormProvider = new DiscussionDetails($connect);
    echo $discussionFormProvider -> createDiscussionForm();

?>