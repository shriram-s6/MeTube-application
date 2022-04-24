<?php
//error_reporting(E_ERROR | E_PARSE);
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

	public function getPassword() {
		return $this->user->getPassword();
	}

	public function getFirstName() {
		return $this->user->getFirstName();
	}

	public function getLastName() {
		return $this->user->getLastName();
	}

	public function getUserFullName() {
		return $this->user->getFullName();
	}

	public function getEmail() {
		return $this->user->getEmail();
	}

	public function getProfilePic() {
		return $this->user->getProfilePic();
	}

	public function getSubscriberCount() {
		return $this->user->getSubscriberCount();
	}
}

?>