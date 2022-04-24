<?php
error_reporting(E_ERROR | E_PARSE);

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

    public static function createUserProfileButton($connect, $email) {
        /*
        $query = $connect->prepare("SELECT email from users WHERE userName =:username");
        $query->bindParam(":username", $username);
        $query->execute();

        $sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $email = $sqlData["email"]; */

        $userObj = new User($connect, $email);
        $profilePic = $userObj->getProfilePic();

        $link = "profile.php?email=".$email;

        return "<a href='$link'>
                    $profilePic
                </a>";
    }

    public static function createEditVideoButton($videoId) {

        $href = "editVideo.php?videoId=$videoId";
        $button = ButtonProvider::createHyperLinkButton("Edit Video", null, $href, "edit button");

        return "<div class='editVideoButtonContainer'>
                    $button
                </div>";

    }

    public static function createSubscriberButton($connect, $subscribedToObj, $userLoggedInObj) {
        // comeback and check this
        $userTo = $subscribedToObj->getUserName();
        $userLoggedIn = $userLoggedInObj->getUserName();

        $isSubscribedTo = $userLoggedInObj->isSubscribedTo($userTo);

        $buttonText = $isSubscribedTo ? "Subscribed" : "Subscribe";

        $buttonText .= " " . $subscribedToObj->getSubscriberCount();

        $buttonClass = $isSubscribedTo ? "unsubscribe" : "subscribe";
        $action = "subscribe(\"$userTo\", \"$userLoggedIn\", this)";

        $button = ButtonProvider::createButton($buttonText, null, $action, $buttonClass);

        return "<div class='subscribeButtonContainer'>
                    $button
                </div>";
    }
}