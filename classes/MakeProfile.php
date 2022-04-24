<?php
error_reporting(E_ERROR | E_PARSE);
class TableRows extends RecursiveIteratorIterator {
	function __construct($it) {
	  parent::__construct($it, self::LEAVES_ONLY);
	}
      
	function current() {
	  return "<td style='width:150px;border:1px solid black;'>" . parent::current(). "</td>";
	}
      
	function beginChildren() {
	  echo "<tr>";
	}
      
	function endChildren() {
	  echo "</tr>" . "\n";
	}
}

require_once("ProfileData.php");

class MakeProfile {

	private $conn, $user, $user_email, $profileData;

	public function __construct($conn, $user_email) {
		$this->conn = $conn;
		$this->profileData = new ProfileData($conn, $user_email);
	}

	public function create() {
		$username = $this->profileData->getUsername();

		if (!$this->profileData->userExists()) {
			return "User Does not exist";
		}

		$output = $this->createHTML();

		return $output;
	}

	private function createHTML() {
		$userInfoSection = $this->createUserInfoSection();
		$tabsSection = $this->createTabsSection();
		$contentSection = $this->createContentSection();
		
		$output = "<div class='profile'>
				$userInfoSection
				$tabsSection
				$contentSection
		           </div>";

		return $output;
	}

	private function createUserInfoSection() {
		$profileImageSource = $this->profileData->getProfilePicSource();
		$profileName = $this->profileData->getUserName();
		// $subscriberCount = $this->profileData->getSubscriberCount();

		$output = "<div class='userInfo'>
				<div class = 'userInfoContainer'>
					<img class='profilePic' src='$profileImageSource'>
					<div class = 'userNameInfo'>
						<span class='title'>$profileName</span>
						<div class = 'editProfile'>
							<a href='editProfile.php'>
								Edit Profile
							</a>
						</div>

					</div>
				</div>
		          </div>";

		return $output;
	}



	private function createTabsSection() {
		$output = "
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
		</div>";

		return $output;
	}
	private function createContentSection() {
		$output = "
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
			</div>
			<div class='tab-pane fade' id='contacts' role='tabpanel' aria-labelledby='contacts-tab'>
				".$this->createContactsSection()."
				<br>
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
            	</div>";

		return $output;
	}

	private function createContactsSection() {
		$query = $this->conn->prepare("SELECT contactUserName FROM contacts WHERE userName = :userName");
		$query->bindParam(":userName", $this->profileData->getUsername());

		$query->execute();

		if ($query->rowCount() == 0) {
			return "No Contacts";
		}

		$output = "<br>";
		$result = $query->setFetchMode(PDO::FETCH_ASSOC);
		foreach(new TableRows(new RecursiveArrayIterator($query->fetchAll())) as $k=>$v) {
			$output .= $v."<br>";
		}
		
		// $string="";implode(",",$contacts);

		return $output;
	}


}

?>