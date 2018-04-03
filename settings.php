<?php
//unset($_SESSION);
$_SESSION['default_db']= 'rtk_db';
$_SESSION['default_ip']= $_SERVER['SERVER_NAME'];
$dbuser="admin";
$dbpass="webadmin2010";
$conn = new PDO("mysql:host=".$_SESSION['default_ip'].";dbname=".$_SESSION['default_db']."", $dbuser, $dbpass);
if(!isset($_SESSION['settings'])){
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $conn->prepare("select * from settings"); 
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	foreach($stmt->fetchAll() as $key => $val){
		$_SESSION['settings'][$val['variable_name']]=$val['variable_values'];
	}
	//print_r($settings);
	if($_SESSION['settings']['connection_type']=="multiple"){
		$stmt = $conn->prepare("select * from settings_connections"); 
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach($stmt->fetchAll() as $key => $val){
			$_SESSION['conlist'][$val['con_name']] = $val;
		}
	}
	

}
if(!isset($_SESSION['reports'])){
//Reports
	$stmt = $conn->prepare("select * from settings_reports"); 
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	foreach($stmt->fetchAll() as $key => $val){
		$_SESSION['reports'][$val['report_name']] = $val;
	}
}

//$_SESSION['settings']['system_name']="Hotel";
//print_r($_SESSION['reports']);
//print_r($_SESSION);
// exit;
// echo "<pre>";
// print_r($_SERVER['SERVER_NAME']);
// echo "</pre>";
$conn = null;

// print_r($_SESSION['settings']['howmanycounter']);
// echo count($conresult);
date_default_timezone_set("Asia/Manila");
if($_SESSION['restrictionid']==3){
	$transtype = "order";
}else{
	$transtype = "sales";
}
// view 1 -> Normal POS
// view 2 -> WRFA Settings
// view 3 -> Marina Settings
// view 4 -> La Marea (Restaurant Settings)
// view 5 -> LizGan Settings (Books)
// view 6 -> Gango Corps
// view 7 -> Hotel
// view 8 -> Clinic Lab
$view=$_SESSION['settings']['view']?$_SESSION['settings']['view']:5;
$allow_negative_inv=$_SESSION['settings']['allow_negative_inv'];
$allow_price_below_cost = $_SESSION['settings']['allow_price_below_cost'];
$auto_barcode=true;

/*On recent versions of Firefox (since 19), you have to disable the bugged and 
native PDF viewer (pdf.js) in about:config. Set the pdfjs.disabled property to true 
and you will see the print window appearing using your script.*/
//print.always_print_silent and set it to true
//$receipt_type = "./reports/receipt_tra_pdf.php?receipt_num=";

//if($_SESSION['more_info']['location']=="OutOfTown(DR)"){
	//$receipt_type = "./reports/receipt_dr.php?receipt_num=";
//}else{
	//$receipt_type = "./reports/receipt_tra.php?receipt_num=";
	//$receipt_type = "./reports/receipt.php?receipt_num=";
	//$receipt_type = "./reports/receipt_large.php?receipt_num=";
	//$receipt_type = "./reports/receipt_lx300.php?receipt_num=";
//}
//$reading_type = "./reports/reading_end_lx300.php?readingnum=";
//$reading_type = "./reports/reading_end.php?readingnum=";
$receipt_type = "./reports/{$_SESSION['reports']['Receipt']['report_link']}?receipt_num=";
$percent_qualify=.03;
$points_divisor=50; //for shoppers card
$dept_id=2;
$ask_cashdetails=true;
$search_list_show_all_uom=true;
$show_zero_inv=true;
$required_customer_name=true;
$allow_cost_markup=0;
$discounting_restricted=false;
$print_productsold=false;
$mode=$_SESSION['settings']['mode']; //main,branch
global $sessiontype;
if($mode=="branch"){
	$_SESSION['connect']=$_SESSION['settings']['session_connect'];
}
// switch($_SERVER['REMOTE_ADDR']){
	// case'192.168.10.87':
		// $_SESSION['counter_num']=1;
		// $serial='6VMGVQGQ';
		// $permit='0411-082-95150-002';
		// $machine='110222710';
	// break;
	// case'192.168.10.226':
		// $_SESSION['counter_num']=2;
		// $serial='WCC2EV083555';
		// $permit='0314-082-182451-002';
		// $machine='140349156';
	// break;
	// case'192.168.10.225':
		// $_SESSION['counter_num']=3;
		// $serial='6VMEJ52P';
		// $permit='0411-082-95149-002';
		// $machine='110222709';
	// break;
	// case'192.168.10.224':
		// $_SESSION['counter_num']=4;
		// $serial='6VMGVQNH';
		// $permit='0411-082-95148-002';
		// $machine='110222708';
	// break;
	// default:
		// $_SESSION['counter_num']=5;
		// $serial='0000000';
		// $permit='0000000';
		// $machine='000000';
	// break;
// }
if($_SESSION['settings'][$_SERVER['REMOTE_ADDR']]){
	$rec = explode(",",$_SESSION['settings'][$_SERVER['REMOTE_ADDR']]);
	$_SESSION['counter_num']=$rec[0];
	$serial=$rec[1];
	$permit=$rec[2];
	$machine=$rec[3];
}else{
	$_SESSION['counter_num']=1;
	$serial='0000000';
	$permit='0000000';
	$machine='000000';
}
$howmanycounter=$_SESSION['settings']['howmanycounter'];
$receipt_header = "<span style='font-size:18px;font-weight:bold;'>LIZGAN DISTRIBUTORS, INC.</span><br/>
		Operated By: LIZGAN DIST., INC.<br/>
		UC-METC, ALUMNOS, MAMBALING, CEBU<br/>
		Tel. Num. 417-7738<br/>
		TIN: 293-592-767-001<br/>
		Permit: $permit<br/>
		Serial: $serial Machine: $machine<br/>
		<div style='clear:both;height:30px;'></div>
		";
$branches = array(1=>"UC-Banilad",2=>"UC-LM",3=>"UC-Main",4=>"UC-Mambaling",5=>"Warehouse");
$branchesid = array('ucbanilad'=>1,'uclm'=>2,'ucmain'=>3,'ucmambaling'=>4,'warehouse'=>5);
switch($_SESSION['connect']){ //used for per branch stocking
	case'uclm':
		unset($branches[2]);
	break;
	case'ucmambaling':
		unset($branches[4]);
	break;
	case'ucmain':
		unset($branches[3]);
	break;
	case'ucbanilad':
		unset($branches[1]);
	break;
	case'warehouse':
		unset($branches[5]);
	break;
}

// $config = parse_ini_file("d:/pos_settings.ini");
// if(!$config){
	// echo "Configuration settings not found...";
	// exit;
// }
//echo getHostByName(getHostName());

// function GetVolumeLabel($drive) {
	   // if (preg_match('#Volume Serial Number is (.*)\n#i', shell_exec('dir '.$drive.':'), $m)) {
		  // $volname = ' ('.$m[1].')';
	   // } else {
		   // $volname = '';
	   // }
   // return $volname;
// }
// $serial = trim(str_replace("(","",str_replace(")","",GetVolumeLabel("c"))));
// echo $_SERVER['REMOTE_ADDR'];

?>
