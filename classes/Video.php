<?php

class Video {

    private $conn, $sqlData, $userLoggedInObj;

    public function __construct($conn, $input, $userLoggedInObj) {
        $this->conn = $conn;
        $this->userLoggedInObj = $userLoggedInObj;

        if(is_array($input)) {
            $this->sqlData = $input;
        } else {

            $query = $this->conn->prepare("SELECT * FROM file_uploads WHERE id = :id");
            $query->bindParam(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

    }

    public function getVideoId() {
        return $this->sqlData["id"];
    }

    public function getUploadedBy() {
        return $this->sqlData["uploadedBy"];
    }

    public function getTitle() {
        return $this->sqlData["title"];
    }

    public function getDescription() {
        return $this->sqlData["description"];
    }

    public function getPrivacy() {
        return $this->sqlData["privacy"];
    }

    public function getFilePath() {
        return $this->sqlData["filePath"];
    }

    public function getCategory() {
        return $this->sqlData["category"];
    }

    public function getUploadDate() {
        $date = $this->sqlData["uploadDate"];
        return date("M j, Y", strtotime($date));
    }

    public function getViews() {
        return $this->sqlData["views"];
    }

    public function getDuration() {
        return $this->sqlData["duration"];
    }

    public function getFileSize() {
        return $this->sqlData["fileSize"];
    }

    public function increaseViewCount() {

        $videoId = $this->getVideoId();
        $query = $this->conn->prepare("UPDATE file_uploads SET views=views+1 WHERE id = :id");
        $query->bindParam(":id", $videoId);

        $query->execute();

        $this->sqlData["views"] = $this->sqlData["views"] + 1;
        return $query->rowCount();
    }

    public function getLikes() {
        $videoId = $this->getVideoId();
        $query = $this->conn->prepare("SELECT count(*) as 'count' FROM likes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data["count"];
    }

    public function getDislikes() {
        $videoId = $this->getVideoId();
        $query = $this->conn->prepare("SELECT count(*) as 'count' FROM dislikes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data["count"];
    }

    public function like() {
        $id = $this->getVideoId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasLikedBy()) {
            $query = $this->conn->prepare("DELETE FROM likes WHERE userName=:username AND videoId=:videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result = array(
                "likes" => -1,
                "dislikes" => 0
            );

            return json_encode($result);

        } else {

            $query = $this->conn->prepare("DELETE FROM dislikes WHERE userName=:username AND videoId=:videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->conn->prepare("INSERT INTO likes(userName, videoId) VALUES (:username, :videoId)");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result = array(
                "likes" => 1,
                "dislikes" => 0 - $count
            );

            return json_encode($result);
        }

    }

    public function dislike() {
        $id = $this->getVideoId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasDislikedBy()) {
            $query = $this->conn->prepare("DELETE FROM dislikes WHERE userName=:username AND videoId=:videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result = array(
                "likes" => 0,
                "dislikes" => -1
            );

            return json_encode($result);

        } else {

            $query = $this->conn->prepare("DELETE FROM likes WHERE userName=:username AND videoId=:videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->conn->prepare("INSERT INTO dislikes(userName, videoId) VALUES (:username, :videoId)");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result = array(
                "likes" => 0 - $count,
                "dislikes" => 1
            );

            return json_encode($result);
        }

    }

    public function wasLikedBy() {
        $username = $this->userLoggedInObj->getUsername();
        $id = $this->getVideoId();

        $query = $this->conn->prepare("SELECT * FROM likes WHERE userName=:username AND videoId=:videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $id);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function wasDislikedBy() {
        $username = $this->userLoggedInObj->getUsername();
        $id = $this->getVideoId();

        $query = $this->conn->prepare("SELECT * FROM dislikes WHERE userName=:username AND videoId=:videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $id);

        $query->execute();

        return $query->rowCount() > 0;
    }
}


?>