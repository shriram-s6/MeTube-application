<?php require_once("classes/fileDetailsProvider.php") ?>
<?php include("config.php") ?>

<div class="uploadVideos">

</div>
    <?php
        $formProvider = new VideoDetails($connect);
        echo $formProvider -> createUploadForm();

    ?>