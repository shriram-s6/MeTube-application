<?php 

require_once("header.php");
require_once("sideNavBar.php");
require_once("config.php");
require_once("classes/ProfileData.php");


if(isset($_GET["email"])) {
    $email = $_GET["email"];
} else {
    echo "<div style='padding: 10px'>
            <br>
            <p>Channel not found.</p>
          </div>";
    exit();
}

$profileData = new ProfileData($connect, $email);

$query = $connect->prepare("SELECT contactUserName FROM contacts WHERE userName = :userName");
$username = $profileData->getUsername();
$query->bindParam(":userName", $username);

$query->execute();

if ($query->rowCount() == 0) {
    return "No Contacts";
}

$output = "";
$result = $query->setFetchMode(PDO::FETCH_ASSOC);
foreach(new TableRows(new RecursiveArrayIterator($query->fetchAll())) as $k=>$v) {
    $output .= $v."<br>";
}
echo $output;

?>

