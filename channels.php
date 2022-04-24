<?php
error_reporting(E_ERROR | E_PARSE);
require_once('config.php');
require_once('classes/ProfileData.php');

$profile_data = new ProfileData($connect, $_SESSION["userLoggedIn"]);

$query = $connect->prepare("SELECT subscribers.subscribedTo, users.email FROM subscribers INNER JOIN users on subscribers.subscribedTo = users.userName WHERE subscribers.subscribedFrom = :userName");
$query->bindParam(":userName", $profile_data->getUsername());
$query->execute();

foreach($query->fetchAll() as $row) {
	$subscribedTo = $row["subscribedTo"];
	$email = $row["email"];
	$subscribedTo_profile_data = new ProfileData($connect, $email);
	$output = "
		<div style='width: 30%; padding: 10px; outline: 1px solid black; outline-offset: -5px;'>
			".$subscribedTo_profile_data->getProfilePic()."
			<a href='profile.php?email=".$email."' style='padding: 0px 0px 0px 20px;'>
			".$subscribedTo."
			</a>
			
		</div>
	";
	echo $output;
}

?>