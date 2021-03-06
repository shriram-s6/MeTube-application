<?php
//error_reporting(E_ERROR | E_PARSE);
class VideoPlayer {
    private $video;
    public function __construct($video) {
        $this->video = $video;
    }

    public function create($autoPlay) {
        if($autoPlay) {
            $autoPlay = "autoplay";
        } else {
            $autoPlay = "";
        }

        $filePath = $this->video->getFilePath();
        return "<video class='videoPlayer' controls $autoPlay>
                    <source src='$filePath' type='video/mp4'>
                    Your browser does not support video
                </video>";
    }

    public function createImage() {

        $filePath = $this->video->getFilePath();
        return "<image class='videoPlayer' src='$filePath'/>";
    }

    
}
