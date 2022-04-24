<?php
error_reporting(E_ERROR | E_PARSE);
require_once("classes/ButtonProvider.php");
class VideoInfoControls {
    private $video, $user;
    public function __construct($video, $user) {

        $this->video = $video;
        $this->user = $user;
    }

    public function create() {
        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();

        return "<div class='controlsDiv'>
                $likeButton
                $dislikeButton
                </div>";
    }

    private function createLikeButton() {
        $text = $this->video->getLikes();
        $videoId = $this->video->getVideoId();
        $userAction = "likeVideo(this, $videoId)";
        $class = "likeButton";

        $imageSrc = "images/icons/like.png";

        if($this->video->wasLikedBy()) {
            $imageSrc = "images/icons/like-active.png";
        }


        return ButtonProvider::createButton($text, $imageSrc, $userAction, $class);
    }

    private function createDislikeButton() {
        $text = $this->video->getDislikes();
        $videoId = $this->video->getVideoId();
        $userAction = "dislikeVideo(this, $videoId)";
        $class = "dislikeButton";

        $imageSrc = "images/icons/dislike.png";

        if($this->video->wasDislikedBy()) {
            $imageSrc = "images/icons/dislike-active.png";
        }

        return ButtonProvider::createButton($text, $imageSrc, $userAction, $class);
    }
}
