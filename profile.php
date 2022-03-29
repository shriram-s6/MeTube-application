<?php require_once("header.php") ?>

<?php require_once("sideNavBar.php"); ?>

<style>
<?php include 'css/profile.css'; ?>
</style>
<!-- <link rel="stylesheet" type="text/css" href="css/profile.css"> -->


<div id="mainSectionContainer">

    <div id="mainContentContainer" style="flex-direction: column;">

        <div class="userContainer">
            <div class="profilePictureContainer">
                <img class="profilePicture" src="images/icons/default_profile_picture.png">
            </div>            
            <div class="username">
                <p>mbcalli</p>
            </div>

        </div>

        <div class="allTabContainer" style="">

            <ul class="nav nav-tabs" id="myTab" role="tablist" style="flex-direction: row;">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="videos-tab" data-toggle="tab" href="#videos" role="tab" aria-controls="videos" aria-selected="false">Videos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="playlists-tab" data-toggle="tab" href="#playlists" role="tab" aria-controls="playlists" aria-selected="false">Playlists</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="channels-tab" data-toggle="tab" href="#channels" role="tab" aria-controls="channels" aria-selected="false">Channels</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="upload-videos-tab" data-toggle="tab" href="#upload-videos" role="tab" aria-controls="upload-videos" aria-selected="false">Upload Videos</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent" style="">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <?php require_once("user.php") ?>
                </div>
                <div class="tab-pane fade" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                    <?php require_once("user_videos.php") ?>
                </div>
                <div class="tab-pane fade" id="playlists" role="tabpanel" aria-labelledby="playlists-tab">
                    <?php require_once("user_playlists.php") ?>
                </div>
                <div class="tab-pane fade" id="channels" role="tabpanel" aria-labelledby="channels-tab">
                    <?php require_once("user_playlists.php") ?>
                </div>
                <div class="tab-pane fade" id="upload-videos" role="tabpanel" aria-labelledby="upload-videos-tab">
                    <?php require_once("upload.php") ?>
                </div>
            </div>

        </div>

<?php require_once("footer.php") ?>