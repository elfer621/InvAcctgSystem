<?php
if($_POST){
	$dt = $_REQUEST['month']?explode("-",date('Y-m',strtotime("2017-".$_REQUEST['month']))):explode("-",date('Y-m'));
	foreach($_REQUEST['cust_id'] as $key=>$val){
		$refid=$val;
		$total[$refid]=0;$taxwheld[$refid]=0;$outputtax[$refid]=0;$utilities[$refid]=0;$other[$refid]=0;$mktg[$refid]=0;
		$info= $db->getWHERE("cust.*,prev.amount,prev.id as soaref,payment.total_payment","tbl_customers cust 
		left join (select * from tbl_soa where cust_id='{$refid}' and year='".$dt[0]."' and month='".($dt[1]-1)."') prev on cust.cust_id=prev.cust_id 
		left join (select cust_id,sum(amount) total_payment from tbl_customers_trans where cust_id='{$refid}' and transtype='PAYMENT' and date_format(date,'%Y%m')='".$dt[0].$db->customeFormat(($dt[1]-1),2)."' group by date_format(date,'%Y%m')) payment on cust.cust_id=payment.cust_id",
		"where cust.cust_id='{$refid}'");
		$data[$refid]['prev'][]=array('details'=>'Previous Account Balance REF #'.$db->customeFormat($info['soaref'],6),'amount'=>$info['amount'],'class'=>'amt');
		$data[$refid]['prev'][]=array('details'=>'Adjustment','amount'=>0,'class'=>'deduction');
		$data[$refid]['prev'][]=array('details'=>'Payment Received OR#','amount'=>$info['total_payment'],'class'=>'deduction');
		$data[$refid]['prev'][]=array('details'=>'Penalty 5%','amount'=>(($info['amount']-$info['total_payment'])*.05),'class'=>'amt');
		$total[$refid]+=(($info['amount']-$info['total_payment'])*1.05);
		//echo $total."<br/>";
		$rent_amt = $info['fixed_rental_amount'];
		$data[$refid]['cc'][] = array('details'=>"Month of ".date('F, Y',strtotime($dt[0]."-".$db->customeFormat(($dt[1]),2)."-01"))." Rental",'amount'=>($rent_amt),'class'=>'');
		$data[$refid]['cc'][] = array('details'=>"Less: Discount",'amount'=>(0));
		$data[$refid]['cc'][] = array('details'=>"Net Rental Due Before Tax",'amount'=>($rent_amt),'class'=>'amt');
		$data[$refid]['cc'][] = array('details'=>"Add: 12% VAT",'amount'=>($rent_amt*.12),'class'=>'amt outputtax');
		$data[$refid]['cc'][] = array('details'=>"Less: 5% Withholding Tax",'amount'=>($rent_amt*.05),'class'=>'deduction');
		$total[$refid]+=(($info['floor_area_sqm']*$info['fixed_rental_per_sqm'])*1.12); //12% - 5%
		$taxwheld[$refid]+=(($info['floor_area_sqm']*$info['fixed_rental_per_sqm'])*.05);
		$outputtax[$refid]+=(($info['floor_area_sqm']*$info['fixed_rental_per_sqm'])*.12);
		//echo $total."<br/>";
		$elect = $db->getWHERE("*","tbl_utilityreading_electricity","where year='{$dt[0]}' and month='{$dt[1]}' and cust_id='{$refid}'");
		$data[$refid]['rc'][] = array(
					'details'=>'Electricity',
					'previous'=>$elect['previous'],
					'present'=>$elect['present'],
					'reading_area'=>$elect['reading'],
					'rate'=>'10.18',
					'amount_due'=>($elect['reading']*10.18),
					'class'=>'utilities'
					);
		$data[$refid]['rc'][] = array(
					'details'=>'Pest Control',
					'previous'=>'',
					'present'=>'',
					'reading_area'=>$info['floor_area_sqm'],
					'rate'=>'5',
					'amount_due'=>$info['floor_area_sqm']*5,
					'class'=>'utilities'
					);
		$data[$refid]['rc'][] = array(
					'details'=>'WATER',
					'previous'=>'',
					'present'=>'',
					'reading_area'=>$info['floor_area_sqm'],
					'rate'=>'20',
					'amount_due'=>$info['floor_area_sqm']*20,
					'class'=>'utilities'
					);
		$utilities[$refid]+=array_sum(array_map(function($var) {return $var['amount_due'];}, $data[$refid]['rc']));
		$outputtax[$refid]+=(array_sum(array_map(function($var) {return $var['amount_due'];}, $data[$refid]['rc']))*.12);
		$data[$refid]['rc'][] = array(
					'details'=>'Add: 12% VAT',
					'previous'=>'',
					'present'=>'',
					'reading_area'=>'',
					'rate'=>'',
					'amount_due'=>(array_sum(array_map(function($var) {return $var['amount_due'];}, $data[$refid]['rc']))*.12),
					'class'=>'outputtax'
					);
		$total[$refid]+=array_sum(array_map(function($var) {return $var['amount_due'];}, $data[$refid]['rc']));
		//echo $total."<br/>";
		//$data[$refid]['oc'][] = array('details'=>'WATER','amount'=>120,'class'=>'utilities');
		$data[$refid]['oc'][] = array('details'=>'CUSA','amount'=>1000,'class'=>'other_income');
		$other[$refid]+=1000;
		$data[$refid]['oc'][] = array('details'=>'MARKETING FEE','amount'=>1000,'class'=>'mktgfee');
		$mktg[$refid]+=1000;
		$outputtax[$refid]+=(array_sum(array_map(function($var) {return $var['amount'];}, $data[$refid]['oc']))*.12);
		$data[$refid]['oc'][] = array('details'=>'Add: 12% VAT','amount'=>(array_sum(array_map(function($var) {return $var['amount'];}, $data[$refid]['oc']))*.12),'class'=>'outputtax');
		$total[$refid]+=array_sum(array_map(function($var) {return $var['amount'];}, $data[$refid]['oc']));
		//echo $total."<br/>";
		//exit;
		$data[$refid]['journal'][]=array(
					'account_code'=>'1201',
					'account_desc'=>'ACCOUNTS RECEIVABLE',
					'dr'=>($total[$refid]-$taxwheld[$refid]),
					'cr'=>0,
					'ar_refid'=>$refid
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'1228',
					'account_desc'=>'CREDITABLE TAX WITHHELD',
					'dr'=>$taxwheld[$refid],
					'cr'=>0
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'2026',
					'account_desc'=>'OUTPUT TAX',
					'dr'=>0,
					'cr'=>$outputtax[$refid]
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'4000',
					'account_desc'=>'RENT REVENUE',
					'dr'=>0,
					'cr'=>($total[$refid]-$taxwheld[$refid])-($other[$refid]+$outputtax[$refid]+$utilities[$refid]+$mktg[$refid])
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'4004',
					'account_desc'=>'OTHER INCOME',
					'dr'=>0,
					'cr'=>$other[$refid]
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'4004A',
					'account_desc'=>'INCOME FROM UTILITIES',
					'dr'=>0,
					'cr'=>$utilities[$refid]
					);
		$data[$refid]['journal'][]=array(
					'account_code'=>'4400',
					'account_desc'=>'MARKETING FEE',
					'dr'=>0,
					'cr'=>$mktg[$refid]
					);
		
		
	}//end of foreach
	
	foreach($data as $key =>$val){
		foreach($val as $k=>$v){
			switch($k){
				case'prev':
					foreach($v as $a=>$b){
						$prev.="('{$_REQUEST['year']}','{$_REQUEST['month']}','$key','{$b['details']}','{$b['amount']}','{$b['class']}')";
					}
				break;
				case'cc':
					foreach($v as $a=>$b){
						$cc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','$key','{$b['details']}','{$b['amount']}','{$b['class']}')";
					}
				break;
				case'rc':
					foreach($v as $a=>$b){
						$rc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','$key','".date('Y-m-d')."','{$b['details']}','{$b['previous']}','{$b['present']}','{$b['reading_area']}','{$b['rate']}','{$b['amount_due']}','{$b['class']}')";
					}
				break;
				case'oc':
					foreach($v as $a=>$b){
						$oc.="('{$_REQUEST['year']}','{$_REQUEST['month']}','$key','".date('Y-m-d')."','{$b['details']}','{$b['amount']}','{$b['class']}')";
					}
				break;
				case'journal':
					foreach($v as $a=>$b){
						$entry[]=array('account_code'=>$b['account_code'],'account_desc'=>$b['account_desc'],'dr'=>str_replace( ',','',$b['dr']),'cr'=>str_replace( ',','',$b['cr']),'ar_refid'=>$b['ar_refid']);
					}
				break;
			}
		}
		
		$soa = $db->getWHERE("id,vouchering_ref","tbl_soa","where year='{$_REQUEST['year']}' and month='{$_REQUEST['month']}' and cust_id='$key'");
		$nextID= $soa?$soa['id']:$db->getNextIdSOA();
		//Journal
		$glref = $soa?$soa['vouchering_ref']:$con->getNextJournalID('GJ');
		$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`) values 
			('".$glref."','','".date('Y-m-d')."','GJ',
			'TO RECORDS BILLING STATEMENT # ".$nextID."',
			'".($total[$key]-$taxwheld[$key])."','".$_SESSION['xid']."','ForApproval') 
			on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
		//echo "<br/><hr/>";
		$glid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry);
		//Journal End
		$soa = "insert into tbl_soa (year,month,cust_id,date,amount,due_date,vouchering_ref) values 
			('{$_REQUEST['year']}','{$_REQUEST['month']}','$key','".date('Y-m-d')."','".($total[$key]-$taxwheld[$key])."','".date('Y-m-d',strtotime(date('m/d/Y')."+5 days"))."','$glid') 
			on duplicate key update date=values(date),amount=values(amount),due_date=values(due_date),vouchering_ref=values(vouchering_ref)";
		//echo "<br/><hr/>";
		$qry_soa=mysql_query($soa);
		if(!$qry_soa){
			echo "Error SOA:".mysql_error();
		}
		//Insert Customer Transaction
		$sql_cust_trans = "insert into tbl_customers_trans (cust_id,receipt,date,transtype,details,amount) 
			values ('$key','$nextID','".date('Y-m-d')."','BILLING','Statement No.:$nextID','".($total[$key]-$taxwheld[$key])."') 
			on duplicate key update cust_id=values(cust_id),date=values(date),transtype=values(transtype),details=values(details),amount=values(amount)";
		$qry_cust_trans = mysql_query($sql_cust_trans);
		if(!$qry_cust_trans){
			echo "Error CustTrans:".mysql_error()."<br/>";
		}
		//Insert Customer Transaction
		
	}
	
	$prev = "insert into tbl_soa_prev_bal (year,month,cust_id,details,amount,class) values ".str_replace(")(","),(",$prev)." on duplicate key update details=values(details),amount=values(amount),class=values(class)";
	$cc = "insert into tbl_soa_current_charges (year,month,cust_id,details,amount,class) values ".str_replace(")(","),(",$cc)." on duplicate key update details=values(details),amount=values(amount),class=values(class)";
	$rc = "insert into tbl_soa_reimbursable_charges (year,month,cust_id,date,details,previous,present,reading_area,rate,amount_due,class) values ".str_replace(")(","),(",$rc)." on duplicate key update details=values(details),previous=values(previous),present=values(present),reading_area=values(reading_area),rate=values(rate),amount_due=values(amount_due),class=values(class)";
	$oc = "insert into tbl_soa_other_charges (year,month,cust_id,date,details,amount,class) values ". str_replace(")(","),(",$oc)." on duplicate key update details=values(details),amount=values(amount),class=values(class)";
	// echo $prev."<br/><hr/>";
	// echo $cc."<br/><hr/>";
	// echo $rc."<br/><hr/>";
	// echo $oc."<br/><hr/>";
	
	$qry_prev=mysql_query($prev);
	if(!$qry_prev){
		echo "Error Prev:".mysql_error();
	}
	$qry_cc=mysql_query($cc);
	if(!$qry_cc){
		echo "Error CC:".mysql_error();
	}
	$qry_rc=mysql_query($rc);
	if(!$qry_rc){
		echo "Error RC:".mysql_error();
	}
	$qry_oc=mysql_query($oc);
	if(!$qry_oc){
		echo "Error OC:".mysql_error();
	}
	
}
$month = array('01'=>'(01) January','02'=>'(02) February','03'=>'(03) March','04'=>'(04) April','05'=>'(05) May',
	'06'=>'(06) June','07'=>'(07) July','08'=>'(08) August','09'=>'(09) September','10'=>'(10) October','11'=>'(11) November','12'=>'(12) December');
$xmonth=date('m');
$sql="select * from tbl_customers";
$qry = mysql_query($sql);
?>
<form method="post">
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
	<input type="submit" value="Process" style="float:right;height:20px;width:100px;"/>
</fieldset>
<table class="navigateable tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
<thead>
	<tr>
		<th style="border:none;width:50px;">&nbsp;</th>
		<th style="border:none;" >Account Code</th>
		<th style="border:none;width:800px;">Account Name</th>
		<th style="border:none;width:800px;">Unit</th>
	</tr>
</thead>
<tbody>
<?php while($row=mysql_fetch_assoc($qry)){
	echo "<tr>
		<td><input type='checkbox' name='cust_id[]' value='{$row['cust_id']}'></td>
		<td>".$db->customeFormat($row['cust_id'],5)."</td>
		<td>{$row['customer_name']}</td>
		<td style='text-align:center;'>F{$row['floor']} U{$row['mall_unit_number']}</td>
	</tr>";
} ?>
</tbody>
</table>
</form>