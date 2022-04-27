<style>
    <?php include('css/profile.css'); ?>
</style>

<?php
error_reporting(E_ERROR | E_PARSE);
require_once("header.php");
require_once("sideNavBar.php");
require_once("classes/MakeProfile.php");
require_once("classes/ProfileData.php");
require_once("classes/FormSanitizer.php");

if (isset($_GET["email"])) {
    $email = $_GET["email"];
} else {
    echo "<div style='padding: 10px'>
                <br>
                <p>Channel not found.</p>
              </div>";
    exit();
}
// $makeProfile = new MakeProfile($connect, $email);
// echo $makeProfile->create();

$profileData = new ProfileData($connect, $email);

$profileURL = "profile.php?email=" . $email;

$profileUserName = $profileData->getUsername();

if (!$profileData->userExists()) {
    echo "User Does not exist";
    exit();
}


$isOnPersonalAccount = str_replace("email=", "", $_SERVER['QUERY_STRING']) == $_SESSION['userLoggedIn'];

if (!$isOnPersonalAccount) {
    $foreignProfileEmail = str_replace("email=", "", $_SERVER['QUERY_STRING']);
    $personalUser = new ProfileData($connect, $_SESSION['userLoggedIn']);
    $personalUserName = $personalUser->getUsername();
    $queryString = "SELECT users.email, users.userName, contacts.userName, contacts.contactUserName, contacts.blocked FROM users INNER JOIN contacts on users.userName = contacts.userName WHERE users.email = :email AND contacts.contactUserName = :userName";
    $query = $connect->prepare($queryString);
    $query->bindParam(":email", $foreignProfileEmail);
    $query->bindParam(":userName", $personalUserName);
    $query->execute();

    if ($query->rowCount() > 0) {
        foreach ($query->fetchAll() as $row) {
            if ($row['blocked'] == 1) {
                echo "You are not allowed to view this profile.";
                exit();
            }
        }
    }
}


if (isset($_POST["contactsSubmitButton"])) {
    $newContactUsername = $_POST["newContactUserName"];
    $newContactType = $_POST["contactType"];

    $profileUserName = $profileData->getUsername();


    $query = $connect->prepare("SELECT * FROM users WHERE userName=:userName");
    $query->bindParam(":userName", $newContactUsername);
    $query->execute();

    $alreadyUserQuery = $connect->prepare("SELECT * FROM contacts WHERE userName=:userName AND contactUserName=:contactUserName");
    $alreadyUserQuery->bindParam(":userName", $profileUserName);
    $alreadyUserQuery->bindParam(":contactUserName", $newContactUsername);
    $alreadyUserQuery->execute();

    if ($alreadyUserQuery->rowCount() == 0) {
        if ($query->rowCount() == 0) {
            echo "<span class='errorMessage' style='color: red;'>Username not valid!</span>";
        } else {

            $query = $connect->prepare("INSERT INTO contacts (userName, contactUserName, type, blocked) VALUES(:userName, :contactUserName, :type, 0)");
            $query->bindParam(":userName", $profileUserName);
            $query->bindParam(":contactUserName", $newContactUsername);
            $query->bindParam(":type", $newContactType);
            $query->execute();
            echo "<span class='successMessage' style='color: green;'>Contact Added!</span>";
        }
    }
    header($profileURL);

}

if (isset($_POST["removeContactsSubmitButton"])) {
    $contactUsername = $_POST["newContactUserName"];

    $profileUserName = $profileData->getUsername();


    $query = $connect->prepare("SELECT * FROM users WHERE userName=:userName");
    $query->bindParam(":userName", $contactUsername);
    $query->execute();

    $alreadyUserQuery = $connect->prepare("SELECT * FROM contacts WHERE userName=:userName AND contactUserName=:contactUserName");
    $alreadyUserQuery->bindParam(":userName", $profileUserName);
    $alreadyUserQuery->bindParam(":contactUserName", $contactUsername);
    $alreadyUserQuery->execute();

    if ($query->rowCount() == 0) {

        echo "<span class='errorMessage' style='color: red;'>Username not valid!</span>";

    } else if ($alreadyUserQuery->rowCount() != 0) {

        $query = $connect->prepare("DELETE FROM contacts WHERE userName =:userName AND contactUserName=:contactUserName");
        $query->bindParam(":userName", $profileUserName);
        $query->bindParam(":contactUserName", $contactUsername);
        $query->execute();

        echo "<span class='successMessage' style='color: green;'>Contact Removed!</span>";

    } else {
        echo "<span class='errorMessage' style='color: red;'>The user is not in your contact list! Add the user to your contact, if you wish!</span>";
    }

    header($profileURL);

}

if (isset($_POST["editContactButton"])) {
    $editContactUsername = $_POST["editContactUserName"];
    $editContactType = $_POST["editContactType"];
    $editBlocked = $_POST["editBlockedContact"];

    $querySetString = "";

    if ($editBlocked == "blocked") {
        $editBlocked = 1;
    } else if ($editBlocked == "unblocked") {
        $editBlocked = 0;
    }

    if ($editBlocked != "--") {
        $querySetString .= "blocked=:blocked";
    }

    if ($editContactType != "--") {
        if (strlen($querySetString) > 0) {
            $querySetString .= ", ";
        }
        $querySetString .= "type=:type";
    }


    $queryString = "UPDATE contacts SET " . $querySetString . " WHERE userName=:userName AND contactUserName=:contactUserName";

    $query = $connect->prepare($queryString);
    $query->bindParam(":userName", $profileUserName);
    $query->bindParam(":contactUserName", $editContactUsername);
    if (str_contains($querySetString, ":blocked")) {
        $query->bindParam(":blocked", $editBlocked);
    }
    if (str_contains($querySetString, ":type")) {
        $query->bindParam(":type", $editContactType);
    }

    $query->execute();
    header($profileURL);
}

if (isset($_POST["playlistsSubmitButton"])) {
    $newPlaylistName = $_POST["newPlaylistName"];

    $profileUserName = $profileData->getUsername();

    $alreadyPlaylistQuery = $connect->prepare("SELECT * FROM playlists WHERE created_by=:userName AND playlist_name=:playlist_name");
    $alreadyPlaylistQuery->bindParam(":userName", $profileUserName);
    $alreadyPlaylistQuery->bindParam(":playlist_name", $newPlaylistName);
    $alreadyPlaylistQuery->execute();

    if ($alreadyPlaylistQuery->rowCount() == 0) {

        $query = $connect->prepare("INSERT INTO playlists (created_by, playlist_name) VALUES(:userName, :playlistName)");
        $query->bindParam(":userName", $profileUserName);
        $query->bindParam(":playlistName", $newPlaylistName);
        $query->execute();
        echo "<span class='successMessage' style='color: green;'>Playlist Created!</span>";

    } else {
        echo "<span class='errorMessage' style='color: red;'>Playlist already created!</span>";
    }
    header($profileURL);
}

if (isset($_POST["renamePlaylistsSubmitButton"])) {
    $oldPlaylistName = $_POST["oldPlaylistName"];
    $newPlaylistName = $_POST["newPlaylistName"];

    $profileUserName = $profileData->getUsername();

    $alreadyPlaylistQuery = $connect->prepare("SELECT * FROM playlists WHERE created_by=:userName AND playlist_name=:playlist_name");
    $alreadyPlaylistQuery->bindParam(":userName", $profileUserName);
    $alreadyPlaylistQuery->bindParam(":playlist_name", $oldPlaylistName);
    $alreadyPlaylistQuery->execute();

    if ($alreadyPlaylistQuery->rowCount() != 0) {

        $query = $connect->prepare("UPDATE playlists SET playlist_name=:newPlaylistName WHERE created_by=:userName AND playlist_name=:oldPlaylistName");
        $query->bindParam(":newPlaylistName", $newPlaylistName);
        $query->bindParam(":userName", $profileUserName);
        $query->bindParam(":oldPlaylistName", $oldPlaylistName);
        $query->execute();
        echo "<span class='successMessage' style='color: green;'>Playlist Renamed!</span>";

    } else {

        echo "<span class='errorMessage' style='color: red;'>This playlist doesn't exist</span>";
    }
    header($profileURL);
}

if (isset($_POST["removePlaylistsSubmitButton"])) {
    $playlistName = $_POST["playlistName"];

    $profileUserName = $profileData->getUsername();

    $alreadyPlaylistQuery = $connect->prepare("SELECT * FROM playlists WHERE created_by=:userName AND playlist_name=:playlist_name");
    $alreadyPlaylistQuery->bindParam(":userName", $profileUserName);
    $alreadyPlaylistQuery->bindParam(":playlist_name", $playlistName);
    $alreadyPlaylistQuery->execute();

    if ($alreadyPlaylistQuery->rowCount() != 0) {

        $query = $connect->prepare("DELETE FROM playlists WHERE created_by=:userName AND playlist_name=:playlistName");
        $query->bindParam(":userName", $profileUserName);
        $query->bindParam(":playlistName", $playlistName);
        $query->execute();
        echo "<span class='successMessage' style='color: green;'>Playlist Removed!</span>";

    } else {

        echo "<span class='errorMessage' style='color: red;'>This playlist doesn't exist</span>";
    }
    header($profileURL);
}

if (isset($_POST["addToPlaylistSubmitButton"])) {

    $mediaTitle = $_POST["mediaName"];
    $playlistName = $_POST["playlistName"];

    $profileUserName = $profileData->getUsername();

    $playlistIDQuery = $connect->prepare("SELECT id FROM playlists WHERE playlist_name=:playlistName");
    $playlistIDQuery->bindParam(":playlistName", $playlistName);
    $playlistIDQuery->execute();

    foreach ($playlistIDQuery->fetchAll() as $row) {
        $playlistID = $row['id'];
    }

    $setPlaylistIdQuery = $connect->prepare("UPDATE file_uploads SET playlist_id=:playlistID WHERE title=:mediaTitle");
    $setPlaylistIdQuery->bindParam(":playlistID", $playlistID);
    $setPlaylistIdQuery->bindParam(":mediaTitle", $mediaTitle);
    $setPlaylistIdQuery->execute();

    header($profileURL);
}

if (isset($_POST["addToFavouritesSubmitButton"])) {

    $mediaName = $_POST["mediaName"];

    $getVideoIdQuery = $connect->prepare("SELECT * FROM file_uploads WHERE title=:mediaName");
    $getVideoIdQuery->bindParam(":mediaName", $mediaName);
    $getVideoIdQuery->execute();

    if ($getVideoIdQuery->rowCount() > 0) {
        $videoId = $getVideoIdQuery->fetch(PDO::FETCH_ASSOC)["id"];
    }


    $profileUserName = $profileData->getUsername();

    $alreadyFavouritesQuery = $connect->prepare("SELECT * FROM favourites WHERE userName=:userName AND videoId=:videoId");
    $alreadyFavouritesQuery->bindParam(":userName", $profileUserName);
    $alreadyFavouritesQuery->bindParam(":videoId", $videoId);
    $alreadyFavouritesQuery->execute();

    if ($alreadyFavouritesQuery->rowCount() == 0) {
        $querySQL = "INSERT INTO favourites (userName, videoId) VALUES('$profileUserName', $videoId)";

        //$query = $connect->prepare("INSERT INTO favourites (userName, videoId) VALUES(:userName, :videoId)");
        //$query->bindParam(":userName", $profileUserName);
        //$query->bindParam(":videoId", $videoId);
        $query = $connect->prepare($querySQL);
        $query->execute();
        echo "<span class='successMessage' style='color: green;'>Media added to the favourites</span>";

    } else {
        echo "<span class='errorMessage' style='color: red;'>Media is already in the favourites list!</span>";
    }
    header($profileURL);
}

if (isset($_POST["removeFromFavouritesSubmitButton"])) {

    $mediaName = $_POST["mediaName"];

    $getVideoIdQuery = $connect->prepare("SELECT * FROM file_uploads WHERE title=:mediaName");
    $getVideoIdQuery->bindParam(":mediaName", $mediaName);
    $getVideoIdQuery->execute();

    if ($getVideoIdQuery->rowCount() > 0) {
        $videoId = $getVideoIdQuery->fetch(PDO::FETCH_ASSOC)["id"];
    }


    $profileUserName = $profileData->getUsername();

    $alreadyFavouritesQuery = $connect->prepare("SELECT * FROM favourites WHERE userName=:userName AND videoId=:videoId");
    $alreadyFavouritesQuery->bindParam(":userName", $profileUserName);
    $alreadyFavouritesQuery->bindParam(":videoId", $videoId);
    $alreadyFavouritesQuery->execute();

    if ($alreadyFavouritesQuery->rowCount() == 0) {

        echo "<span class='errorMessage' style='color: red;'>Media not in the favourites list! Add to the favourites if you wish!</span>";

    } else {

        $querySQL = "DELETE FROM favourites WHERE userName='$profileUserName' AND videoId=$videoId";

        //$query = $connect->prepare("INSERT INTO favourites (userName, videoId) VALUES(:userName, :videoId)");
        //$query->bindParam(":userName", $profileUserName);
        //$query->bindParam(":videoId", $videoId);
        $query = $connect->prepare($querySQL);
        $query->execute();
        echo "<span class='successMessage' style='color: green;'>Media removed from the favourites</span>";
    }
    header($profileURL);
}

?>

<?php require_once("footer.php") ?>

<div id="mainSectionContainer">
    <div id="mainContentContainer">
        <div class='profile'>
            <div class='userInfo'>
                <div class='userInfoContainer'>
                    <?php echo $profileData->getProfilePic(); ?>
                    <div class='userNameInfo'>
                        <span class='title'><?php echo $profileData->getUserName(); ?></span>
                        <?php

                        $isOnPersonalAccount = str_replace("email=", "", $_SERVER['QUERY_STRING']) == $_SESSION['userLoggedIn'];

                        if ($isOnPersonalAccount) {
                            echo "<div class = 'editProfile'>
                                        <a href='editProfile.php'>
                                            Edit Profile
                                        </a>
                                    </div>";
                        }

                        ?>
                    </div>
                </div>
                <?php
                if ($isOnPersonalAccount) {
                    echo "<button id='chatButton' onclick=\"location.href = 'chatting.php';\"><img id='chatButtonPicture' src='images/icons/chatting_icon.png'></button>";
                }

                ?>
            </div>


            <div class='tabs'>
                <ul class='nav nav-tabs' id='myTab' role='tablist' style='flex-direction: row;'>
                    <li class='nav-item'>
                        <a class='nav-link active' id='home-tab' data-toggle='tab' href='#home' role='tab'
                           aria-controls='home' aria-selected='true'>Home</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='playlists-tab' data-toggle='tab' href='#playlists' role='tab'
                           aria-controls='playlists' aria-selected='false'>Playlists</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='channels-tab' data-toggle='tab' href='#channels' role='tab'
                           aria-controls='channels' aria-selected='false'>Channels</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='favourites-tab' data-toggle='tab' href='#favourites' role='tab'
                           aria-controls='favourites' aria-selected='false'>Favourites</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='upload-videos-tab' data-toggle='tab' href='#upload-videos' role='tab'
                           aria-controls='upload-videos' aria-selected='false'>Upload Media</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='contacts-tab' data-toggle='tab' href='#contacts' role='tab'
                           aria-controls='contacts' aria-selected='false'>Contacts</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='discussion-tab' data-toggle='tab' href='#discussion' role='tab'
                           aria-controls='discussion' aria-selected='false'>Discussion</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='wordcloud-tab' data-toggle='tab' href='#wordcloud' role='tab'
                           aria-controls='wordcloud' aria-selected='false'>Word Cloud</a>
                    </li>
                </ul>
            </div>

            <div class='tab-content' id='myTabContent'>
                <div class='tab-pane fade show active' id='home' role='tabpanel' aria-labelledby='home-tab'>
                    <?php
                    require_once('videos.php');
                    ?>
                </div>
                <div class='tab-pane fade' id='playlists' role='tabpanel' aria-labelledby='playlists-tab'>
                    <?php
                    $query = $connect->prepare("SELECT * FROM playlists WHERE created_by=:userName");
                    $query->bindParam(":userName", $profileUserName);
                    $query->execute();

                    foreach ($query->fetchAll() as $row) {
                        $playlistID = $row['id'];
                        $playlistName = $row['playlist_name'];
                        echo "<h1>" . $playlistName . "</h1>";
                        $videoQuerySQL = "SELECT * FROM file_uploads WHERE playlist_id=" . $playlistID;
                        $videoQuery = $connect->prepare($videoQuerySQL);
                        $videoQuery->execute();

                        $playlistMedia = array();
                        foreach ($videoQuery->fetchAll() as $sub_row) {
                            $media = new Video($connect, $sub_row, $user);
                            array_push($playlistMedia, $media);
                        }

                        if ($playlistMedia != null) {
                            $grid = new VideoGrid($connect, $user);
                            echo $grid->create(null, null, $playlistMedia);
                        } else {
                            echo "Add videos!";
                        }


                    }
                    ?>
                    <br>
                    <br>
                    <form action='' method='POST'>
                        <input type='text' name='newPlaylistName' placeholder='Playlist Name' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='playlistsSubmitButton' value='Create Playlist'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                    <br>
                    <br>
                    <form action='' method='POST'>
                        <input type='text' name='playlistName' placeholder='Playlist Name' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='removePlaylistsSubmitButton' value='Remove Playlist'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                    <br>
                    <br>
                    <form action='' method='POST'>
                        <input type='text' name='oldPlaylistName' placeholder='Old Playlist Name' value='' required
                               autocomplete='off'>
                        <input type='text' name='newPlaylistName' placeholder='New Playlist Name' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='renamePlaylistsSubmitButton' value='Rename Playlist'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                    <br>
                    <br>
                    <form action='' method='POST'>
                        <input type='text' name='mediaName' placeholder='Media Title' value='' required
                               autocomplete='off'>
                        <input type='text' name='playlistName' placeholder='Playlist Name' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='addToPlaylistSubmitButton' value='Add to Playlist'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                </div>
                <div class='tab-pane fade' id='channels' role='tabpanel' aria-labelledby='channels-tab'>

                    <?php require_once('channels.php'); ?>
                </div>
                <div class='tab-pane fade' id='favourites' role='tabpanel' aria-labelledby='favourites-tab'>
                    <?php
                    $query = $connect->prepare("SELECT * FROM favourites WHERE userName=:userName");
                    $query->bindParam(":userName", $profileUserName);
                    $query->execute();

                    foreach ($query->fetchAll() as $row) {
                        $videoId = $row['videoId'];
                        $videoQuerySQL = "SELECT * FROM file_uploads WHERE id=" . $videoId;
                        $videoQuery = $connect->prepare($videoQuerySQL);
                        $videoQuery->execute();

                        $favouriteMedia = array();

                        foreach ($videoQuery->fetchAll() as $sub_row) {
                            $media = new Video($connect, $sub_row, $user);
                            array_push($favouriteMedia, $media);
                        }

                        if ($favouriteMedia != null) {
                            $grid = new VideoGrid($connect, $user);
                            echo $grid->create(null, null, $favouriteMedia);
                        } else {
                            echo "Add videos!";
                        }

                    }
                    ?>
                    <br>
                    <br>
                    <form action='' method='POST'>
                        <input type='text' name='mediaName' placeholder='Media Title' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='addToFavouritesSubmitButton' value='Add to Favourites'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                    <form action='' method='POST'>
                        <input type='text' name='mediaName' placeholder='Media Title' value='' required
                               autocomplete='off'>
                        <br>
                        <input type='submit' name='removeFromFavouritesSubmitButton' value='Remove from Favourites'
                               style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                    </form>
                </div>
                <div class='tab-pane fade' id='upload-videos' role='tabpanel' aria-labelledby='upload-videos-tab'>
                    <?php

                    if ($isOnPersonalAccount) {
                        require_once('upload.php');
                    } else {
                        echo "This feature is only available on your account.";
                    }
                    ?>
                </div>
                <div class='tab-pane fade' id='contacts' role='tabpanel' aria-labelledby='contacts-tab'>
                    <?php require_once('contacts.php'); ?>
                    <?php

                    if ($isOnPersonalAccount) {
                        echo "
                            <br>
                            <b>Add Contact</b>
                            <form action='' method='POST'>
                                <input type='text' name='newContactUserName' placeholder='Contact Username' value='' required autocomplete='off'>
                                <label for='contactType'>Contact Type:</label>
                                <select id='contactType' name='contactType'>
                                    <option value='family'>Family</option>
                                    <option value='friend'>Friend</option>
                                    <option value='favorite'>Favorite</option>
                                </select>
                                <br>
                                <input type='submit' name='contactsSubmitButton' value='Add Contact' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                            </form>
                            <br>
                            <br>
                            <b>Remove Contact</b>
                            <form action='' method='POST'>
                                <input type='text' name='newContactUserName' placeholder='Contact Username' value='' required autocomplete='off'>
                                <br>
                                <input type='submit' name='removeContactsSubmitButton' value='Remove Contact' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                            </form>
                            <br>
                            <br>
                            <b>Edit Contact</b>
                            <form action='' method='POST'>
                                <input type='text' name='editContactUserName' placeholder='Contact Username' value='' required autocomplete='off'>
                                <label for='contactType'>Contact Type:</label>
                                <select id='editContactType' name='editContactType'>
                                    <option value='--'>--</option>
                                    <option value='family'>Family</option>
                                    <option value='friend'>Friend</option>
                                    <option value='favorite'>Favorite</option>
                                </select>
                                <label for='contactType'>Block Contact:</label>
                                <select id='editBlockedContact' name='editBlockedContact'>
                                    <option value='--'>--</option>
                                    <option value='blocked'>Blocked</option>
                                    <option value='unblocked'>Unblocked</option>
                                </select>
                                <br>
                                <input type='submit' name='editContactButton' value='Edit Contact' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                            </form>";
                    } else {
                        echo "<br><br>You can only add or edit contacts on your account.";
                    }

                    ?>
                </div>
                <div class='tab-pane fade' id='discussion' role='tabpanel' aria-labelledby='discussion-tab'>
                    <?php
                    require_once('discussionForum.php');
                    ?>
                </div>
                <div class='tab-pane fade' id='wordcloud' role='tabpanel' aria-labelledby='wordcloud-tab'>
                    <?php require_once('wordCloud.php'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
