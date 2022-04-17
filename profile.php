<style>
<?php include('css/profile.css'); ?>
</style>

<?php
    require_once("header.php");
    require_once("sideNavBar.php");
    require_once("classes/MakeProfile.php");
    require_once("classes/ProfileData.php");

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
    
    if (!$profileData->userExists()) {
        echo "User Does not exist";
        exit();
    }


?>

<div id="mainSectionContainer">
    <div id="mainContentContainer">
            <div class='profile'>
                <div class='userInfo'>
                    <div class = 'userInfoContainer'>
                        <img class='profilePic' src='<?php echo $profileData->getProfilePicSource(); ?>'>
                        <div class = 'userNameInfo'>
                            <span class='title'><?php echo $profileData->getUserName(); ?></span>
                            <div class = 'editProfile'>
                                <a href='editProfile.php'>
                                    Edit Profile
                                </a>
                            </div>

                        </div>
                    </div>
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
                        Upload Tab
                        <?php require_once('upload.php');?>
                    </div>
                    <div class='tab-pane fade' id='contacts' role='tabpanel' aria-labelledby='contacts-tab'>
                        <?php require_once('contacts.php');?>
                        <br>
                        <b>Add Contact</b>
                        <form action='editProfile.php' method='POST'>
                            <input type='text' name='newContactUserName' placeholder='Contact Username' value='' required autocomplete='off'>
                            <label for='contactType'>Contact Type:</label>
                            <select id='contactType' name='contactType'>
                                <option value='family'>Family</option>
                                <option value='friend'>Friend</option>
                                <option value='favorite'>Favorite</option>
                            </select>
                            <br>
                            <input type='submit' name='submitButton' value='Add Contact' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                        </form>
                    </div>
            	</div>
		    </div>
    </div>
</div>
