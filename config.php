<?php
//error_reporting(E_ERROR | E_PARSE);
ob_start();
session_start();
date_default_timezone_set("America/New_York");
try {
    global $connect;
    $connect = new PDO("mysql:dbname=metube-g2_p6fa;host=mysql1.cs.clemson.edu", "metube-g2_aala", "callicottsekar1");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    // echo $connect;
} catch (PDOException $exception) {
    echo "Connection failed: ". $exception->getMessage();
}