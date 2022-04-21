<?php
require_once("ButtonProvider.php");
require_once("CommentControls.php");

class Comments {

    private $connect, $input, $userLoggedInObj, $videoId;

    public function __construct($connect, $input, $userLoggedInObj, $videoId) {
        if(!is_array($input)) {
            $query = $connect->prepare("SELECT * FROM user_comments where commentId =:commentId");
            $query->bindParam(":commentId", $input);
            $query->execute();

            $input = $query->fetch(PDO::FETCH_ASSOC);
        }

        $this->sqlData = $input;
        $this->connect = $connect;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->videoId = $videoId;
    }

    public function create() {

        $id = $this->sqlData["commentId"];
        $comment = $this->sqlData["comment"];
        $commentedBy = $this->sqlData["commentedBy"];
        $videoId = $this->getVideoId();

        $query = $this->connect->prepare("SELECT email from users WHERE userName =:username");
        $query->bindParam(":username", $commentedBy);
        $query->execute();

        $sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $email = $sqlData["email"];

        $timespan = $this->time_elapsed_string($this->sqlData["commentedOn"]);

        $commentsControlObj = new CommentControls($this->connect, $this, $this->userLoggedInObj);
        $commentsControls = $commentsControlObj->create();

        $noOfResponses = $this->getNumberOfReplies();


        if($noOfResponses > 0) {
            $viewRepliesText = "<span class='repliesSection viewReplies' onclick='getReplies($id, this, $videoId)'>View all $noOfResponses replies</span>";
        } else {
            $viewRepliesText = "<div class='repliesSection'></div>";
        }

        $profileButton = ButtonProvider::createUserProfileButton($this->connect, $email);

        return "<div class='itemContainer'>
                    <div class='comment'>
                        $profileButton
                        <div class='mainContainer'>
                            <div class='commentHeader'>
                                <a href='profile.php?email=$email'>
                                    <div>$commentedBy</span></a>
                            </div>
                            <div class='body' style='font-size: 18px;'>
                                $comment
                            </div>
                            <div><span class='timestamp' style='font-size: 12px;'>$timespan</span></div>
                        </div>
                       
                    </div>
                    $commentsControls
                    $viewRepliesText
                </div>";
    }

    public function getNumberOfReplies() {

        $id = $this->sqlData["commentId"];
        $query = $this->connect->prepare("SELECT COUNT(*) FROM user_comments WHERE respondedTo =:responseTo");
        $query->bindParam(":responseTo", $id);

        $query->execute();

        return $query->fetchColumn();
    }

    public function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }


    public function getCommentId() {
        return $this->sqlData["commentId"];
    }

    public function getVideoId() {
        return $this->videoId;
    }

    public function wasLikedBy() {
        $username = $this->userLoggedInObj->getUsername();
        $commentId = $this->getVideoId();

        $query = $this->connect->prepare("SELECT * FROM likes WHERE userName=:username AND commentId=:commentId");
        $query->bindParam(":username", $username);
        $query->bindParam(":commentId", $commentId);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function wasDislikedBy() {
        $username = $this->userLoggedInObj->getUsername();
        $commentId = $this->getVideoId();

        $query = $this->connect->prepare("SELECT * FROM dislikes WHERE userName=:username AND commentId=:commentId");
        $query->bindParam(":username", $username);
        $query->bindParam(":commentId", $commentId);

        $query->execute();

        return $query->rowCount() > 0;
    }


    public function getLikes() {

        $commentId= $this->getCommentId();
        $query = $this->connect->prepare("SELECT COUNT(*) AS 'likes_count' FROM likes WHERE commentId =:commentId");
        $query->bindParam(":commentId", $commentId);

        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        $noOfLikes = $data["likes_count"];


        $query = $this->connect->prepare("SELECT COUNT(*) AS 'dislikes_count' FROM dislikes WHERE commentId =:commentId");
        $query->bindParam(":commentId", $commentId);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        $noOfDislikes = $data["dislikes_count"];

        return $noOfLikes - $noOfDislikes;
    }

    public function like() {
        $commentId = $this->getCommentId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasLikedBy()) {
            $query = $this->connect->prepare("DELETE FROM likes WHERE userName=:username AND commentId=:commentId");
            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $commentId);
            $query->execute();

            return -1;

        } else {

            $query = $this->connect->prepare("DELETE FROM dislikes WHERE userName=:username AND commentId=:commentId");
            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $commentId);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->connect->prepare("INSERT INTO likes(userName, commentId) VALUES (:username, :commentId)");
            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $commentId);
            $query->execute();

            return 1 + $count;
        }

    }

    public function dislike() {
        $commentId = $this->getCommentId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasDislikedBy()) {
            $query = $this->connect->prepare("DELETE FROM dislikes WHERE userName=:username AND commentId=:commentId");
            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $commentId);
            $query->execute();

            return 1;

        } else {

            $query = $this->connect->prepare("DELETE FROM likes WHERE userName=:username AND commentId=:commentId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $commentId);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->connect->prepare("INSERT INTO dislikes(userName, commentId) VALUES (:username, :commentId)");
            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $commentId);
            $query->execute();

            return -1 - $count;
        }

    }
}