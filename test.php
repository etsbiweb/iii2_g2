<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/log.php");
require_once("includes/dbh.php");

$logs = new Log();

$logs->newLog("prvi glupi log");