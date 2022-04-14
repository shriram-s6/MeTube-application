<?php require_once("header.php"); ?>
<?php require_once("signInLogo.php"); ?>
<?php require_once("footer.php"); ?>
<?php 

if (isset($_SESSION["userLoggedIn"])) {
	echo "user is logged in as " . $user->getFullName();
} else {
	echo "not logged in";
}

?>
