<?php
error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
session_unset();
header("Location: index.php");

?>