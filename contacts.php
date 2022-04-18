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

$query = $connect->prepare("SELECT contacts.contactUserName as contactUserName, contacts.blocked as blocked, contacts.type as type, users.email as email FROM contacts INNER JOIN users ON contacts.contactUserName = users.userName WHERE contacts.userName = :userName");
$username = $profileData->getUsername();
$query->bindParam(":userName", $username);

$query->execute();

if ($query->rowCount() == 0) {
    return "No Contacts";
}

$output = "<b>Contacts:</b><br>";
$result = $query->setFetchMode(PDO::FETCH_ASSOC);
// foreach(new TableRows(new RecursiveArrayIterator($query->fetchAll())) as $k=>$v) {
//     $output .= $v."<br>";
// }
foreach($query->fetchAll() as $row) {
    $contactURL = "profile.php?email=" . $row["email"];
    $output .= "<a href='".$contactURL."'>".$row['contactUserName']."</a>";
    $output .= "     (" . $row['type'] . ")";
    if ($row['blocked'] == 1) {
        $output .= "     [blocked]";
    }
    $output .= "<br>";
}
echo $output;

?>

