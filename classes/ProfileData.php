<?php

class ProfileData {

	private $conn, $user;

	public function __construct($conn, $user_email) {
		$this->conn = $conn;
		$this->user = new User($conn, $user_email);
	}

	public function getUsername() {
		return $this->user->getUsername();
	}

	public function userExists() {
		$query = $this->conn->prepare("SELECT * FROM users WHERE userName = :username");
		$query->bindParam(":username", $this->getUsername());
		
		$query->execute();

		return $query->rowCount() != 0;
	}

	public function getUserFullName() {
		return $this->user->getFullName();
	}

	public function getProfilePicSource() {
		return $this->user->getProfilePic();
	}

	public function getSubscriberCount() {
		return $this->user->getSubscriberCount();
	}
}

?>