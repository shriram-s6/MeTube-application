<?php require_once("header.php");
require_once("classes/videoUploadData.php");
require_once("classes/videoProcessor.php");
require_once("config.php");

if (!isset($_POST["uploadButton"])) {
    echo "No file submitted";
    exit();
}

$videoUploadData = new VideoUploadData($_FILES["fileInput"],
                                        $_POST["fileTypeInput"],
                                        $_POST["titleInput"],
                                        $_POST["descriptionInput"],
                                        $_POST["privacyInput"],
                                        $_POST["commentInput"],
                                        $_POST["categoryInput"],
                                        $user->getUsername());

$videoProcessor = new VideoProcessor($connect);
$isSuccessful = $videoProcessor->upload($videoUploadData);

if($isSuccessful) {
    echo "Video is uploaded successfully. You can go to the homepage to watch the video.";
}