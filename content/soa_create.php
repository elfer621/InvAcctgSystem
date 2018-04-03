<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$sessiontype = $_REQUEST['page'];

$month = array('01'=>'(01) January','02'=>'(02) February','03'=>'(03) March','04'=>'(04) April','05'=>'(05) May',
	'06'=>'(06) June','07'=>'(07) July','08'=>'(08) August','09'=>'(09) September','10'=>'(10) October','11'=>'(11) November','12'=>'(12) December');
$output_tax = 0;
if($_POST){
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;
	$info = $db->getWHERE("a.*,b.*","tbl_customers a left join tbl_customers_contract b on a.cust_id=b.custid","where a.cust_id='{$_REQUEST['business_name']}'");
	//Journal Saving
	$glref = isset($_REQUEST['jrefid'])&&$_REQUEST['jrefid']!=0?$_REQUEST['jrefid']:$con->getNextJournalID('SJ');
	
	// $sql="insert into tbl_vouchering (id,date,type,payee,remarks,particular_array,amount_array,total,preparedby,`status`) values 
	// ('$glref','{$_REQUEST['xdate']}','SJ','','{$info['customer_name']}','','','".str_replace( ',', '', $_REQUEST['xtotal'])."','".$_SESSION['xid']."','ForApproval') 
	// on duplicate key update `date`=values(`date`),type=values(type),payee=values(payee),remarks=values(remarks),particular_array=values(particular_array),amount_array=values(amount_array),total=values(total)";
	$data=array(
		'id'=>$glref,
		'date'=>$_REQUEST['xdate'],
		'type'=>'SJ',
		'payee'=>$info['customer_name'],
		'remarks'=>'',
		'particular_array'=>'',
		'amount_array'=>'',
		'total'=>str_replace( ',', '', $_REQUEST['xtotal']),
		'preparedby'=>$_SESSION['xid'],
		'status'=>'ForApproval'
	);
	$sql = $db->genSqlInsert($data,'tbl_vouchering');
	
	for($x=0;$x<count($_REQUEST['code']);$x++){
		if($db->strpos_arr($_REQUEST['desc'][$x],array("ACCOUNTS RECEIVABLE")) !== false){
			$entry[]=array(
				'account_code'=>$_REQUEST['code'][$x],
				'account_desc'=>$_REQUEST['desc'][$x],
				'dr'=>str_replace( ',', '', $_REQUEST['dr_amt'][$x]),
				'cr'=>str_replace( ',', '', $_REQUEST['cr_amt'][$x]),
				'type'=>'SJ',
				'ar_refid'=>$_REQUEST['business_name']
			);
		}else{
			$entry[]=array(
				'account_code'=>$_REQUEST['code'][$x],
				'account_desc'=>$_REQUEST['desc'][$x],
				'dr'=>str_replace( ',', '', $_REQUEST['dr_amt'][$x]),
				'cr'=>str_replace( ',', '', $_REQUEST['cr_amt'][$x]),
				'type'=>'SJ'
			);
		}
	}
	$jrefid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry);
	//Journal Saving
	// $sql="insert into tbl_soa (cust_id,year,month,date,amount,due_date,vouchering_ref) values 
		// ('{$_REQUEST['business_name']}','{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['xdate']}','".str_replace(",","",$_REQUEST['xtotal'])."','{$_REQUEST['due_date']}','$jrefid') 
		// on duplicate key update date=values(date),due_date=values(due_date),amount=values(amount)";
	$data=array(
		'cust_id'=>$_REQUEST['business_name'],
		'year'=>$_REQUEST['year'],
		'month'=>$_REQUEST['month'],
		'date'=>$_REQUEST['xdate'],
		'amount'=>str_replace(",","",$_REQUEST['xtotal']),
		'due_date'=>$_REQUEST['due_date'],
		'vouchering_ref'=>$jrefid
	);
	$sql = $db->genSqlInsert($data,'tbl_soa');
	$qry = mysql_query($sql);
	if($qry){
		$soa_num=$_SESSION["soa"]['soanum']?$_SESSION["soa"]['soanum']:mysql_insert_id();
		$s0="delete from tbl_soa_prev_bal where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='{$_REQUEST['business_name']}'"; 
		$s1="delete from tbl_soa_current_charges where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='{$_REQUEST['business_name']}'";
		$s2="delete from tbl_soa_reimbursable_charges where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='{$_REQUEST['business_name']}'";
		
		$del0=@mysql_query($s0);
		if(!$del0){
			echo $s0."<br/>";
			echo "Error Del 0:".mysql_error()."<br/>";
		}
		$del1=@mysql_query($s1);
		if(!$del1){
			echo $s1."<br/>";
			echo "Error Del 1:".mysql_error()."<br/>";
		}
		$del2=@mysql_query($s2);
		if(!$del2){
			echo $s2."<br/>";
			echo "Error Del 2:".mysql_error()."<br/>";
		}
		
		
			for($x=0;$x<count($_REQUEST['prev_particular']);$x++){
				$prev.="('{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['business_name']}','{$_REQUEST['prev_particular'][$x]}','".str_replace(",","",$_REQUEST['prev_amt'][$x])."','{$_REQUEST['prev_class'][$x]}'),";
			}
			for($x=0;$x<count($_REQUEST['cc_particular']);$x++){
				$cc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['business_name']}','{$_REQUEST['cc_particular'][$x]}','".str_replace(",","",$_REQUEST['cc_amt'][$x])."','{$_REQUEST['cc_class'][$x]}'),";
			}
			for($x=0;$x<count($_REQUEST['rc_details']);$x++){
				$rc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['business_name']}','{$_REQUEST['xdate']}','{$_REQUEST['rc_details'][$x]}','{$_REQUEST['rc_previous'][$x]}','{$_REQUEST['rc_present'][$x]}','{$_REQUEST['rc_reading_area'][$x]}','{$_REQUEST['rc_rate'][$x]}','".str_replace(",","",$_REQUEST['rc_amount_due'][$x])."','{$_REQUEST['rc_class'][$x]}'),";
			}
			if($_REQUEST['oc_details']){
				$s3="delete from tbl_soa_other_charges where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='{$_REQUEST['business_name']}'";
				$del3=@mysql_query($s3);
				if(!$del3){
					echo $s3."<br/>";
					echo "Error Del 3:".mysql_error()."<br/>";
				}
				for($x=0;$x<count($_REQUEST['oc_details']);$x++){
					$oc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['business_name']}','{$_REQUEST['xdate']}','{$_REQUEST['oc_details'][$x]}','".str_replace(",","",$_REQUEST['oc_amount'][$x])."','{$_REQUEST['oc_class'][$x]}'),";
				}
				$sql_oc = "insert into tbl_soa_other_charges (year,month,cust_id,date,details,amount,class) values $oc; on duplicate key update details=values(details),amount=values(amount),class=values(class)";
				$sql_oc = str_replace(",;","",$sql_oc);
				$qry_oc = mysql_query($sql_oc);
				if(!$qry_oc){
					echo "Error OC:(".$sql_oc.")".mysql_error()."<br/>";
				}
			}
			if($_REQUEST['nvc_particular']){
				$s4="delete from tbl_soa_nonvat where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='{$_REQUEST['business_name']}'";
				$del4=@mysql_query($s4);
				if(!$del4){
					echo $s4."<br/>";
					echo "Error Del 4:".mysql_error()."<br/>";
				}
				for($x=0;$x<count($_REQUEST['nvc_particular']);$x++){
						$nvc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','{$_REQUEST['business_name']}','{$_REQUEST['xdate']}','{$_REQUEST['nvc_particular'][$x]}','".str_replace(",","",$_REQUEST['nvc_amt'][$x])."','{$_REQUEST['nvc_class'][$x]}'),";
				}
				$sql_nvc = "insert into tbl_soa_nonvat (year,month,cust_id,date,details,amount,class) values $nvc; on duplicate key update details=values(details),amount=values(amount),class=values(class)";
				$sql_nvc = str_replace(",;","",$sql_nvc);
				$qry_nvc = mysql_query($sql_nvc);
				if(!$qry_nvc){
					echo "Error NVC:(".$sql_nvc.")".mysql_error()."<br/>";
				}
			}
			
			$sql_prev = "insert into tbl_soa_prev_bal (year,month,cust_id,details,amount,class) values $prev; on duplicate key update details=values(details),amount=values(amount),class=values(class)";
			$sql_prev = str_replace(",;","",$sql_prev);
			$qry_prev = mysql_query($sql_prev);
			if(!$qry_prev){
				echo "Error PREV:(".$sql_prev.")".mysql_error()."<br/>";
			}
			$sql_cc = "insert into tbl_soa_current_charges (year,month,cust_id,details,amount,class) values $cc; on duplicate key update details=values(details),amount=values(amount),class=values(class)";
			$sql_cc = str_replace(",;","",$sql_cc);
			$qry_cc = mysql_query($sql_cc);
			if(!$qry_cc){
				echo "Error CC:(".$sql_cc.")".mysql_error()."<br/>";
			}
			$sql_rc = "insert into tbl_soa_reimbursable_charges (year,month,cust_id,date,details,previous,present,reading_area,rate,amount_due,class) values $rc; on duplicate key update details=values(details),previous=values(previous),present=values(present),reading_area=values(reading_area),rate=values(rate),amount_due=values(amount_due),class=values(class)";
			$sql_rc = str_replace(",;","",$sql_rc);
			$qry_rc = mysql_query($sql_rc);
			if(!$qry_rc){
				echo "Error RC:(".$sql_rc.")".mysql_error()."<br/>";
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
			//unset($_POST);
		
	}else{
		echo mysql_error();
	}
	//unset($_SESSION["soa"]);
}
//put this before generate SOA to get prev balance
$dt = $_REQUEST['month']?explode("-",date('Y-m',strtotime("2017-".$_REQUEST['month']))):explode("-",date('Y-m'));
//put this before generate SOA to get prev balance
if($_REQUEST['gensoa']){ //Generate New SOA
	$stat=true;
	unset($_SESSION["soa"]);
	$refid=$_REQUEST['cust_id'];
	// $info= $db->getWHERE("cust.*,b.*,prev.amount,prev.id as soaref,payment.total_payment","tbl_customers cust 
		// left join tbl_customers_contract b on cust.cust_id=b.custid 
		// left join (select * from tbl_soa where cust_id='{$refid}' and year='".$dt[0]."' and month='".($dt[1]-1)."') prev on cust.cust_id=prev.cust_id 
		// left join (select cust_id,sum(amount) total_payment from tbl_customers_trans where cust_id='{$refid}' and transtype='Payment' and date_format(date,'%Y%m')='".$dt[0].$db->customeFormat(($dt[1]-1),2)."' group by date_format(date,'%Y%m')) payment on cust.cust_id=payment.cust_id",
		// "where cust.cust_id='{$refid}'");
	$info= $db->getWHERE("cust.*,b.*,prev.amount,prev.id as soaref,prev.due_date,payment.total_payment,payment.receipts,prev_rc.present elect_prev",
		"tbl_customers cust 
		left join tbl_customers_contract b on cust.cust_id=b.custid 
		left join (select * from tbl_soa where cust_id='{$refid}' and year='".$dt[0]."' and month='".($dt[1]-1)."') prev on cust.cust_id=prev.cust_id 
		left join (select cust_id,sum(total_amount) total_payment,group_concat(receipt) receipts from tbl_receipt_manual where cust_id='{$refid}' and date_format(date,'%Y%m')='".$dt[0].$db->customeFormat(($dt[1]-1),2)."' and (`status` <> 'CANCELED' or `status` is null) group by date_format(date,'%Y%m')) payment on cust.cust_id=payment.cust_id
		left join (select * from tbl_soa_reimbursable_charges where cust_id='{$refid}' and year='{$dt[0]}' and month='".($dt[1]-1)."' and details='Electricity') prev_rc on cust.cust_id=prev_rc.cust_id",
		"where cust.cust_id='{$refid}'");
	$pdc=$db->resultArray("*","tbl_soa_pdclist","where custid='{$refid}' and monthrent='".$dt[0]."-".$dt[1]."' order by id asc");
	$prev[]=array('details'=>'Prev Acct Bal REF #'.$db->customeFormat($info['soaref'],6)." DueDate: {$info['due_date']}",'amount'=>$info['amount'],'class'=>'amt');
	$prev[]=array('details'=>'Adjustment','amount'=>0,'class'=>'deduction');
	$prev[]=array('details'=>'Payment Received OR# '.$info['receipts'],'amount'=>$info['total_payment'],'class'=>'deduction');
	if(($info['amount']-$info['total_payment'])>0){
		//$numdays = date('Y-m-d',strtodate($info['due_date']))-date('Y-m-d',strtodate());
		$prev[]=array('details'=>'Penalty 5%','amount'=>(($info['amount']-$info['total_payment'])*.05),'class'=>'amt');
	}
	if(date('Ym',strtotime($info['contract_end_date']))>date('Ym',mktime(0,0,0,$_REQUEST['month'],1,date('Y')))){
		$totalrent_amt = $info['fixed_monthly_rental'];
		$cc[] = array('details'=>"Month of ".date('F, Y',strtotime($dt[0]."-".$db->customeFormat(($dt[1]),2)."-01"))." Rental",'amount'=>($totalrent_amt),'class'=>'cc_rental');
	}else if(date('Ym',strtotime($info['contract_end_date']))<=date('Ym',mktime(0,0,0,$_REQUEST['month'],1,date('Y')))){
		$numDays = cal_days_in_month (CAL_GREGORIAN, date('m',strtotime($info['contract_end_date'])),date('Y',strtotime($info['contract_end_date'])));
		$oldrent_perday = $info['fixed_monthly_rental']/$numDays;
		$oldrent_days = date('d',strtotime($info['contract_end_date']));
		$newrent_perday = ($info['fixed_monthly_rental']*1.05)/$numDays;
		$newrent_days = ($numDays-date('d',strtotime($info['contract_end_date'])));
		$oldrent = $oldrent_days * $oldrent_perday;
		$newrent = $newrent_days*$newrent_perday;
		
		// echo "(".date('d',strtotime($info['contract_end_date'])).")".$oldrent."<br/>";
		// echo "(".($numDays-date('d',strtotime($info['contract_end_date']))).")".$newrent."<br/>";
		// echo $oldrent+$newrent;
		// exit;
		$cc[] = array('details'=>"Month of ".date('F, Y',strtotime($dt[0]."-".$db->customeFormat(($dt[1]),2)."-01"))." Rental (Old Rate) x $oldrent_days days",'amount'=>($oldrent),'class'=>'cc_rental');
		$cc[] = array('details'=>"Month of ".date('F, Y',strtotime($dt[0]."-".$db->customeFormat(($dt[1]),2)."-01"))." Rental (New Rate) x $newrent_days days",'amount'=>($newrent),'class'=>'cc_rental');
		$totalrent_amt = $oldrent+$newrent;
	}
	
	
	$cc[] = array('details'=>"Less: Discount",'amount'=>(0));
	$cc[] = array('details'=>"Net Rental Due Before Tax",'amount'=>($totalrent_amt),'class'=>'amt');
	$cc[] = array('details'=>"Add: 12% VAT",'amount'=>($totalrent_amt*.12),'class'=>'amt outputtax');
	$cc[] = array('details'=>"Less: 5% Withholding Tax",'amount'=>($totalrent_amt*.05),'class'=>'deduction');
	
	$rc[] = array(
				'details'=>'Electricity',
				'previous'=>$info['elect_prev'],
				'present'=>'',
				'reading_area'=>$info['floor_area_sqm'],
				'rate'=>$info['elect_rate'],
				'amount_due'=>$info['floor_area_sqm']*$info['elect_rate'],
				'class'=>'utilities'
				);
	$rc[] = array(
				'details'=>'Pest Control',
				'previous'=>'',
				'present'=>'',
				'reading_area'=>$info['floor_area_sqm'],
				'rate'=>$info['others_pestcontrol_percent'],
				'amount_due'=>$info['floor_area_sqm']*$info['others_pestcontrol_percent'],
				'class'=>'utilities'
				);
	$total['rc']+=array_sum(array_map(function($var) {return $var['amount_due'];}, $rc));
	$rc[] = array(
				'details'=>'Add: 12% VAT',
				'previous'=>'',
				'present'=>'',
				'reading_area'=>'',
				'rate'=>'',
				'amount_due'=>($total['rc']*.12),
				'class'=>'outputtax'
				);
	
	if($info['water_rate']!=0){
		$oc[] = array('details'=>'WATER','amount'=>($info['floor_area_sqm']*$info['water_rate']),'class'=>'utilities');
	}
	if($info['others_cusa_rate']!=0){
		$oc[] = array('details'=>'CUSA','amount'=>($info['floor_area_sqm']*$info['others_cusa_rate']),'class'=>'other_income');
	}
	$rec = $db->resultArray("*","tbl_customers_contract_recurring_charges","where custid='$refid'");
	foreach($rec as $key => $val){
		$oc[] = array('details'=>$val['charge_description'],'amount'=>$val['amount'],'class'=>'other_income');
	}
	
	$rec = $db->resultArray("sum(amount) total_amt,b.Description","tbl_soa_other_charges_daily a 
		left join tbl_soa_other_charges_category_name b on a.category_id=b.category_id","where custid='{$refid}' and forthemonth='".$dt[0]."-".$dt[1]."' group by a.category_id");
	foreach($rec as $key => $val){
		$oc[] = array('details'=>$val['Description'],'amount'=>$val['total_amt'],'class'=>'other_income');
	}
	$total['oc']+=array_sum(array_map(function($var) {return $var['amount'];}, $oc));
	$oc[] = array('details'=>'Add: 12% VAT','amount'=>($total['oc']*.12),'class'=>'outputtax');
	
	$journal[]=array(
				'account_code'=>'1201',
				'account_desc'=>'ACCOUNTS RECEIVABLE',
				'dr'=>number_format($cc[0]['amount']*1.07,2),
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'1228',
				'account_desc'=>'CREDITABLE TAX WITHHELD',
				'dr'=>number_format($cc[4]['amount'],2),
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'2026',
				'account_desc'=>'OUTPUT TAX',
				'dr'=>0,
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'4000',
				'account_desc'=>'RENT REVENUE',
				'dr'=>0,
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'4004',
				'account_desc'=>'OTHER INCOME',
				'dr'=>0,
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'4004A',
				'account_desc'=>'INCOME FROM UTILITIES',
				'dr'=>0,
				'cr'=>0
				);
	$journal[]=array(
				'account_code'=>'4400',
				'account_desc'=>'MARKETING FEE',
				'dr'=>0,
				'cr'=>0
				);
}elseif($_SESSION["soa"]['soanum']){ //View or Edit SOA
	$rec=$db->getWHERE("*","tbl_soa","where id='{$_SESSION["soa"]['soanum']}'");
	$refid=$rec['cust_id'];
	// $info= $db->getWHERE("cust.*,prev.amount,prev.id as soaref","tbl_customers cust left join 
		// (select * from tbl_soa where cust_id='{$refid}' and year='".$dt[0]."' and month='".$dt[1]."') prev on cust.cust_id=prev.cust_id",
		// "where cust.cust_id='{$refid}'");
	$info= $db->getWHERE("cust.*,b.*,prev.amount,prev.id as soaref,prev.due_date",
		"tbl_customers cust 
		left join tbl_customers_contract b on cust.cust_id=b.custid 
		left join (select * from tbl_soa where cust_id='{$refid}' and year='".$dt[0]."' and month='".($dt[1]-1)."') prev on cust.cust_id=prev.cust_id 
		",
		"where cust.cust_id='{$refid}'");
	$voucher = $db->getWHERE("a.*,b.user certifiedby,c.user approver",
		"tbl_vouchering a left join tbl_user b on a.certifiedcorrect=b.id left join tbl_user c on a.approvedby=c.id",
		"where a.id='{$rec['vouchering_ref']}'");
	$prev=$db->resultArray("*","tbl_soa_prev_bal","where cust_id='{$rec['cust_id']}' and year='{$rec['year']}' and month='{$rec['month']}' order by id asc");
	$cc=$db->resultArray("*","tbl_soa_current_charges","where cust_id='{$rec['cust_id']}' and year='{$rec['year']}' and month='{$rec['month']}' order by id asc");
	$rc=$db->resultArray("*","tbl_soa_reimbursable_charges","where cust_id='{$rec['cust_id']}' and year='{$rec['year']}' and month='{$rec['month']}' order by id asc");
	$oc=$db->resultArray("*","tbl_soa_other_charges","where cust_id='{$rec['cust_id']}' and year='{$rec['year']}' and month='{$rec['month']}' order by id asc");
	$nvc=$db->resultArray("*","tbl_soa_nonvat","where cust_id='{$rec['cust_id']}' and year='{$rec['year']}' and month='{$rec['month']}' order by id asc");
	$pdc=$db->resultArray("*","tbl_soa_pdclist","where custid='{$rec['cust_id']}' and monthrent='".$rec['year']."-".$db->customeFormat($rec['month'],2)."' order by id asc");
	$journal=$db->resultArray("*","tbl_journal_entry","where refid='{$rec['vouchering_ref']}'");
	$stat=true;
}
//echo $_SESSION["soa"]['soanum'];
$xmonth=$_REQUEST['month']?$_REQUEST['month']:($rec['month']?$rec['month']:date('m')); //it should be at the end to assign $rec if any
?>
<style>
td > input {
	font-size:12px;
}
th {
	font-size:11px;
}
.navigateableMain td {
	padding: 0.15em 0.5em !important;
}
</style>
<link rel="stylesheet" href="./js/chosen/chosen.css">
<script src="./js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="./js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<div class="top">
	<form method="post" action="./?page=soa_create" name="frm_stockin" style="padding:10px 0 10px 0;">
		<div class="header" style="width:100% !important;">
			<div>
				<input type="hidden" name="jrefid" value="<?=$rec['vouchering_ref']?>"/>
				<div style="float:left;border:1px solid #000;margin-right:5px;padding:5px;width:605px;">
					<div style="float:left;margin-right:5px;">
						<div style="float:left;margin-right:5px;width:60px;">Business Name:</div>
						<div style="width:500px;float:left;">
							<select  name="business_name" id="business_name" data-placeholder="Choose Customer Name..." class="chosen-select"  tabindex="1">
								<option value=""></option>
								<?
								$qry =mysql_query("select * from tbl_customers");
								while($row=mysql_fetch_assoc($qry)){
									echo "<option ".($refid==$row['cust_id']?"selected":"")." value='{$row['cust_id']}'>{$row['customer_name']}</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;">
						<div style="float:left;margin-right:5px;">
							<fieldset>
								<legend>Statement for the Month Of</legend>
								<input type="text" name="year" id="year" value="<?=date('Y')?>" style="float:left;margin-right:5px;width:80px;"/>
								<select name="month" id="month" style="float:left;width:180px;">
								<?php
								foreach($month as $key=>$val){
									echo "<option ".($xmonth==$key?"selected":"")." value='{$key}'>{$val}</option>";
								}
								?>
								</select>
							</fieldset>
						</div>
						<div style="clear:both;height:5px;"></div>
						<input type="button" value="Generate SOA" onclick="generateSOA();" style="width:295px;height:40px;"/>
					</div>
					<div style="float:left;">
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:100px;">SOA #:</div>
							<input style="float:left;width:133px;" readonly type="text" name="refid" id="refid" value="<?=$rec['id']?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:100px;">Date:</div>
							<input style="float:left;width:133px;" type="text" name="xdate" id="xdate" value="<?=$rec['date']?$rec['date']:date('Y-m-d');?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:100px;">DueDate:</div>
							<input style="float:left;width:133px;" type="text" name="due_date" id="due_date" value="<?=$rec['due_date']?$rec['due_date']:date('Y-m-d',strtotime(date('m/d/Y')."+5 days")); //to change 7th day of the following month?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:100px;">Contract End:</div>
							<input style="float:left;width:133px;" type="text" name="contract_end" id="contract_end" value="<?=$info['contract_end_date'];?>" />
						</div>
					</div>
				</div>
				<fieldset>
					<legend><strong>TOTAL</strong></legend>
					<input readonly type="text" id="xtotal" name="xtotal" value="<?=($info['total']?number_format($info['total'],2):"0.00")?>" style="float:right;width:325px;font-size:55px;margin-top:10px;text-align:right;border:none;border-color:transparent;background:transparent;"/>
				</fieldset>
			</div>
		</div>
		<div class="content" style="min-height:300px;width:100%!important;">
			<div style="float:left;width:15%;text-align:center;">
				<fieldset style="padding:5px;text-align:center;">
					<span style="font-size:20px;"><strong>SOA</strong></span><br/>
					<span style="font-size:12px;color:red;"><?= "Branch: ".($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING");?></span>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<fieldset style="padding:5px;text-align:center;">
					<legend>&nbsp; MENU &nbsp;</legend>
					<?php
					if($voucher['approvedby']){
					?>
						<input id="bt6" class="buthov" type="button" value="Close" onclick="window.location.reload();" style="float:right;height:40px;width:100%;float:left;"/>
					<?}else{ ?>
						<button id="bt1" class="buthov" type="button" onclick="frmSave()" name="SaveRec" style="height:40px;width:95%;">Save</button>
					<?php } ?>
					<input id="bt5" class="buthov" type="button" value="View Records" onclick="viewSOA();" style="height:40px;width:95%;"/>
					<input id="bt7" class="buthov" type="button" value="Main Dashboard" onclick="window.location='index.php'" style="height:40px;width:95%;"/>
				</fieldset>
				<div style="clear:both;height:10px;"></div>
				<fieldset style="padding:5px;text-align:center;border-color:red;">
					<legend style="color:red;">&nbsp; VALIDATION &nbsp;</legend>
					<?php
					if($voucher['certifiedcorrect']){
						echo "<fieldset><legend style='text-align:left;'>CertifiedCorrect By:</legend>".$voucher['certifiedby']."</fieldset>";
					}else{ ?>
						<input id="bt8" class="buthov" type="button" value="CertifiedCorrect" onclick="validate(<?=$voucher['id']?>,'certifiedcorrect');" style="height:40px;width:95%;"/>
					<?php } ?>
					<?php
					if($voucher['approvedby']){
						echo "<fieldset><legend style='text-align:left;'>Approved By:</legend>".$voucher['approver']."</fieldset>";
					}else{ ?>
						<input id="bt9" class="buthov" type="button" value="Approved" onclick="validate(<?=$voucher['id']?>,'approvedby');" style="height:40px;width:95%;"/>
					<?php } ?>
					<input id="bt12" class="buthov" type="button" value="Reject" onclick="reject(<?=$voucher['id']?>)" style="height:40px;width:95%;"/>
				</fieldset>
				<?php if($voucher['approvedby']){ ?>
					<fieldset style="padding:5px;text-align:center;border-color:red;">
						<legend style="color:blue;">&nbsp; REPORTS &nbsp;</legend>
							<input id="bt10" class="buthov" type="button" value="Voucher" onclick="viewReport('./reports/vouchering.php?refid=<?php echo $voucher['id'] ?>')" style="height:40px;width:95%;"/>
					</fieldset>
				<?php } ?>
			</div>
			<div style="float:right;width:85%;">
				<div style="display: flex;">
					<fieldset style="align-items: stretch;float:left;width:50%;">
						<legend>Previous Account Balance 
							<input type="button" value="+" id="prev" style="width:20px;height:20px;"/>
							<input type="button" value="Penalty" id="penalty" onclick="penaltyCompute()" style="width:50px;height:20px;font-size:11px;"/>
						</legend>
						<table class="navigateableMain" id="prev_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;width:300px;">Particular</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($stat){
								
								foreach($prev as $key => $val){
									echo "<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='prev_particular[]' style='width:100%' value='{$val['details']}'/></td>
											<td>
												<input class='{$val['class']}' type='text'  name='prev_amt[]' style='width:100%;text-align:right;' value='".number_format($val['amount'],2)."'/>
												<input type='hidden' name='prev_class[]' value='{$val['class']}'/>
											</td>
										</tr>";
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
					<fieldset style="align-items: stretch;float:left;width:50%;">
						<legend>Current Charges <input type="button" value="+" id="cc" style="width:20px;height:20px;"/></legend>
						<table class="navigateableMain" id="cc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;width:250px;">Particular</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($stat){
								
								foreach($cc as $key => $val){
									echo "<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='cc_particular[]' style='width:100%' value='{$val['details']}'/></td>
											<td>
												<input class='{$val['class']}' type='text'  name='cc_amt[]' style='width:100%;text-align:right;' value='".number_format($val['amount'],2)."'/>
												<input type='hidden' name='cc_class[]' value='{$val['class']}'/>
											</td>
										</tr>";
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</div>
				<div style="clear:both;height:10px;"></div>
				<div style="display: flex;">
					<fieldset style="align-items: stretch;width:50%;float:left;">
						<legend>Reimbursable Charges <input type="button" value="+" id="rc" style="width:20px;height:20px;"/></legend>
						<table class="navigateableMain" id="rc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;" width="100px">Particular</th>
									<th style="border:none;">Previous</th>
									<th style="border:none;">Present</th>
									<th style="border:none;">Reading / Area</th>
									<th style="border:none;">Rate</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($stat){
								foreach($rc as $key=>$val){
									echo "<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='rc_details[]' style='width:100%;' value='{$val['details']}'/></td>
											<td><input type='text' name='rc_previous[]' style='width:100%;' value='{$val['previous']}'/></td>
											<td><input type='text' name='rc_present[]' style='width:100%;' value='{$val['present']}'/></td>
											<td><input type='text' name='rc_reading_area[]' style='width:100%;text-align:right;' value='{$val['reading_area']}'/></td>
											<td><input type='text' name='rc_rate[]' style='width:100%;text-align:right;' value='{$val['rate']}'/></td>
											<td>
												<input ".($val['details']=="Add: 12% VAT"?"id='rc_vat'":"")." type='text' class='amt {$val['class']}' name='rc_amount_due[]' style='width:100%;text-align:right;' value='{$val['amount_due']}'/>
												<input type='hidden' name='rc_class[]' value='{$val['class']}'/>
											</td>
										</tr>";
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
					<fieldset style="align-items: stretch;width:50%;float:left;">
						<legend>Other Charges <input type="button" value="+" id="oc" style="width:20px;height:20px;"/></legend>
						<table class="navigateableMain" id="oc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;width:250px;">Particular</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($stat){
								
								foreach($oc as $key=>$val){
									//if($val['details']=="Add: 12% VAT"){$output_tax+=$val['amount'];}
									echo "<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='oc_details[]' style='width:100%;' value='{$val['details']}'/></td>
											<td>
												<input type='text' class='amt {$val['class']}' name='oc_amount[]' style='width:100%;text-align:right;' value='".number_format($val['amount'],2)."'/>
												<input type='hidden' name='oc_class[]' value='{$val['class']}'/>
											</td>
										</tr>";
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</div>
				<div style="clear:both;height:10px;"></div>
				<div style="display: flex;">
					<fieldset style="align-items: stretch;width:50%;float:left;">
						<legend>PDC <input type="button" value="+" id="pdc" style="width:20px;height:20px;"/></legend>
						<table class="navigateableMain" id="pdc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;" width="150px">For the Month</th>
									<th style="border:none;" width="500px">Particular</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							foreach($pdc as $key => $val){
								echo "<tr>
										<td><input type='checkbox' ></td>
										<td>".$val['monthrent']."</td>
										<td>".$val['bank']." ".$val['checknum']." ".$val['checkdate']."</td>
										<td>".number_format($val['amount'],2)."</td>
									</tr>";
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
					<fieldset style="align-items: stretch;width:50%;float:left;">
						<legend>NonVat Charges <input type="button" value="+" id="nvc" style="width:20px;height:20px;"/></legend>
						<table class="navigateableMain" id="nvc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;" width="400px">Particular</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($stat){
								foreach($nvc as $key=>$val){
									echo "<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='nvc_particular[]' style='width:100%;' value='{$val['details']}'/></td>
											<td>
												<input type='text' class='amt {$val['class']}' name='nvc_amt[]' style='width:100%;text-align:right;' value='".number_format($val['amount'],2)."'/>
												<input type='hidden' name='nvc_class[]' value='{$val['class']}'/>
											</td>
										</tr>";
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th style="text-align:right;font-size:15px;"></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</div>
				<div style="clear:both;height:5px;"></div>
				<div style="display: flex;">
					<fieldset style="align-items: stretch;width:50%;float:left;">
						<legend>Journal Entry <input id="bt3" class="buthov" type="button" value="ChartOfAccount" onclick="ChartOfAccount();" style="height:30px;width:150px;"/></legend>
						<table class="navigateableMain" id="journalTbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;width:50px;" >Account Code</th>
									<th style="border:none;width:200px;">Account Desc</th>
									<th style="border:none;">Debit</th>
									<th style="border:none;">Credit</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($journal){
								$sumdr=0;$sumcr=0;
								foreach($journal as $key=>$val){
									echo '<tr>
										<td><input type="checkbox" ></td>
										<td><input readonly type="text" name="code[]" value="'.$val['account_code'].'" style="width:100%;"/></td>
										<td><input readonly type="text" name="desc[]" value="'.$val['account_desc'].'" style="width:100%;"/></td>
										<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="'.($val['dr']!=0?$val['dr']:"").'"/></td>
										<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="'.($val['cr']!=0?$val['cr']:"").'"/></td>
									</tr>';
									$sumdr+=$val['dr'];$sumcr+=$val['cr'];
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3">Total</th>
									<th id="dr_amt_total" style="text-align:right;"><?=$sumdr?></th>
									<th id="cr_amt_total" style="text-align:right;"><?=$sumcr?></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</div>
				<? if($info['notes']){?>
					<fieldset>
						<legend>NOTES</legend>
						<?=$info['notes']?>
					</fieldset>
				<?}?>
			</div>
		</div>
	</form>
	<div style="clear:both;height:20px;"></div>
</div>
<script>
$(document).ready(function() {
	$('input[name*="date"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	var config = {
	  '.chosen-select'           : {width: "100%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		console.log(this.value);
		console.log(this.options[this.selectedIndex].innerHTML);
	});
	
	// $("#journalTbl").click(function(){
		// jQuery.tableNavigationJournal();
	// });
	// $("#cc_tbl,#rc_tbl,#oc_tbl").click(function(){
		// jQuery.tableNavigationMain();
	// });
	sumVoucherAmt();
	sumDRAmt();
	sumCRAmt();
	$("input[name='prev_amt[]']").live("change", function (){
		$(this).val(new Number($(this).val()).formatMoney(2));
		sumVoucherAmt();
		sumGroup("prev_tbl");
	});
	$("input[name='cc_amt[]']").live("change", function (){
		$(this).val(new Number($(this).val()).formatMoney(2));
		//var rental = strtodouble($('#cc_tbl tbody tr:first').find("input[name='cc_amt[]']").val());
		var rental = sumClass("cc_rental");
		var disc = strtodouble($('#cc_tbl tr:has(td input[value="Less: Discount"])').find("input[name='cc_amt[]']").val());
		$('#cc_tbl tr:has(td input[value="Net Rental Due Before Tax"])').find("input[name='cc_amt[]']").val(new Number(rental-disc).formatMoney(2));
		var rental_bt = strtodouble($('#cc_tbl tr:has(td input[value="Net Rental Due Before Tax"])').find("input[name='cc_amt[]']").val());
		$('#cc_tbl tr:has(td input[value="Add: 12% VAT"])').find("input[name='cc_amt[]']").val(new Number(rental_bt*.12).formatMoney(2));
		$('#cc_tbl tr:has(td input[value="Less: 5% Withholding Tax"])').find("input[name='cc_amt[]']").val(new Number(rental_bt*.05).formatMoney(2));
		sumVoucherAmt();
		sumGroup("cc_tbl");
	});
	$("input[name='rc_reading_area[]']").live("change", function (){
		$(this).val(new Number($(this).val()).formatMoney(2));
		var rate = $(this).closest("tr").find("input[name='rc_rate[]']").val();
		var xval = $(this).val() * rate;
		$(this).closest("tr").find("input[name='rc_amount_due[]']").val(xval);
		var vat = new Number(sumRCAmt()*.12).formatMoney(2);
		$("#rc_vat").val(vat);
		sumVoucherAmt();
		sumGroup("rc_tbl");
	});
	$("input[name='oc_amount[]']").live("change", function (){
		$(this).val(new Number($(this).val()).formatMoney(2));
		var sum = 0;
		$("#oc_tbl .amt").each(function() {
			if (!$(this).hasClass("outputtax")) {
				var value = $(this).val().replace(/,/g, "");
				if(!isNaN(value) && value.length != 0) {
					sum += parseFloat(value);
				}
			}
		});
		$('#oc_tbl tr:has(td input[value="Add: 12% VAT"])').find("input[name='oc_amount[]']").val(new Number(sum*.12).formatMoney(2));
		sumGroup("oc_tbl");
	});
	$("input[name='nvc_amt[]']").live("change", function (){
		$(this).val(new Number($(this).val()).formatMoney(2));
		sumGroup("nvc_tbl");
	});
});
function penaltyCompute(){
	alert("Under development");
}
function sumRCAmt(){
	var sum = 0;
	$("#rc_tbl .amt").each(function() {
		if (!$(this).hasClass("outputtax")) {
			var value = $(this).val().replace(/,/g, "");
			if(!isNaN(value) && value.length != 0) {
				sum += parseFloat(value);
			}
		}
	});
	return sum;
}
$("input[name='rc_present[]']").live("change", function (){
	var result = $(this).closest("tr").find("input[name='rc_present[]']").val()-$(this).closest("tr").find("input[name='rc_previous[]']").val();
	$(this).closest("tr").find("input[name='rc_reading_area[]']").val(result);
	$(this).closest("tr").find("input[name='rc_amount_due[]']").val(result * $(this).closest("tr").find("input[name='rc_rate[]']").val());
	var vat = new Number(sumRCAmt()*.12).formatMoney(2);
	$("#rc_vat").val(vat);
	sumVoucherAmt();
});
$("input[name='rc_rate[]']").live("change", function (){
	var result = $(this).closest("tr").find("input[name='rc_reading_area[]']").val();
	//$(this).closest("tr").find("input[name='rc_reading_area[]']").val(result);
	$(this).closest("tr").find("input[name='rc_amount_due[]']").val(result * $(this).closest("tr").find("input[name='rc_rate[]']").val());
	var vat = new Number(sumRCAmt()*.12).formatMoney(2);
	$("#rc_vat").val(vat);
	sumVoucherAmt();
});
$("input[value='+']").on('click',function(){
	var id = $(this).attr('id');
	switch(id){
		case'cc':
			var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="particular[]" value="" style="width:100%;"/></td>\
					<td><input onchange="sumVoucherAmt()" type="text" class="amt" name="amt[]" style="width:100%;text-align:right;" value="0.00"/></td>\
				</tr>';
		break;
		case'rc':
			var txt = "<tr>"+
							'<td><input type="checkbox" ></td>'+
							"<td><input type='text' name='rc_details[]' style='width:100%;' value=''/></td>"+
							"<td><input type='text' name='rc_previous[]' style='width:100%;' value=''/></td>"+
							"<td><input type='text' name='rc_present[]' style='width:100%;' value=''/></td>"+
							"<td><input type='text' name='rc_reading_area[]' style='width:100%;text-align:right;' value=''/></td>"+
							"<td><input type='text' name='rc_rate[]' style='width:100%;text-align:right;' value=''/></td>"+
							"<td>"+
							"<input type='text' class='amt' name='rc_amount_due[]' style='width:100%;text-align:right;' value=''/>"+
							"<input type='hidden' name='rc_class[]' value='utilities'/>"+
							"</td>"+
						"</tr>";
		break;
		case'oc':
			var txt =  "<tr>"+
							'<td><input type="checkbox" ></td>'+
							"<td><input type='text' name='oc_details[]' style='width:100%;' value=''/></td>"+
							"<td>"+
								"<input type='text' class='amt' name='oc_amount[]' style='width:100%;text-align:right;' value=''/>"+
								"<input type='hidden' name='oc_class[]' value='other_income'/>"+
							"</td>"+
							"</tr>";
		break;
		case'prev':
			var txt =  "<tr>"+
							'<td><input type="checkbox" ></td>'+
							"<td><input type='text' name='prev_details[]' style='width:100%;' value=''/></td>"+
							"<td><input type='text' class='amt' name='prev_amount[]' style='width:100%;text-align:right;' value=''/></td>"+
						"</tr>";
		break;
		case'nvc':
			var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="nvc_particular[]" value="" style="width:100%;"/></td>\
					<td><input onchange="sumVoucherAmt()" type="text" class="amt" name="nvc_amt[]" style="width:100%;text-align:right;" value="0.00"/></td>\
				</tr>';
		break;
	}
	
	$("#"+id+"_tbl tbody").prepend(txt);
	sumVoucherAmt();
});
function generateSOA(){
	var cust_id = $("#business_name").chosen().val();
	var month = $("#month").val();
	if(cust_id==""){
		alert("Pls select Business Name first...");
	}else{
		window.location = "?page=soa_create&cust_id="+cust_id+"&month="+month+"&gensoa=true";
	}
}

function frmSave(){
	var frm = document.frm_stockin;
	
	if($("#xtotal").val()==0){
		alert("Pls me more particular....");
		return false;
	}
	if($("#dr_amt_total").text()==""||$("#cr_amt_total").text()==""){
		alert("Don't forget the Journal Entry....");
		return false;
	}
	// if(parseFloat($("#dr_amt_total").text())!=parseFloat($("#cr_amt_total").text())){
		// alert("Debit and Credit should be equal....");
		// return false;
	// }
	// if(parseFloat($("#dr_amt_total").text())!=parseFloat($("#xtotal").val())){
		// alert("Journal Amount should be equal to Total Amount....");
		// return false;
	// }
	frm.submit();
}
// function viewReport(page){
	// if (window.showModalDialog) {
		// window.showModalDialog(page,"PO","dialogWidth:650px;dialogHeight:650px");
	// } else {
		// window.open(page,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	// }
// }

function validate(id,type){
	$.ajax({
		url: './content/vouchering_ajax.php?execute=validate&refid='+id+'&type='+type,
		type:"POST",
		success:function(data){
			alert(data);
			if(data=="success"){
				window.location.reload();
			}
		}
	});
}
function reject(id){
	var urls = getUrl();
	clickDialog('dialogbox2',400,300,'rejectView&id='+id,'Reject Voucher',urls,'vouchering_ajax.php');
}
$("#cc_tbl,#rc_tbl,#oc_tbl,#prev_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		//$("tr.selected").remove();
		$('input[type="checkbox"]:checked').closest("tr").remove();
		sumVoucherAmt();
	}
});
$("#journalTbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		//$("tr.selectedjournal").remove();
		$('input[type="checkbox"]:checked').closest("tr").remove();
	}
	//event.preventDefault();
});

function itemSelected(code,desc,defside){ //in journalizing
	var txt = '<tr>\
			<td><input type="checkbox" ></td>\
			<td><input readonly type="text" name="code[]" value="'+code+'" style="width:100%;"/></td>\
			<td><input readonly type="text" name="desc[]" value="'+desc+'" style="width:100%;"/></td>\
			<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="'+(defside=='D'?'0.00':'')+'"/></td>\
			<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="'+(defside=='C'?'0.00':'')+'"/></td>\
		</tr>';
	$("#journalTbl tbody").prepend(txt);
	$('#prodlist').dialog('close');
	//jQuery.tableNavigationJournal();
	
}

function ChartOfAccount(){
	var urls = getUrl();
	clickDialog('prodlist',1000,500,'chartofaccount','Chart of Account',urls,'vouchering_ajax.php');
	jQuery.tableNavigation();
}
function viewSOA(){
	var urls = getUrl();
	clickDialog('prodlist',1000,500,'view_listing','View Records',urls,'soa_ajax.php');
	jQuery.tableNavigation();
}
function sumVoucherAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	$(".deduction").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum -= parseFloat(value);
		}
	});
	
	
	$("#xtotal").val(new Number(sum).formatMoney(2));
	var utilities = $('#rc_tbl tr:has(td input[value="Electricity"])').find("input[name='rc_amount_due[]']").val()!=undefined?$('#rc_tbl tr:has(td input[value="Electricity"])').find("input[name='rc_amount_due[]']").val():0;
	utilities+=$('#rc_tbl tr:has(td input[value="Pest Control"])').find("input[name='rc_amount_due[]']").val()!=undefined?$('#rc_tbl tr:has(td input[value="Pest Control"])').find("input[name='rc_amount_due[]']").val():0;
	utilities+=$('#rc_tbl tr:has(td input[value="WATER"])').find("input[name='oc_amount[]']").val()!=undefined?$('#rc_tbl tr:has(td input[value="WATER"])').find("input[name='oc_amount[]']").val():0;
	var wtax = $('#cc_tbl tr:has(td input[value="Less: 5% Withholding Tax"])').find("input[name='cc_amt[]']").val();
	
	$('#journalTbl tbody tr:first td:eq(3)').find("input[name='dr_amt[]']").val(new Number(parseFloat(sum)-strtodouble(wtax)).formatMoney(2)); //AR
	$('#journalTbl tr:has(td input[value="CREDITABLE TAX WITHHELD"])').find("input[name='dr_amt[]']").val(new Number(strtodouble(wtax)).formatMoney(2));
	
	$('#journalTbl tr:has(td input[value="OUTPUT TAX"])').find("input[name='cr_amt[]']").val(new Number(sumClass('outputtax')).formatMoney(2));
	$('#journalTbl tr:has(td input[value="RENT REVENUE"])').find("input[name='cr_amt[]']").val(new Number(sum-(sumClass('outputtax')+sumClass('other_income')+sumClass('utilities')+sumClass('mktgfee'))).formatMoney(2));
	$('#journalTbl tr:has(td input[value="OTHER INCOME"])').find("input[name='cr_amt[]']").val(new Number(sumClass('other_income')).formatMoney(2));
	$('#journalTbl tr:has(td input[value="INCOME FROM UTILITIES"])').find("input[name='cr_amt[]']").val(new Number(sumClass('utilities')).formatMoney(2));
	$('#journalTbl tr:has(td input[value="MARKETING FEE"])').find("input[name='cr_amt[]']").val(new Number(sumClass('mktgfee')).formatMoney(2));
	
	
	sumDRAmt();
	sumCRAmt();
	sumGroup("prev_tbl");
	sumGroup("cc_tbl");
	sumGroup("rc_tbl");
	sumGroup("oc_tbl");
	sumGroup("nvc_tbl");
}

function sumGroup(tblid){
	var add = 0;
	var minus = 0;
	// iterate through each td based on class and add the values
	$("#"+tblid +" tbody tr").each(function() {
		add += strtodouble($(this).find('.amt').val());
		minus +=  strtodouble($(this).find('.deduction').val());
	});
	var res = (add - minus);
	$("#"+tblid +" tfoot tr:first th:eq(1)").html(strtocurrency(res));
}
function sumClass(classname){
	var sum = 0;
	// iterate through each td based on class and add the values
	$("."+classname).each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		console.log(classname+':'+value+'\n');
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return sum;
	//return new Number(sum).formatMoney(2);
}

function sumDRAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".dr_amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#dr_amt_total").html(new Number(sum).formatMoney(2));
}
function sumCRAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".cr_amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#cr_amt_total").html(new Number(sum).formatMoney(2));
}
</script>