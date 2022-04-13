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
        echo "<div style='padding: 10px'>
                <br>
                <p>Channel not found.</p>
              </div>";
        exit();
    }
    $makeProfile = new MakeProfile($username);
    echo $makeProfile->create();
?>
