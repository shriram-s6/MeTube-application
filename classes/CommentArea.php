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

        $query = $this->connect->prepare("SELECT email from users WHERE userName =:username");
        $query->bindParam(":username", $commentedBy);
        $query->execute();

        $sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $email = $sqlData["email"];


        $profileButton = ButtonProvider::createUserProfileButton($this->connect, $email);
        $commentAction = "postComment(this, \"$commentedBy\", $videoId, null, \"comments\")";

        $commentButton = ButtonProvider::createButton("Post", null, $commentAction, "postComment");

        $comments = $this->video->getComments();
        $commentItems = "";
        foreach ($comments as $comment) {
            $commentItems .= $comment->create();
        }

        return "<div class='commentSection'>
                    <div class='header'>
                        <span class='commentCount'>$noOfComments comments</span>
                        <div class='commentForm'>
                            $profileButton
                            <textarea class='commentBodyClass' placeholder='Add a comment...'></textarea>
                            $commentButton
                        </div>
                    </div>
                    
                    <div class='comments'>
                        $commentItems
                    </div>
                    
                </div>";
    }

}
