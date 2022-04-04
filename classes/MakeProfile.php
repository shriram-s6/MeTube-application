<?php

require_once("ProfileData.php");

class MakeProfile {

	private $username, $profileData;

	public function __construct($username) {
		$this->username = $username;
		$this->profileData = new ProfileData();
	}

	public function create() {
		$output = $this->createHTML();
		echo "hello";

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
		$subscriberCount = $this->profileData->getSubscriberCount();

		$subButton = $this->createSubscriberButton();

		$output = "<div class='userInfo'>
				<div class = 'userInfoContainer'>
					<img class='profilePic' src='$profileImageSource'>
					<div class = 'userNameInfo'>
						<span class='title'>$profileName</span>
						<span class='subscriberCount'>$subscriberCount Subscribers</span>
					</div>
				</div>
				<div class = 'subButtonContainer'>
					<div class = 'subscribeButton'>
						$subButton
					</div>
				</div>
		          </div>";

		return $output;
	}


	private function createSubscriberButton() {
		// if the user is on their own profile
			// return empty string
		// else
		return "<div class='subscribeButtonContainer'>
				<button class='button'>
					<span class='text'>Subscribe</span>       
				</button>
		 	</div>";
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
            	</div>";

		return $output;
	}


}

?>