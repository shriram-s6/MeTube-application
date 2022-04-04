<style>
<?php include('css/profile.css'); ?>
</style>

<?php
    require_once("header.php");
    require_once("sideNavBar.php");
    require_once("classes/MakeProfile.php");

    if(isset($_GET["username"])) {
        $username = $_GET["username"];
    } else {
        echo "Channel not found.";
        exit();
    }
    $makeProfile = new MakeProfile($username);
    echo $makeProfile->create();
?>
