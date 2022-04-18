<?php
require_once("Video.php");

class CommentArea {
    private $connect, $video, $user;
    public function __construct($connect, $video, $user) {
        $this->connect = $connect;
        $this->video = $video;
        $this->user = $user;
    }

    public function create() {
        return $this->createCommentArea();
    }

    private function createCommentArea() {
        $noOfComments = $this->video->getNumberOfComments();
        $commentedBy = $this->user->getUsername();
        $videoId = $this->video->getVideoId();

        $profileButton = ButtonProvider::createUserProfileButton($this->connect, $commentedBy);
        $commentAction = "postComment(this, \"$commentedBy\", $videoId, null, \"comments\")";

        $commentButton = ButtonProvider::createButton("Make Comment", null, $commentAction, "postComment");

        return "<div class='commentSection'>
                    <div class='header'>
                        <span class='commentCount'>$noOfComments comments</span>
                        <div class='commentForm'>
                        
                        </div>
                    </div>

                </div>";
    }

}
