<?php
//error_reporting(E_ERROR | E_PARSE);
require_once("../config.php");
if(isset($_POST["subscribedTo"]) && isset($_POST["subscribedFrom"])) {
    $subscribedTo = $_POST["subscribedTo"];
    $subscribedFrom = $_POST["subscribedFrom"];

    $query = $connect->prepare("SELECT * FROM subscribers WHERE subscribedTo =:subscribedTo AND subscribedFrom =:subscribedFrom");
    $query->bindParam(":subscribedTo", $subscribedTo);
    $query->bindParam(":subscribedFrom", $subscribedFrom);
    $query->execute();

    if($query->rowCount() == 0) {
        $query = $connect->prepare("INSERT INTO subscribers(subscribedTo,subscribedFrom) VALUES (:subscribedTo,:subscribedFrom)");
        $query->bindParam(":subscribedTo", $subscribedTo);
        $query->bindParam(":subscribedFrom", $subscribedFrom);
        $query->execute();
    } else {
        $query = $connect->prepare("DELETE FROM subscribers WHERE subscribedTo =:subscribedTo AND subscribedFrom =:subscribedFrom");
        $query->bindParam(":subscribedTo", $subscribedTo);
        $query->bindParam(":subscribedFrom", $subscribedFrom);
        $query->execute();
    }

    $query = $connect->prepare("SELECT * FROM subscribers WHERE subscribedTo =:subscribedTo");
    $query->bindParam(":subscribedTo", $subscribedTo);
    $query->execute();

    unset($_POST["subscribedTo"]);
    unset($_POST["subscribedFrom"]);

    echo $query->rowCount();
} else {
    echo "check the parameters";
}