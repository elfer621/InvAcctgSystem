<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
error_reporting(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
switch($_REQUEST['execute']){
	case'view_listing':
		include_once"view_soa.php";
	break;
	case'viewSOA':
		unset($_SESSION["soa"]);
		$_SESSION["soa"]['soanum']=$_REQUEST['refid'];
		echo "success";
	break;
}