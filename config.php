<?php
ob_start();
session_start();
date_default_timezone_set("America/New_York");
try {
    $connect = new PDO("mysql:dbname=metube;host=localhost", "root", "");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $exception) {
    echo "Connection failed: ". $exception->getMessage();
}