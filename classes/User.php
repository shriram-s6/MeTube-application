<?php

class User {

    private $conn, $sqlData;

    public function __construct($conn, $email) {
        $this->conn = $conn;

        $query = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(":email", $email);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function isLoggedIn() {
        return isset($_SESSION["userLoggedIn"]);
    }
    
    public function getUsername() {
        return $this->sqlData["userName"];
    }

    public function getFullName() {
        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
    }

    public function getFirstName() {
        return $this->sqlData["firstName"];
    }

    public function getLastName() {
        return $this->sqlData["lastName"];
    }

    public function getEmail() {
        return $this->sqlData["email"];
    }

    public function getProfilePic() {
        // return "<img ng-src='data:image/jpg;charset=utf8;base64,.".base64_encode($row['image'])."' /> ";
        if ($this->sqlData["profileImage"] == NULL) {
            return "<img src='".$this->sqlData["profilePic"]."' /> ";
        } else {
            return "<img src='data:image/jpg;charset=utf8;base64,".base64_encode($this->sqlData['profileImage'])."' style='width: 75px;height: 75px;border: 2px solid black;' />";
        }
    }

    public function getSignUpDate() {
        return $this->sqlData["signUpDate"];
    }

    public function isSubscribedTo($subscribedTo) {

        $username = $this->getUsername();
        $query = $this->conn->prepare("SELECT * FROM subscribers WHERE subscribedTo =:subscribedTo AND subscribedFrom=:subscribedFrom");
        $query->bindParam(":subscribedTo", $subscribedTo);
        $query->bindParam(":subscribedFrom", $username);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function getSubscriberCount() {

        $username = $this->getUsername();
        $query = $this->conn->prepare("SELECT * FROM subscribers WHERE subscribedTo =:subscribedTo");
        $query->bindParam(":subscribedTo", $username);

        $query->execute();

        return $query->rowCount();
    }

    public function getPassword() {
        return $this->sqlData["password"];
    }
}


?>