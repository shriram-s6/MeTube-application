<?php
class VideoUploadData {

    public $videoDataArray, $fileTypeInput, $title, $description, $sharingMode, $commentsAllowed, $fileCategory, $uploadedBy;

    public function __construct($videoDataArray, $fileTypeInput, $title, $description, $sharingMode, $commentsAllowed, $fileCategory, $uploadedBy) {
        $this->videoDataArray = $videoDataArray;
        $this->fileTypeInput = $fileTypeInput;
        $this->title = $title;
        $this->description = $description;
        $this->sharingMode = $sharingMode;
        $this->commentsAllowed = $commentsAllowed;
        $this->fileCategory = $fileCategory;
        $this->uploadedBy = $uploadedBy;
    }

}