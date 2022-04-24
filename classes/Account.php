<?php
error_reporting(E_ERROR | E_PARSE);

class Account {

    private $connect;
    private $errorArray = array();
    public function __construct($connect) {
        $this->connect = $connect;
    }

    public function login($email, $password) {
        $password = hash("sha512", $password);

        $query = $this->connect->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);
        $query->execute();

        if($query->rowCount() == 1) {
            echo "user in database";
            return true;
        } else {
            echo "user NOT in database";
            $this->errorArray[] = Constants::$loginFailed;
            return false;
        }
    }

    public function register($firstName, $lastName, $userName, $email, $password) {
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateUserName($userName);
        $this->validateEmail($email);
        $this->validatePassword($password);

        if(empty($this->errorArray)) {
            return $this->insertUserDetails($firstName, $lastName, $userName, $email, $password);
        } else {
            return false;
        }

    }

    public function update($oldUsername, $firstName, $lastName, $userName, $email, $password, $profilePic) {
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateUserNameForEdit($userName);
        $this->validateEmailForEdit($email);
        $this->validatePassword($password);

        if(empty($this->errorArray)) {
            return $this->editUserDetails($oldUsername, $firstName, $lastName, $userName, $email, $password, $profilePic);
        } else {
            return false;
        }

    }

    public function insertUserDetails($firstName, $lastName, $userName, $email, $password) {
        $password = hash("sha512", $password);
        $profilePic = "images/icons/default_profile_picture.png";

        $query = $this->connect->prepare("INSERT INTO users (firstName,lastName,userName,email,password,profilePic) 
                                            VALUES(:firstName, :lastName, :userName, :email, :password, :profPic)");

        $query->bindParam(":firstName", $firstName);
        $query->bindParam(":lastName", $lastName);
        $query->bindParam(":userName", $userName);
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $password);

        $query->bindParam(":profPic", $profilePic);

        return $query->execute();
    }

    public function editUserDetails($oldEmail, $firstName, $lastName, $userName, $email, $password, $profilePic) {
        $password = hash("sha512", $password);

        if ($profilePic == null) {
            $query = $this->connect->prepare("UPDATE users SET firstName = :firstName, lastName = :lastName, userName = :userName, email = :email, password = :password WHERE email = :oldEmail");
        } else {
            $query = $this->connect->prepare("UPDATE users SET firstName = :firstName, lastName = :lastName, userName = :userName, email = :email, password = :password, profileImage='$profilePic' WHERE email = :oldEmail");
        }
            
        $query->bindParam(":firstName", $firstName);
        $query->bindParam(":lastName", $lastName);
        $query->bindParam(":userName", $userName);
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $password);
        $query->bindParam(":oldEmail", $oldEmail);

        return $query->execute();
    }

    private function validateFirstName($firstName) {
        if(strlen($firstName) <2) {
            $this->errorArray[] = Constants::$firstNameTooShort;
        } elseif (strlen($firstName) > 25) {
            $this->errorArray[] = Constants::$firstNameTooLong;
        }
    }

    private function validateLastName($lastName) {
        if(strlen($lastName) <2) {
            $this->errorArray[] = Constants::$LastNameTooShort;
        } elseif (strlen($lastName) > 25) {
            $this->errorArray[] = Constants::$LastNameTooLong;
        }
    }

    private function validateUserName($userName) {
        if(strlen($userName) <5) {
            $this->errorArray[] = Constants::$UserNameTooShort;
            return;
        } elseif (strlen($userName) > 25) {
            $this->errorArray[] = Constants::$UserNameTooLong;
            return;
        }

        $query = $this->connect->prepare("SELECT username FROM users WHERE username=:userName");
        $query->bindParam(":userName", $userName);
        $query->execute();

        if($query->rowCount() != 0) {
            $this->errorArray[] = Constants::$UserNameExists;
        }
    }

    private function validateEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorArray[] = Constants::$invalidEmail;
            return;
        }

        $query = $this->connect->prepare("SELECT email FROM users WHERE email=:email");
        $query->bindParam(":email", $email);
        $query->execute();

        if($query->rowCount() != 0) {
            $this->errorArray[] = Constants::$emailExists;
        }
    }

    private function validatePassword($password) {
        if(preg_match("/[^A-Za-z0-9]/", $password)) {
            $this->errorArray[] = Constants::$passwordNotAlphaNumeric;
        }elseif(strlen($password) <5) {
            $this->errorArray[] = Constants::$passwordTooShort;
        } elseif (strlen($password) > 25) {
            $this->errorArray[] = Constants::$passwordTooLong;
        }
    }

    public function getError($error) {
        if(in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }



    private function validateUserNameForEdit($userName) {
        if(strlen($userName) <5) {
            $this->errorArray[] = Constants::$UserNameTooShort;
            return;
        } elseif (strlen($userName) > 25) {
            $this->errorArray[] = Constants::$UserNameTooLong;
            return;
        }
    }

    private function validateEmailForEdit($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorArray[] = Constants::$invalidEmail;
            return;
        }
    }
}
