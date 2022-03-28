<?php
class VideoProcessor {

    private $connect;
    private $sizeLimit = 500000000;
    private $allowedVideoFileTypes = array("mp4");
    private $allowedAudioFileTypes = array("mp3");
    private $allowedImageTypes = array("png", "jpg");

    public function __construct($connect) {
        $this->connect = $connect;
    }

    public function upload($videoUploadData) {


        $videoData = $videoUploadData->videoDataArray;
        $fileType = $videoUploadData->fileTypeInput;

        if($fileType == 0) {
            $targetDirectory = "uploads/videos/";
        } else if($fileType == 1) {
            $targetDirectory = "uploads/audios/";
        } else if($fileType == 2) {
            $targetDirectory = "uploads/images/";
        } else {
            $targetDirectory = "uploads/";
        }


        $tempFilePath = $targetDirectory . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);

        if(!$isValidData) {
            return false;
        }

        if(move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            $finalFilePath = $targetDirectory . uniqid();
            if($fileType == 0) {
                $finalFilePath .= ".mp4";
            } else if($fileType == 1) {
                $finalFilePath .= ".mp3";
            } else if($fileType == 2) {
                $finalFilePath .= ".png";
            }

            if(!$this->insertFileData($videoUploadData, $finalFilePath, $videoData["size"])) {
                return false;
            }

            if(!$this->deleteFile($tempFilePath)) {
                return false;
            }
        }
        return true;
    }

    private function processData($videoData, $filePath) {

        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if(!$this->validSizeCheck($videoData)) {
            return false;
        } else if(!$this->validTypeCheck($videoType)) {
            return false;
        } else if($this->containsError($videoData)) {
            return false;
        }

        return true;
    }

    private function validSizeCheck($data) {
        return $data["size"] <= $this->sizeLimit;
    }

    private function validTypeCheck($fileType) {

        $low_cased = strtolower($fileType);
        if(in_array($low_cased, $this->allowedVideoFileTypes)) {
            return true;
        } elseif (in_array($low_cased, $this->allowedAudioFileTypes)) {
            return true;
        } elseif (in_array($low_cased, $this->allowedImageTypes)) {
            return true;
        }

        return false;
    }

    private function containsError($data) {
        return $data["error"] != 0;
    }

    private function insertFileData($uploadedData, $filePath, $fileSize) {
        $query = $this->connect->prepare("INSERT INTO file_uploads(uploadedBy,title,description,fileType,privacy,
                         filePath,category,fileSize) VALUES (:uploadedBy, :title, :description, :fileType, :privacy, 
                                                             :filePath, :category, :fileSize)");
        $query->bindParam(":uploadedBy", $uploadedData->uploadedBy);
        $query->bindParam(":title", $uploadedData->title);
        $query->bindParam(":description", $uploadedData->description);
        $query->bindParam(":fileType", $uploadedData->fileTypeInput);
        $query->bindParam(":privacy", $uploadedData->sharingMode);
        $query->bindParam(":filePath", $filePath);
        $query->bindParam(":category", $uploadedData->fileCategory);
        $query->bindParam(":fileSize", $fileSize);

        return $query->execute();
    }

    private function deleteFile($filePath) {

        if(!unlink($filePath)) {
            return false;
        }
        return true;
    }

    public function generateThumbnails() {
        $thumbnailSize = "210x118";
        $numThumbnails = 3;
        $pathToThumbnail = "uploads/videos/thumbnails/";
    }

}