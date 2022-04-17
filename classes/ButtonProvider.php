<?php
class ButtonProvider {

    public static $signInFunction = "notSignedIn()";

    public static function createLink($link) {
        return User::isLoggedIn() ? $link : ButtonProvider::$signInFunction;
    }

    public static function createButton($text, $imageSrc, $userAction, $class) {

        $userAction = ButtonProvider::createLink($userAction);

        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc' alt=''>";
        return "<button class='$class' onclick='$userAction'>
                    $image
                    <span class='text'>$text</span>
                </button>";
    }


    public static function createHyperLinkButton($text, $imageSrc, $href, $class) {

        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc' alt=''>";
        return "<a href='$href'>
                    <button class='$class'
                        $image
                        <span class='text'>$text</span>
                    </button>
                </a>";
    }

    public static function createUserProfileButton($connect, $username) {
        $userObj = new User($connect, $username);
        $profilePic = $userObj->getProfilePic();

        $query = $connect->prepare("SELECT email from users WHERE userName =:username");
        $query->bindParam(":username", $username);
        $query->execute();

        $sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $email = $sqlData["email"];

        $link = "profile.php?email=$email";

        return "<a href='$link'>
                    <img src='$profilePic' class='profilePicture'>
                </a>";
    }

    public static function createEditVideoButton($videoId) {

        $href = "editVideo.php?videoId=$videoId";
        $button = ButtonProvider::createHyperLinkButton("Edit Video", null, $href, "edit button");

        return "<div class='editVideoButtonContainer'>
                    $button
                </div>";

    }
}