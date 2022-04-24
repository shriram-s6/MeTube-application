<?php 
//error_reporting(E_ERROR | E_PARSE);
require_once("classes/fileDetailsProvider.php"); ?>
<?php include("config.php") ?>

<div class="uploadVideos">

</div>
    <?php
        $formProvider = new VideoDetails($connect);
        echo $formProvider -> createUploadForm();

    ?>
<script>
    $("form").submit(function () {
        $("#loadModal").modal("show");
    });
</script>
<div class="modal fade" id="loadModal" tabindex="-1" aria-labelledby="loadModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                File uploading. Please wait.
                <img src="images/icons/upload-loader.gif">
            </div>

        </div>
    </div>
</div>
