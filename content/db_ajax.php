<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
//set_time_limit(0);
session_start();
header("Access-Control-Allow-Origin: *");
require_once"../settings.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
switch($_REQUEST['execute']){
	case'sendInvToAdmin':
		echo "InventoryUpdated: ".$con->updateInv()."\n";
		echo "InvSendToAdmin: ".$con->SendInvToAdmin($_SESSION['connect'])."\n";
		echo "JournalHeader: ".$con->sendTbl("tbl_vouchering")."\n";
		echo "JournalEntry: ".$con->sendTbl("tbl_journal_entry")."\n";
	break;
	case'updateItems':
		// echo "ProductName:".$con->updateItems("tbl_product_name")."\n";
		// echo "Barcode:".$con->updateItems("tbl_barcodes")."\n";
		// echo "Packages:".$con->updateItems("tbl_packages")."\n";
		
		echo "ProductName:".$con->copyTbl("tbl_product_name")."\n";
		echo "Barcode:".$con->copyTbl("tbl_barcodes")."\n";
		echo "Packages:".$con->copyTbl("tbl_packages")."\n";
		echo "Category:".$con->copyTbl("tbl_category")."\n";
		echo "ChartOfAccount:".$con->copyTbl("tbl_chart_of_account")."\n";
		echo "Supplier:".$con->copyTbl("tbl_supplier")."\n";
		if($mode!='main'){
			echo "InvAllBranch:".$con->copyTbl("tbl_inv_allbranch")."\n";
		}
	break;
	case'updateOthers':
		echo "Supplier:".$con->updateItems("tbl_supplier")."\n";
		if($_SESSION['connect']!="warehouse"){
			echo "DataCollege:".$con->updateItems("data_college_{$_SESSION['connect']}")."\n";
			echo "DataElem:".$con->updateItems("data_elem_{$_SESSION['connect']}")."\n";
			echo "DataHighSch:".$con->updateItems("data_highsch_{$_SESSION['connect']}")."\n";
		}
	break;
}
?>