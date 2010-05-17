<?php
ob_start();
session_start();

//error_reporting(0);

putenv("TZ=Europe/London"); 

require ("classes/core.class.php");
require ("config.php");

require ("classes/db.class.php");


$core = new core();
$core->db = new db();

core::init();

?>