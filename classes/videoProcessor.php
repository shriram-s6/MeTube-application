<?php

class VideoProcessor
{

    private $connect;
    private $sizeLimit = 500000000;
    private $allowedVideoFileTypes = array("mp4");
    private $allowedAudioFileTypes = array("mp3");
    private $allowedImageTypes = array("png", "jpg");

    public function __construct($connect)
    {
        $this->connect = $connect;
    }

    public function upload($videoUploadData)
    {


        $videoData = $videoUploadData->videoDataArray;
        $fileType = $videoUploadData->fileTypeInput;

        if ($fileType == 0) {
            $targetDirectory = "uploads/videos/";
        } else if ($fileType == 1) {
            $targetDirectory = "uploads/audios/";
        } else if ($fileType == 2) {
            $targetDirectory = "uploads/images/";
        } else {
            $targetDirectory = "uploads/";
        }


        $tempFilePath = $targetDirectory . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);


        if (!$isValidData) {
            return false;
        }

        if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            $finalFilePath = $targetDirectory . uniqid();
            if ($fileType == 0) {
                $finalFilePath .= ".mp4";
            } else if ($fileType == 1) {
                $finalFilePath .= ".mp3";
            } else if ($fileType == 2) {
                $finalFilePath .= ".png";
            }

            if (!$this->insertFileData($videoUploadData, $finalFilePath, $videoData["size"])) {
                echo "Failed";
                return false;
            }

            if ($fileType == 0) {
                if (!$this->convertVideo($fileType, $tempFilePath, $finalFilePath)) {
                    echo "File upload failed, please check the error and try again";
                    return false;
                }

                if (!$this->deleteFile($tempFilePath)) {
                    return false;
                }

                if (!$this->generateThumbnails($fileType, $finalFilePath)) {
                    return false;
                }
            }

            return true;
        }
        return true;
    }

    private function processData($videoData, $filePath)
    {

        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!$this->validSizeCheck($videoData)) {
            return false;
        } else if (!$this->validTypeCheck($videoType)) {
            return false;
        } else if ($this->containsError($videoData)) {
            return false;
        }

        return true;
    }

    private function validSizeCheck($data)
    {
        return $data["size"] <= $this->sizeLimit;
    }

    private function validTypeCheck($fileType)
    {

        $low_cased = strtolower($fileType);
        if (in_array($low_cased, $this->allowedVideoFileTypes)) {
            return true;
        } elseif (in_array($low_cased, $this->allowedAudioFileTypes)) {
            return true;
        } elseif (in_array($low_cased, $this->allowedImageTypes)) {
            return true;
        }

        return false;
    }

    private function containsError($data)
    {
        return $data["error"] != 0;
    }

    private function insertFileData($uploadedData, $filePath, $fileSize)
    {
        $querySQL = "INSERT INTO file_uploads(uploadedBy,title,description,fileType,privacy,
                         filePath,category,fileSize) VALUES ('$uploadedData->uploadedBy', '$uploadedData->title', '$uploadedData->description', $uploadedData->fileTypeInput, $uploadedData->sharingMode, 
                                                             '$filePath', $uploadedData->fileCategory, $fileSize)";
        
        $query = $this->connect->prepare($querySQL);
        // $query->bindParam(":uploadedBy", $uploadedData->uploadedBy);
        // $query->bindParam(":title", $uploadedData->title);
        // $query->bindParam(":description", $uploadedData->description);
        // $query->bindParam(":fileType", $uploadedData->fileTypeInput);
        // $query->bindParam(":privacy", $uploadedData->sharingMode);
        // $query->bindParam(":filePath", $filePath);
        // $query->bindParam(":category", $uploadedData->fileCategory);
        // $query->bindParam(":fileSize", $fileSize);

        return $query->execute();
    }

    public function convertVideo($fileType, $tempFilePath, $finalFilePath)
    {


        $ffmpegPath = "ffmpeg/mac/regular-xampp/ffmpeg";
        $cmd = "$ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        if ($returnCode != 0) {
            foreach ($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;


    }

    private function deleteFile($filePath)
    {

        if (!unlink($filePath)) {
            return false;
        }
        return true;
    }

    public function generateThumbnails($fileType, $filePath)
    {
        $thumbnailSize = "210x118";
        $numThumbnails = 3;
        $pathToThumbnail = "uploads/videos/thumbnails/";

        $videoDuration = $this->getVideoDuration($fileType, $filePath);
        $videoId = $this->connect->lastInsertId();
        $this->updateVideoDuration($videoDuration, $videoId);

        for ($num = 1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            $interval = ($videoDuration * 0.8) / $numThumbnails * $num;

            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";

            $ffmpegPath = "ffmpeg/mac/regular-xampp/ffmpeg";
            $cmd = "$ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

            $outputLog = array();
            exec($cmd, $outputLog, $returnCode);

            if ($returnCode != 0) {
                foreach ($outputLog as $line) {
                    echo $line . "<br>";
                }
            }

            $pickedThumbnail = $num == 1 ? 1 : 0;

            $query = $this->connect->prepare("INSERT INTO video_thumbnails(videoId, filePath, pickedThumbnail)                                                
                                              VALUES (:videoId, :filePath, :picked)");

            $query->bindParam(":videoId", $videoId);
            $query->bindParam(":filePath", $fullThumbnailPath);
            $query->bindParam(":picked", $pickedThumbnail);

            $success = $query->execute();

            if(!$success) {
                return false;
            }
        }

        return true;

    }

    private function getVideoDuration($fileType, $filePath)
    {
        if ($fileType == 0) {
            $ffprobePath = "ffmpeg/mac/regular-xampp/ffprobe";
            return shell_exec("$ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
        }
    }

    private function updateVideoDuration($duration, $videoId)
    {
        $duration = (int)$duration;
        $hours = floor($duration / 3600);
        $minutes = floor(($duration - ($hours * 3600)) / 60);
        $seconds = floor($duration % 60);

        if ($hours < 10) {
            $hours = "0" . $hours;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }
        if ($seconds < 10) {
            $seconds = "0" . $seconds;
        }

        $duration = $hours.":".$minutes.":".$seconds;

        $query = $this->connect->prepare("UPDATE file_uploads SET duration=:duration WHERE id=:videoId");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoId", $videoId);
        $query->execute();
    }

}