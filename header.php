<?php 

require_once("config.php");
require_once("classes/User.php");

if(isset($_SESSION["userLoggedIn"])) {
    $user_email = $_SESSION["userLoggedIn"];
} else {
    $user_email = "none";
}


$user = new User($connect, $user_email);

?>
<!DOCTYPE html>

<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MeTube</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="javascript/commonActions.js"></script>
</head>
<body>
<div id="pageContainer">

    <div id="masterHeadContainer">
        <button class="navShowHide">
            <img src="images/icons/menu.png" alt="MeTube Menu">
        </button>

        <a class="logoContainer" href="index.php">
            <img src="images/icons/MeTubeLogo.png" alt="MeTube Logo" title="MeTube Logo">
        </a>

        <div class="searchBarContainer">

            <form action="search.php" method="GET">
                <label>
                    <input type="text" class="searchBar" name="term" placeholder="Search">
                    <button class="searchButton">
                        <img src="images/icons/search_icon.png" alt="search icon" title="search icon">
                    </button>
                </label>
            </form>

        </div>

        <?php 
        
        if (!isset($_SESSION["userLoggedIn"])) {
            require_once("signInLogo.php");
        } else {
            require_once("profileLogo.php");
            require_once("signOutLogo.php");
        }
        

        
        ?>

    </div>

    
    <?php require_once("sideNavBar.php"); ?>

</div>
