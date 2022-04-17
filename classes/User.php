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
        return isset($this->sqlData["profilePic"]) ? $this->sqlData["profilePic"] : 'images/icons/default_profile_picture.png';
    }

    public function getSignUpDate() {
        return $this->sqlData["signUpDate"];
    }

    public function getSubscriberCount() {
        $query = $this->conn->prepare("SELECT * FROM subscribers WHERE userTo = :userTo");
        $query->bindParam(":userTo", $this->getUsername());
        $query->execute();
        return $query->rowCount();
    }
}


?>