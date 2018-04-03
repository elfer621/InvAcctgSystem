<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
if($_POST){
	$info = $db->getWHERE("*","tbl_customers","where cust_id='{$_REQUEST['business_name']}'");
	//Journal Saving
	$glref = isset($_REQUEST['jrefid'])&&$_REQUEST['jrefid']!=0?$_REQUEST['jrefid']:$con->getNextJournalID();
	$sql="insert into tbl_vouchering (id,date,type,payee,remarks,particular_array,amount_array,total,preparedby,`status`) values 
	('$glref','{$_REQUEST['xdate']}','Sales Journal','','{$info['customer_name']}','','','".str_replace( ',', '', $_REQUEST['xtotal'])."','".$_SESSION['xid']."','ForApproval') 
	on duplicate key update `date`=values(`date`),type=values(type),payee=values(payee),remarks=values(remarks),particular_array=values(particular_array),amount_array=values(amount_array),total=values(total)";
	$jrefid = $con->insertSJ($glref,$sql,$_REQUEST['xdate'],array('code'=>$_REQUEST['code'],'desc'=>$_REQUEST['desc'],'dr_amt'=>$_REQUEST['dr_amt'],'cr_amt'=>$_REQUEST['cr_amt']));
	//Journal Saving
	
	$sql="insert into tbl_soa (cust_id,year,month,date,amount,due_date,vouchering_ref) values 
		('{$_REQUEST['business_name']}','{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['xdate']}','".str_replace(",","",$_REQUEST['xtotal'])."','{$_REQUEST['due_date']}','$jrefid') 
		on duplicate key update due_date=values(due_date),amount=values(amount)";
	$qry = mysql_query($sql);
	if($qry){
		$soa_num=$_SESSION["soa"]['soanum']?$_SESSION["soa"]['soanum']:mysql_insert_id();
		if($_SESSION["soa"]['soanum']){
			$s0="delete from tbl_soa_prev_bal where soa_num='$soa_num' and cust_id='{$_REQUEST['business_name']}'";
			$s1="delete from tbl_soa_current_charges where soa_num='$soa_num' and cust_id='{$_REQUEST['business_name']}'";
			$s2="delete from tbl_soa_reimbursable_charges where soa_num='$soa_num' and cust_id='{$_REQUEST['business_name']}'";
			$s3="delete from tbl_soa_other_charges where soa_num='$soa_num' and cust_id='{$_REQUEST['business_name']}'";
			$del0=mysql_query($s0);
			if(!$del0){
				echo $s0."<br/>";
				echo "Error Del 0:".mysql_error()."<br/>";
			}
			$del1=mysql_query($s1);
			if(!$del1){
				echo $s1."<br/>";
				echo "Error Del 1:".mysql_error()."<br/>";
			}
			$del2=mysql_query($s2);
			if(!$del2){
				echo $s2."<br/>";
				echo "Error Del 2:".mysql_error()."<br/>";
			}
			$del3=mysql_query($s3);
			if(!$del3){
				echo $s3."<br/>";
				echo "Error Del 3:".mysql_error()."<br/>";
			}
		}
		if($soa_num!=0){
			for($x=0;$x<count($_REQUEST['prev_particular']);$x++){
				$prev.="('$soa_num','{$_REQUEST['business_name']}','{$_REQUEST['prev_particular'][$x]}','".str_replace(",","",$_REQUEST['prev_amt'][$x])."','{$_REQUEST['prev_class'][$x]}'),";
			}
			for($x=0;$x<count($_REQUEST['cc_particular']);$x++){
				$cc.="('$soa_num','{$_REQUEST['business_name']}','{$_REQUEST['cc_particular'][$x]}','".str_replace(",","",$_REQUEST['cc_amt'][$x])."','{$_REQUEST['cc_class'][$x]}'),";
			}
			for($x=0;$x<count($_REQUEST['rc_details']);$x++){
				$rc.="('$soa_num','{$_REQUEST['business_name']}','{$_REQUEST['xdate']}','{$_REQUEST['rc_details'][$x]}','{$_REQUEST['rc_previous'][$x]}','{$_REQUEST['rc_present'][$x]}','{$_REQUEST['rc_reading_area'][$x]}','{$_REQUEST['rc_rate'][$x]}','".str_replace(",","",$_REQUEST['rc_amount_due'][$x])."','{$_REQUEST['rc_class'][$x]}'),";
			}
			for($x=0;$x<count($_REQUEST['oc_details']);$x++){
				$oc.="('$soa_num','{$_REQUEST['business_name']}','{$_REQUEST['xdate']}','{$_REQUEST['oc_details'][$x]}','".str_replace(",","",$_REQUEST['oc_amount'][$x])."','{$_REQUEST['oc_class'][$x]}'),";
			}
			$sql_prev = "insert into tbl_soa_prev_bal (soa_num,cust_id,details,amount,class) values $prev;";
			$sql_prev = str_replace(",;","",$sql_prev);
			$qry_prev = mysql_query($sql_prev);
			if(!$qry_prev){
				echo "Error PREV:(".$sql_prev.")".mysql_error()."<br/>";
			}
			$sql_cc = "insert into tbl_soa_current_charges (soa_num,cust_id,details,amount,class) values $cc;";
			$sql_cc = str_replace(",;","",$sql_cc);
			$qry_cc = mysql_query($sql_cc);
			if(!$qry_cc){
				echo "Error CC:(".$sql_cc.")".mysql_error()."<br/>";
			}
			$sql_rc = "insert into tbl_soa_reimbursable_charges (soa_num,cust_id,date,details,previous,present,reading_area,rate,amount_due,class) values $rc;";
			$sql_rc = str_replace(",;","",$sql_rc);
			$qry_rc = mysql_query($sql_rc);
			if(!$qry_rc){
				echo "Error RC:(".$sql_rc.")".mysql_error()."<br/>";
			}
			$sql_oc = "insert into tbl_soa_other_charges (soa_num,cust_id,date,details,amount,class) values $oc;";
			$sql_oc = str_replace(",;","",$sql_oc);
			$qry_oc = mysql_query($sql_oc);
			if(!$qry_oc){
				echo "Error OC:(".$sql_oc.")".mysql_error()."<br/>";
			}
			//Insert Customer Transaction
			$sql_cust_trans = "insert into tbl_customers_trans (cust_id,receipt,date,transtype,details,amount) 
				values ('{$_REQUEST['business_name']}','$soa_num','{$_REQUEST['xdate']}','BILLING','Statement No.:$soa_num','".str_replace( ',', '', $_REQUEST['xtotal'])."') 
				on duplicate key update cust_id=values(cust_id),date=values(date),transtype=values(transtype),details=values(details),amount=values(amount)";
			$qry_cust_trans = mysql_query($sql_cust_trans);
			if(!$qry_cust_trans){
				echo "Error CustTrans:".mysql_error()."<br/>";
			}
			//Insert Customer Transaction
			unset($_POST);
		}
	}else{
		echo mysql_error();
	}
	unset($_SESSION["soa"]);
}
?>