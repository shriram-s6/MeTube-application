<?php
require_once("header.php");
require_once("config.php");
require_once("sideNavBar.php");
require_once("classes/ProfileData.php");

$profileData = new ProfileData($connect, $_SESSION["userLoggedIn"]);
$userName = $profileData->getUsername();

if (isset($_GET["sender"])) {
	$sender = $_GET["sender"];
	if ($sender != $userName) {
		echo "You are not authorized to view this page.";
		exit();
	}
} else {
	$sender = NULL;
}

if (isset($_GET["receiver"])) {
	$receiver = $_GET["receiver"];
} else {
	$receiver = NULL;
}

if (isset($_POST["send"])) {
	if ($_POST["send"] != NULL) {
		echo "trying to send";
		$message = $_POST["messageInput"];
		$query = $connect->prepare("INSERT INTO chat (message, toUserName, fromUserName) VALUES (:message, :toUserName, :fromUserName)");
		$query->bindParam(":message", $message);
		$query->bindParam(":toUserName", $receiver);
		$query->bindParam(":fromUserName", $userName);
		$query->execute();
		header("Location: chatting.php?sender=" . $userName . "&receiver=" . $receiver);
	}
}

?>


<link rel="stylesheet" href="css/chatting.css" type="text/css">
<div id="chatUsersContainer">
	<b class="chatLinks">
		Chat
	</b>
	<?php
	
		$query = $connect->prepare("SELECT * FROM contacts WHERE userName = :userName");
		$query->bindParam(":userName", $userName);
		$query->execute();

		foreach ($query->fetchAll() as $row) {
			$chatUserName = $row["contactUserName"];
			$chatURL = "chatting.php?sender=".$userName."&receiver=".$chatUserName;
			$output = "<a class='chatLinks' href='".$chatURL."'>".$chatUserName."</a><br>";
			echo $output."<br>";
		}
	
	?>
</div>
<div id="chatMessagesContainer">
	<?php
		if ($sender != NULL && $receiver != NULL) {
			$query = $connect->prepare("SELECT * FROM chat WHERE (fromUserName = :sender AND toUserName = :receiver) OR (fromUserName = :receiver AND toUserName = :sender) ORDER BY chatId DESC LIMIT 5");
			$query->bindParam(":sender", $sender);
			$query->bindParam(":receiver", $receiver);
			$query->execute();

			foreach (array_reverse($query->fetchAll()) as $row) {
				$fromUserName = $row["fromUserName"];
				$message = $row["message"];
				if ($row['toUserName'] == $userName) {
					$output = "<p class='messageReceived'>".$message."</p>";
				} else {
					$output = "<p class='messageSent'>".$message."</p>";
				}
				
				echo $output;
			}

			
			$textInput = "<input type='text' id='messageInput' name='messageInput' placeholder='Type your message here...'>";
			$sendButton = "<input type='submit' name='send' id='sendButton' value='Send'>";

			echo "<form action='' method='POST'>
				$textInput
				$sendButton
			</form>";
		}
	?>
</div>
