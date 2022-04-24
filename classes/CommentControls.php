<?php
//error_reporting(E_ERROR | E_PARSE);
require_once("ButtonProvider.php");

class CommentControls {
    private $connect, $comment, $user;

    public function __construct($connect, $comment, $user) {

        $this->connect = $connect;
        $this->comment = $comment;
        $this->user = $user;
    }

    public function create(): string
    {

        $replyButton = $this->createReplyButton();
        $likesCount = $this->createLikesCount();
        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();
        $replySection = $this->createReplySection();

        return "<div class='controlsDiv'>
                    $replyButton
                    $likesCount
                    $likeButton
                    $dislikeButton
                </div>
                $replySection";
    }

    private function createReplyButton(): string {
        $text = "Reply";
        $action = "toggleReply(this)";
        return ButtonProvider::createButton($text, null, $action, null);
    }

    public function createLikesCount(): string {
        $text = $this->comment->getLikes();

        if($text == 0) $text = "";
        return "<span class='likesCount'>$text</span>";
    }

    public function createReplySection(): string {

        $commentedBy = $this->user->getUsername();
        $videoId = $this->comment->getVideoId();
        $commentId = $this->comment->getCommentId();

        $query = $this->connect->prepare("SELECT email from users WHERE userName =:username");
        $query->bindParam(":username", $commentedBy);
        $query->execute();

        $sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $email = $sqlData["email"];


        $profileButton = ButtonProvider::createUserProfileButton($this->connect, $email);
        $cancelAction = "toggleReply(this)";
        $cancelButton = ButtonProvider::createButton("Cancel", null, $cancelAction, "cancelComment");

        $postButtonAction = "postComment(this, \"$commentedBy\", $videoId, $commentId, \"repliesSection\")";
        $postButton = ButtonProvider::createButton("Reply", null, $postButtonAction, "postComment");

        return "<div class='commentForm hidden'>
                    $profileButton
                    <textarea class='commentBodyClass' placeholder='Add a comment...'></textarea>
                    $cancelButton
                    $postButton
                </div>";
    }


    private function createLikeButton(): string
    {

        $commentId = $this->comment->getCommentId();
        $videoId = $this->comment->getVideoId();
        $userAction = "likeComment($commentId, this, $videoId)";
        $class = "likeButton";

        $imageSrc = "images/icons/like.png";

        if($this->comment->wasLikedBy()) {
            $imageSrc = "images/icons/like-active.png";
        }


        return ButtonProvider::createButton("", $imageSrc, $userAction, $class);
    }

    private function createDislikeButton(): string
    {
        $commentId = $this->comment->getCommentId();
        $videoId = $this->comment->getVideoId();
        $userAction = "dislikeComment($commentId, this, $videoId)";
        $class = "dislikeButton";

        $imageSrc = "images/icons/dislike.png";

        if($this->comment->wasDislikedBy()) {
            $imageSrc = "images/icons/dislike-active.png";
        }

        return ButtonProvider::createButton("", $imageSrc, $userAction, $class);
    }
}
