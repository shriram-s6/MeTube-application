<?php 
//error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("classes/videoUploadData.php");
require_once("classes/videoProcessor.php");
require_once("config.php");

echo "Post value: ".$_POST["uploadMediaButton"];

if (!isset($_POST["uploadMediaButton"])) {
    echo "No file submitted";
    // exit();
}

foreach ($_FILES["fileInput"] as $value) {
    echo $value."<br>";
}

echo "<br>";

$videoUploadData = new VideoUploadData($_FILES["fileInput"],
                                        $_POST["fileTypeInput"],
                                        $_POST["titleInput"],
                                        $_POST["descriptionInput"],
                                        $_POST["privacyInput"],
                                        $_POST["commentInput"],
                                        $_POST["categoryInput"],
                                        $user->getUsername());

foreach ($videoUploadData->videoDataArray as $value) {
    echo $value;
}

$videoProcessor = new VideoProcessor($connect);
$isSuccessful = $videoProcessor->upload($videoUploadData);

if($isSuccessful) {
    header("Location: index.php");
}