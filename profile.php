<style>
<?php include('css/profile.css'); ?>
</style>

<?php
    require_once("header.php");
    require_once("sideNavBar.php");
    require_once("classes/MakeProfile.php");
    require_once("classes/ProfileData.php");
    require_once("classes/FormSanitizer.php");

    if(isset($_GET["email"])) {
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
            foreach($query->fetchAll() as $row) {
                if ($row['blocked'] == 1) {
                    echo "You are not allowed to view this profile.";
                    exit();
                }
            }
        }
    }

                    

    if(isset($_POST["contactsSubmitButton"])) {
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
            if($query->rowCount() == 0) {
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

        $profileUserName = $profileData->getUsername();

        $queryString = "UPDATE contacts SET ".$querySetString." WHERE userName=:userName AND contactUserName=:contactUserName";

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
    

?>

<?php require_once("footer.php") ?>

<div id="mainSectionContainer">
    <div id="mainContentContainer">
            <div class='profile'>
                <div class='userInfo'>
                    <div class = 'userInfoContainer'>
                        <?php echo $profileData->getProfilePic(); ?>
                        <div class = 'userNameInfo'>
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
                            <a class='nav-link active' id='home-tab' data-toggle='tab' href='#home' role='tab' aria-controls='home' aria-selected='true'>Home</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='videos-tab' data-toggle='tab' href='#videos' role='tab' aria-controls='videos' aria-selected='false'>Videos</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='playlists-tab' data-toggle='tab' href='#playlists' role='tab' aria-controls='playlists' aria-selected='false'>Playlists</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='channels-tab' data-toggle='tab' href='#channels' role='tab' aria-controls='channels' aria-selected='false'>Channels</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='upload-videos-tab' data-toggle='tab' href='#upload-videos' role='tab' aria-controls='upload-videos' aria-selected='false'>Upload Videos</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='contacts-tab' data-toggle='tab' href='#contacts' role='tab' aria-controls='contacts' aria-selected='false'>Contacts</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' id='discussion-tab' data-toggle='tab' href='#discussion' role='tab' aria-controls='discussion' aria-selected='false'>Discussions</a>
                        </li>
                    </ul>
                </div>

                <div class='tab-content' id='myTabContent'>
                    <div class='tab-pane fade show active' id='home' role='tabpanel' aria-labelledby='home-tab'>
                        Home Tab
                    </div>
                    <div class='tab-pane fade' id='videos' role='tabpanel' aria-labelledby='videos-tab'>
                        Videos Tab
                    </div>
                    <div class='tab-pane fade' id='playlists' role='tabpanel' aria-labelledby='playlists-tab'>
                        Playlist Tab
                    </div>
                    <div class='tab-pane fade' id='channels' role='tabpanel' aria-labelledby='channels-tab'>
                        Channels Tab
                    </div>
                    <div class='tab-pane fade' id='upload-videos' role='tabpanel' aria-labelledby='upload-videos-tab'>
                        <?php require_once('upload.php');?>
                    </div>
                    <div class='tab-pane fade' id='contacts' role='tabpanel' aria-labelledby='contacts-tab'>
                        <?php require_once('contacts.php');?>
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
                        </form>
                    </div>
                    <div class='tab-pane fade' id='discussion' role='tabpanel' aria-labelledby='discussion-tab'>
                        <?php require_once('discussionForum.php');?>
                    </div>
            	</div>
		    </div>
    </div>
</div>
