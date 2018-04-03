<?php
if($_SESSION['settings']['postype']!='single'){
	if(!$_SESSION['serverReading']&&$_REQUEST['page']=="sales"){
		$_SESSION['serverReading']=$db->getServerReadingnum();
		if(!$_SESSION['serverReading']){
			if($_SESSION['restrictionid']!=1){
				session_destroy();
			}
			echo "<script>window.onload=function(){alertMsgServerReading('Server are not yet initialized the reading....');}</script>";
		}
	}
}
if(!$_SESSION['readingnum']){
	$_SESSION['readingnum']=$db->getReadingnum($_SESSION['counter_num']);
}
if(!$_SESSION['readingnum'] && $_SESSION['counter_num'] && $db->getServerReadingnum()){ //&& ($_SESSION['restrictionid']==2 || $_SESSION['restrictionid']==4)
	$check = $db->getWHERE("*","tbl_reading","where reading_num='".$_SESSION['serverReading']."' and counter='".$_SESSION['counter_num']."'");
	if($check){
		if($check['end_date']!='0000-00-00'){
			echo "<script>window.onload=function(){alertMsgServerReading('Reading {$_SESSION['serverReading']} is already closed transaction on {$check['end_date']}');}</script>";
		}
	}else{
		$check = $db->checkSalesTbl($_SESSION['counter_num']);
		if(!$check){
			$db->createSalesTable($_POST['counter_num']);
		}
		echo "<script>window.onload=function(){
			$('#startreading').dialog({
				autoOpen: false,
				width: 300,
				height: 200,
				modal: true,
				resizable: false,
				closeOnEscape: false,
				title:'Reading Start',
				open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
			});
			htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=startreading',async:false});
			$('#startreading').html(htmlobj.responseText);
			$('#startreading').dialog('open');
		}</script>";
	}
}
switch($_REQUEST['specialcase']){
	case 1: //teletech
		$rec = $db->getWHERE("*","data_teletech_patient","where id='{$_REQUEST['idno']}'");
		$_SESSION['patient_info']=array(
			'idno'=>$_REQUEST['idno'],
			'name'=>$rec['first_name']." ".$rec['last_name'],
			'tblsource'=>"data_teletech_patient",
			'patientInfo'=>array(
				'idno'=>$_REQUEST['idno'],
				'first_name'=>$rec['first_name'],
				'last_name'=>$rec['last_name'],
				'birth_date'=>$rec['date_of_birth'],
				'age'=>$rec['age'],
				'gender'=>$rec['gender']
			)
		);
		$cust = $db->getWHERE("*","tbl_customers","where cust_id='{$_SESSION['custid']}'");
		$_SESSION['customer_info']=array('cust_id'=>$_SESSION['custid'],'name'=>$cust['customer_name']);
		
		$package = $db->getWHERE("*","tbl_packages","where id='{$_SESSION['defaultPackage']}'");
		$_SESSION[$_REQUEST['page']]=unserialize($package['packages']);
	break;
}
$sumInfo = $db->sumInfo($_SESSION['sales'],$percent_qualify,$points_divisor); //sum total for receipt header
//print_r($_SESSION['sales']);
$_SESSION['receiptnum']=($_SESSION['receiptnum']?$_SESSION['receiptnum']:($db->getReceipt($_SESSION['counter_num'],$_SESSION['readingnum'],"sales")+1));
if($_POST['counter_num']&&$_SESSION['sales']){
	mysql_query("BEGIN");
	//assigning variable for what table to save -->Start
	switch($_REQUEST['xtype']){
		case "sales":
			$tblname = "tbl_sales_receipt_".$_POST['counter_num'];
			$tblname_items = "tbl_sales_items";
			if($_SESSION['tra_num']){
				$refid=$_SESSION['tra_num'];
			}else{
				//$refid = $db->getReceipt($_SESSION['counter_num'],$_SESSION['readingnum'],"sales")+1;
				$refid= $_REQUEST['receipt_num'];
			}
		break;
		case"suspended":
		case"order":
			$tblname = "tbl_order_receipt";
			$tblname_items = "tbl_order_items";
			if($view==4){
				$check_table = $db->getWHERE("*","tbl_order_receipt","where receipt_id='{$_REQUEST['table']}'");
				if($check_table){
					echo "<script>$(document).ready(function(){alertMsg('That table has already entry...');window.location='$location';});</script>";
					//header("location:$location");
					exit();
				}else{
					$refid = $_REQUEST['table'];
				}
			}else{
				$refid = $db->getReceipt($_SESSION['counter_num'],$_SESSION['readingnum'],"order")+1;
			}
		break;
	}
	//assigning variable for what table to save -->End
	//saving entries -->Start
	if(is_array($_REQUEST['payment_type'])){
		$payment_type="SplitPayment";
		$payment=0;
		$flag=false;
		$sql="insert into tbl_sales_splitpayment (receipt_id,counter,reading,payment_type,payment_amt) values ";
		for($x=0;$x<count($_REQUEST['payment_type']);$x++){
			if($flag)$sql.=",";
			$sql.="('$refid','{$_SESSION['counter_num']}','{$_SESSION['readingnum']}','{$_REQUEST['payment_type'][$x]}','{$_REQUEST['payment'][$x]}')";
			$payment+=$_REQUEST['payment'][$x];
			$flag=true;
		}
		$qry=mysql_query($sql);
		if(!$qry){
			echo "SplitPayment Error: ".mysql_error();
		}
	}else{
		$payment=str_replace(",","",$_POST['payment']);
		$payment_type=$_REQUEST['payment_type'];
	}
	$ext="";
	if($_SESSION['student_info']){
		$ext = "studentid='".($_SESSION['student_info']?$_SESSION['student_info']['idno']:'')."',
				studentname='".strtoupper($_SESSION['student_info']['name'])."',";
	}elseif($_SESSION['patient_info']){
		$ext = "studentid='".($_SESSION['patient_info']?$_SESSION['patient_info']['idno']:'')."',
			studentname='".($_SESSION['patient_info']?$_SESSION['patient_info']['patientInfo']['first_name']." ".$_SESSION['patient_info']['patientInfo']['last_name']:'')."',
			tblsource='".($_SESSION['patient_info']?$_SESSION['patient_info']['tblsource']:'')."',";
	}
	$sql_receipt = "insert into $tblname set 
		receipt_id='".$refid."',
		counter_num=".$_SESSION['counter_num'].",
		reading='".$_SESSION['readingnum']."',
		date=curdate(),
		amount='".str_replace(",","",$_POST['total'])."',
		`change`='".str_replace(",","",number_format($_POST['change'],2))."',
		cost='".$sumInfo['cost']."',
		gain='".str_replace(",","",($_POST['total']-$sumInfo['cost']))."',
		vat='".$sumInfo['vat']."',
		$ext
		course='".$_SESSION['student_info']['course']."',
		yr='".$_SESSION['student_info']['yr']."',
		category_id='{$_SESSION['category']['catid']}',
		orderslip='".$_SESSION['OrderSlip']."',
		type='".strtoupper($_REQUEST['xtype'])."',
		cashier='".$_SESSION['xuser_pos'].($_REQUEST['order_cleark']==""?"":"/".$_REQUEST['order_cleark'])."',
		tender='".$payment."',
		payment='".strtoupper($payment_type)."' 
		on duplicate key update amount=values(amount),`change`=values(`change`),cost=values(cost),gain=values(gain),
		vat=values(vat),studentid=values(studentid),studentname=values(studentname),course=values(course),
		yr=values(yr),category_id=values(category_id),orderslip=values(orderslip),`type`=values(`type`),cashier=values(cashier),
		tender=values(tender),payment=values(payment)";
	//print_r($sumInfo);
	//echo $sql_receipt;exit;
	$qry_receipt = mysql_query($sql_receipt);
	if($qry_receipt){
		$delitems = mysql_query("delete from $tblname_items where receipt='$refid' and counter='{$_SESSION['counter_num']}' and reading='{$_SESSION['readingnum']}'");
		foreach($db->subval_sort($_SESSION['sales'],'count',arsort) as $val){ 
			if(!isset($val['category_id'])){
				$catid = $db->getWHERE("category_id","tbl_product_name","where sku_id='{$val['sku']}'");
				$catid = $catid['category_id'];
			}else{
				$catid = $val['category_id'];
			}
			$sql_items = "insert into $tblname_items set 
				receipt=$refid,
				counter={$_SESSION['counter_num']},
				reading={$_SESSION['readingnum']},
				barcode='".$val['bcode']."',
				item_desc='".mysql_real_escape_string($val['prod_name'])."',
				qty=".$val['qty'].",
				unit='".$val['unit']."',
				selling='".str_replace(",","",$val['price'])."',
				total='".str_replace(",","",$val['total'])."',
				cost='".str_replace(",","",$val['cost'])."',
				gain='".((str_replace(",","",$val['price'])-str_replace(",","",$val['cost']))*$val['qty'])."',
				vat='".($val['tax_type']==3?($val['price']/9.333):0)."',
				studentid='".($_SESSION['student_info']?$_SESSION['student_info']['idno']:'')."',
				subjnametype='".$val['subjnametype']."',
				skuid='".$val['sku']."',
				category_id='".$catid."',
				divmul='".$val['divmul']."'";
			$qry_items = mysql_query($sql_items);
			if(!$qry_items){
				echo "Item[".$val['bcode']."]: ".mysql_error();
				//exit();
			}else{
				if($_REQUEST['xtype']=="sales"){
					echo $db->updateInvNew($val['sku']);
				}
			}
		}
		if($qry_receipt && $qry_items){
			mysql_query("COMMIT");
			//saving extra info -->Start
			if($_SESSION['customer_info']){
				$db->saveCustTrans(array('cust_id'=>$_SESSION['customer_info']['cust_id'],
					'receipt'=>$refid,'counter'=>$_SESSION['counter_num'],'reading'=>$_SESSION['readingnum'],
					'transtype'=>$_REQUEST['payment_type'],'details'=>"Receipt:$refid | Counter: {$_SESSION['counter_num']} | Reading: {$_SESSION['readingnum']}",
					'amount'=>str_replace(",","",number_format($_POST['total'],2))));
			}else if($_SESSION['cheque']){
				$sqlcheque = "insert into tbl_chequepayment_details set 
					receipt='".$refid."',reading='".$_SESSION['readingnum']."',counter='".$_SESSION['counter_num']."',cheque_details='".$_SESSION['cheque']['details']."',amount='".str_replace(",","",$_SESSION['cheque']['amount'])."'";
				$qrycheque=mysql_query($sqlcheque);
				if(!$qrycheque){
					echo mysql_error();
				}			
			}
			if($_SESSION['voucher']){
				$sqlvoucher = "insert into tbl_voucherpayment_details set 
					receipt='".$refid."',reading='".$_SESSION['readingnum']."',counter='".$_SESSION['counter_num']."',voucher_num='".$_SESSION['voucher']['voucher_num']."',voucher_details='".$_SESSION['voucher']['details']."',amount='".str_replace(",","",$_SESSION['voucher']['amount'])."'";
				$qryvoucher=mysql_query($sqlvoucher);
				if(!$qryvoucher){
					echo mysql_error();
				}else{
					$voucher_amt = (double)str_replace(",","",$_SESSION['voucher']['amount'])<(double)str_replace(",","",$_POST['total'])?str_replace(",","",$_SESSION['voucher']['amount']):str_replace(",","",$_POST['total']);
					$sqlvoucher2 = "insert into tbl_voucherdeduction_details set 
						receipt='".$refid."',reading='".$_SESSION['readingnum']."',counter='".$_SESSION['counter_num']."',
						voucher_num='".$_SESSION['voucher']['voucher_num']."',amount='".$voucher_amt."'";
					$qryvoucher2=mysql_query($sqlvoucher2);
					if(!$qryvoucher2){
						echo mysql_error();
					}
				}
			}
			if($_SESSION['creditcard']){
				$cc_num = $_SESSION['creditcard']['cc_num'];
				$cc_custname = $_SESSION['creditcard']['cc_custname'];
				$cc_approval = $_SESSION['creditcard']['cc_approval'];
				$card_type = $_SESSION['creditcard']['card_type'];
				$cc_amount = $_SESSION['creditcard']['cc_amount'];
				$sql = "insert into tbl_creditcardpayment_details (receipt,reading,counter,cc_num,cc_custname,cc_approval,card_type,cc_amount) 
					values ('$refid','{$_SESSION['readingnum']}','{$_SESSION['counter_num']}','$cc_num','$cc_custname','$cc_approval','$card_type','$cc_amount')";
				$qry=mysql_query($sql);
				if(!$qry){
					echo mysql_error();
				}
			}
			if($_SESSION['more_info']){
				$sqlmoreinfo = "insert into tbl_sales_moreinfo set 
					sales_refid='$refid',
					ws='{$_SESSION['more_info']['ws']}',
					ob='{$_SESSION['more_info']['ob']}',
					location='{$_SESSION['more_info']['location']}',
					agent='{$_SESSION['more_info']['agent']}'";
				$qrymoreinfo=mysql_query($sqlmoreinfo);
				if(!$qrymoreinfo){
					echo mysql_error();
				}
			}
			if($_SESSION['shoppers_card']){
				$sqlshoppers_card = "insert into tbl_shoppers_points (shoppers_card_id,dept_id,total_purchased,total_points) 
					values ('".$_SESSION['shoppers_card']['shoppers_card_id']."','".$dept_id."','".str_replace(",","",$_POST['total'])."','".$sumInfo['points']."') 
					ON DUPLICATE KEY UPDATE 
					total_purchased=total_purchased + ".str_replace(",","",$_POST['total']).",
					total_points=total_points + ".$sumInfo['points'];
				$qryshoppers_card=mysql_query($sqlshoppers_card);
				if(!$qryshoppers_card){
					echo mysql_error();
				}
			}
			
			switch($_REQUEST['specialcase']){
				default: //teletech
					$sql="update data_teletech_patient set compliance='Incomplete',receipt='$refid',counter='{$_SESSION['readingnum']}',reading='{$_SESSION['counter_num']}' where id='{$_REQUEST['idno']}'";
					$qry=mysql_query($sql);
					if(!$qry){
						echo mysql_error();
					}
				break;
			}
			
			//saving extra info -->End
		}else{
			mysql_query("ROLLBACK");
			exit;
		}
	}else{
		echo "Receipt Header: ".mysql_error()."\n";
		echo $sql_receipt;
		//exit();
	}
	//saving entries -->End
	if($_REQUEST['xtype']=="sales"){
		//$receipt_type = "./reports/receipt.php?receipt_num=$refid";
		if($_REQUEST['specialcase']){
		echo "<script>$(document).ready(function(){window.close();window.opener.location.href = window.opener.location.href;});</script>";
		}else{
			echo "<script>$(document).ready(function(){loadiFrame('$receipt_type$refid');PrintIframe('{$_POST['change']}','{$_REQUEST['payment_type']}');});</script>";
		}
	}else if($_REQUEST['xtype']=="order"){ //order station functionality
		// if($view!=4){
			// session_destroy();
		// }
		header("location:$location");
	}
	//$db->unsetExcept(array('xuser_pos','restrictionid','xid','counter_num','readingnum'));
	unset($_SESSION['sales']);unset($_SESSION['order_info']);unset($_SESSION['customer_info']);unset($_SESSION['tra_num']);unset($_SESSION['cheque']);
	unset($_SESSION['more_info']);unset($_SESSION['shoppers_card']);unset($_SESSION['previewToSession']);unset($_SESSION['creditcard']);
	unset($_SESSION['student_info']);unset($_SESSION['voucher']);unset($_SESSION['disc_info']);unset($_SESSION['OrderSlip']);unset($_SESSION['category']);
	unset($_SESSION['receiptnum']);unset($_SESSION['patient_info']);
}
if($_POST['cashout_save']){
	$qry = mysql_query("insert into tbl_cashout set 
		counter_num='".$_SESSION['counter_num']."',
		reading='".$_SESSION['readingnum']."',
		date='".$_POST['cashout_date']."',
		remarks='".$_POST['cashout_remarks']."',
		amount='".$_POST['cashout_amount']."'");
	$cashout_refid = mysql_insert_id();
	if(!$qry){
		echo mysql_error();
	}else{
		echo "<script>printingModal('./reports/cashout.php?refnum=".$cashout_refid."','Cash Out',300,300);</script>";
	}
}

// arsort($_SESSION['sales']);
//print_r($_SESSION['sales']);
// if(!$_SESSION['category']){
// echo "<script>window.onload=function(){
			// $('#dialogbox3').dialog({
				// autoOpen: false,
				// width: 300,
				// height: 200,
				// modal: true,
				// resizable: false,
				// closeOnEscape: false,
				// title:'Select Category',
				// open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
			// });
			// htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=selectcategory',async:false});
			// $('#dialogbox3').html(htmlobj.responseText);
			// $('#dialogbox3').dialog('open');
		// }</script>";
// }
// echo "<pre>";
// print_r($_SESSION['patient_info']);
// echo "</pre>";
?>
<style>
#modal-background {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: white;
    opacity: .50;
    -webkit-opacity: .5;
    -moz-opacity: .5;
    filter: alpha(opacity=50);
    z-index: 1000;
}
#modal-background.active {
    display: block;
}
.top{
	width:980px;
	margin:0 auto;
}
</style>
<div class="top">
	<div class="header">
		<div class="header-top">
			<div style="float:left;">
				<div style="font-size:15px;"><?= Ucfirst($transtype)." Station ".$_SESSION['counter_num']?></div>
				<div style="clear:both;"></div>
				<div style="font-size:17px;float:left;margin-right:10px;">Category:</div><input type="text" name="cat" id="cat" style="float:left;width:150px;border:none;background:transparent;" value="<?=$_SESSION['category']['catname']?>" readonly />
				<?='<div style="font-size:15px;float:left;margin-left:10px;">'." Receipt Num: <input type='text' name='receipt_num' id='receipt_num' value='".$_SESSION['receiptnum']."' readonly style='border:none;background:none;'/></div><div style='clear:both;'></div>"?>
				<div style="font-size:17px;float:left;margin-right:10px;">OrderSlip#</div><input type="text" name="orderslip" id="orderslip" style="float:left;width:150px;" value="<?=$_SESSION['OrderSlip']?>" onchange="saveOrderSlip(this.value)"/>
				<div style="clear:both;height:5px;"></div>
				<div id="name_cust" style="font-size:17px;"><?= $_SESSION['customer_info']?"Customer Name: ".$_SESSION['customer_info']['name']:""?></div>
				<?php if($_SESSION['patient_info']){ ?>
				<div id="name_student" style="font-size:17px;"><?= $_SESSION['patient_info']?"Patient Name: "."[".$_SESSION['patient_info']['idno']."] ".$_SESSION['patient_info']['name']:""?></div>
				<?php }else{ ?>
				<div id="name_student" style="font-size:17px;"><?= $_SESSION['student_info']?"Student Name: "."[".$_SESSION['student_info']['idno']."] ".$_SESSION['student_info']['name']." [".$_SESSION['student_info']['course']." ".$_SESSION['student_info']['yr']."]":""?></div>
				<?php } ?>
				<?php if($_SESSION['previewToSession']){echo '<div style="clear:both;"></div><div style="float:left;">'.$_SESSION['customer_info']['transtype'].' | Dated: '.$_SESSION['customer_info']['date'].'</div>';} ?>
				<? if($_SESSION['shoppers_card']){ ?>
				<div style="font-size:15;">Shoppers ID: <?= $db->customeFormat($_SESSION['shoppers_card']['shoppers_card_id'])." | ".$_SESSION['shoppers_card']['name']."</br>"."Points Earn:".$sumInfo['points']?></div>
				<? } ?>
			</div>
			<div style="float:right;">
				<div id="xtotal" style="float:right;width:375px;font-size:65px;margin-top:10px;text-align:right;margin-right:5px;"></div>
				<div id="xtotal_label" style="float:right;margin:10px -131px 0 0;font-size:13px;"></div>
			</div>
		</div>
	</div>
	<div class="content" style="min-height:150px;">
		<div style="float:left;width:170px;">
			<fieldset style="padding:10px;min-height:150px;">
				<legend>&nbsp; MENU &nbsp;</legend>
				<input id="bt1" class="buthov" type="button" value="Refresh (F5)" onclick="backToBarcode()" style="float:right;height:30px;width:100%;float:left;"/>
				<br/>
				<!--<input id="bt7" class="buthov" type="button" value="Search" onclick="prod_search()" style="float:right;height:30px;width:100%;float:left;"/>
				<br/>-->
				<?php include_once"pos_leftbutton.php"; ?>
				<!--<input type="button" value="View Reading" onclick="viewReading()" style="height:40px;width:100%;float:left;"/>-->
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="padding:5px;">
				<legend>INFO</legend>
				<div style="float:left;">Reading:</div>
				<div style="float:right;"><?=$_SESSION['readingnum']?></div>
				<br/>
				<div style="float:left;">Cashier:</div>
				<div style="float:right;"><?=$_SESSION['xuser_pos']?></div>
				<br/>
				<div style="float:left;">TransType:</div>
				<div style="float:right;"><?=strtoupper($transtype)?></div>
				<br/>
				<div style="float:left;">Connection:</div>
				<div style="float:right;"><?= $db->constatus;?></div>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="padding:5px;font-size:10px;">
				<legend>LEGEND: Press</legend>
				<div style="float:left;"><span style="color:red;">[F2]</span> (Change Qty)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[F4]</span> (UOM List)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[F8]</span> (Change Price)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[Delete]</span> (Remove Item List)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[=]</span> (Tender Payment)</div>
			</fieldset>
		</div>
		<div style="float:right;width:800px;">
			<form method="post" name="frm_pos">
			<div style="height:330px;overflow:auto;">
				<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th style="border:none;">Barcodes</th>
							<th style="border:none;" width="400px">Desc</th>
							<th style="border:none;">Price</th>
							<th style="border:none;">Qty</th>
							<th style="border:none;">Unit</th>
							<th style="border:none;">Amount</th>
							<th style="border:none;">Subject</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($_SESSION['sales'])){ $change="";$xtotal=0;?>
							<?php $qty=0;$count=1; foreach($db->subval_sort($_SESSION['sales'],'count',arsort) as $val){ ?>
								<tr id="<?php echo $val['bcode']; ?>">
									<td><a href="javascript:backToBarcode();" class="activationMain"><?php echo $val['bcode']; ?></a></td>
									<td  width="400px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
									<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
									<td style="text-align:right;"><?php echo $val['qty'] ?></td>
									<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
									<td  style="text-align:right;" class="totalamt"><?php echo number_format($val['total'],2) ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['cost'] ?></td>
									<td  style="text-align:right;"><?php echo $val['subjnametype'] ?></td>
								</tr>
							<?php $qty+=(double)$val['qty'];$xtotal+=$val['total'];$count++;} ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<input type="hidden" value="<?=$_SESSION['counter_num']?>" name="counter_num"/>
			<input type="hidden" name="xtype" value="<?=$transtype?>"/>
			<input type="hidden" name="order_cleark" value="<?=($_SESSION['order_info']['sales_clerk']?$_SESSION['order_info']['sales_clerk']:"")?>"/>
			<input type="hidden" name="total" id="pos_total" value="<?=$xtotal?>"/>
			<!-- Tender Payment Area    style="position:relative;bottom:100px;z-index: 1001"-->
			<div id="modal-background"></div>
			<div id="tenderPayment" style="position:relative;bottom:170px;z-index: 1001"></div>
			<!-- Tender Payment Area-->
			</form> <!-- End of Form here.... -->
			<div style="clear:both;height:10px;"></div>
			<fieldset>
				<?php if($_SESSION['previewToSession']){ ?>
				<input id="bt11" class="buthov" type="button" value="Clear" onclick="unsetSession()" style="height:30px;width:150px;float:left;"/>
				<?php }else{ ?>
				<legend style="color:blue;">Scan Barcode OR Press Enter</legend>
				<input onkeypress="return runScript(event,this);" type="text" name="barcode" id="barcode" style="float:left;height:25px;width:100%" value=""/>
				<?php } ?>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<?php if($transtype!="order"){ ?>
				<div style="width:100%;">
					<fieldset style="float:left;">
						<legend>Admin Menu:</legend>
						<button type="button" id="bt3" class="buthov" onclick="readingEnd()" style="height:30px;width:140px;float:left;font-size:11px;">End <span style="font-weight:bold;text-decoration:underline;">R</span>eading</button>
						<button type="button" id="bt11" class="buthov" onclick="salesvoid()" style="height:30px;width:100px;float:left;font-size:11px;">V<span style="font-weight:bold;text-decoration:underline;">o</span>id</button>
						<button type="button" id="bt12" class="buthov" onclick="discounting_approval()" style="height:30px;width:120px;float:left;font-size:11px;">D<span style="font-weight:bold;text-decoration:underline;">i</span>scounting</button>
						<button type="button" id="bt7" class="buthov" onclick="reprintApproval()" style="height:30px;width:100px;float:left;font-size:11px;">Reprint</button>
						<button type="button" id="bt18" class="buthov" onclick="showTouchScreen()" style="height:30px;width:120px;float:left;font-size:11px;">Touch Screen</button>
					</fieldset>
					<fieldset style="width:80px;float:right;">
						<legend>QTY</legend>
						<div style="font-size:17px;font-weight:bold;height:30px;text-align:center;"><?=$qty?></div>
					</fieldset>
				</div>
			<?php } ?>
		</div>
		<!--<input type="hidden" id="prevchange"/>-->
		<div style="clear:both;height:10px;"></div>
	</div>
	<div id="reprintreceipt"></div>
	<div id="dialogbox"></div>
	<div id="frm_prevchange"></div>
</div>
<script>
var system_name = '<?=$_SESSION['settings']['system_name']?>';
$(document).ready(function() {
	<? if($_SESSION['counter_num']==0 && isset($_SESSION['xuser_pos'])){ ?>
		alertMsg("Unable to proceed No Counter Number assigned...");
		signOut();
	<? } ?>
	 $("#xtotal").html('<?php echo number_format($xtotal,2);?>');
	<? if($_REQUEST['change']){ ?>
		$("#changebutton").focus();
	<? }else{ ?>
		$('#barcode').focus();
	<? } ?>
	<?php if($_REQUEST['specialcase']==1){ ?>
		openPaymentBox();
	<?php } ?>
});
$(document).bind('keydown',function(e){
	var ctrlPressed=0;
	var altPressed=0;
	var shiftPressed=0;
	var evt = (e==null ? event:e);
	shiftPressed=evt.shiftKey;
	altPressed  =evt.altKey;
	ctrlPressed =evt.ctrlKey;
	
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode == 61 || chCode == 107){ //pressing =
		tenderPayment(e);
	}else if(chCode==112){ //pressing f1
		order_list2();
	}else if(chCode==120){
		cashOut();
	}else if(altPressed && chCode==87){
		reprintReceipt();
	}else if(chCode==121){
		suspend();
	}else if(altPressed && chCode==65){
		cashDetails();
	}else if(altPressed && chCode==80){
		changePass();
	}else if(altPressed && chCode==82){
		readingEnd();
	}else if(altPressed && chCode==67){
		show_custlist();
	}else if(altPressed && chCode==79){
		salesvoid();
	}else if(altPressed && chCode==73){
		discounting_approval();
	}
	//alertMsg(chCode);
	//27 esc
});
function selectCategory(catid){
	$.ajax({
		url: './content/pos_ajax.php?execute=setCategory&catid='+catid,
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alertMsg(data);
			}
		}
	});
}
function changeCategory(){
	$('#dialogbox3').dialog({
		autoOpen: false,
		width: 300,
		height: 200,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		title:'Select Category',
		open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
	});
	htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=selectcategory',async:false});
	$('#dialogbox3').html(htmlobj.responseText);
	$('#dialogbox3').dialog('open');
}
function packages(){
	var urls = getUrl();
	$("#prodlist").html("");
	clickDialog('prodlist',1000,550,'packageslist','Package List',urls);
	jQuery.tableNavigation();
	$("#search_prodname").focus();
}
function showTouchScreen(){
	/*var w = 1000;
	var h = 600;
	var left = (screen.width/2)-(w/2);
	var top = ((screen.height/2)-(h/2))-20;
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=touchscreen_prodlist',"ToucheScreen Ordering","dialogWidth:"+w+"px;dialogHeight:"+h+"px;dialogTop:"+top+";dialogLeft:"+left);
	} else {
		window.open('index.php?page=touchscreen_prodlist',"ToucheScreen Ordering",'location=no,height='+h+',width='+w+',toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes,top='+top+', left='+left);
	}*/
	showTS();
}
$("#mytbl").click(function(e){
	jQuery.tableNavigationMain();
	return false;
});

/*$( "#mytbl" ).mouseover(function() {
	jQuery.tableNavigationMain();
	return false;
});*/
function unsetSession(){
	$.ajax({
		url: './content/pos_ajax.php?execute=unsetSession',
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alertMsg(data);
			}
		}
	});
}
function shoppers_card(){
	clickDialog('dialogbox',400,200,'shoppers_card','Shoppers Card');
}
function addNewProd(){
	//var win=window.open('index.php?page=prod_maintenance_whole','_blank');
	//win.focus();
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=prod_maintenance_whole',"Prod Maintenance","dialogWidth:1050px;dialogHeight:550px");
	} else {
		window.open('index.php?page=prod_maintenance_whole',"Prod Maintenance",'height=550,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function input_tra(){
	clickDialog('dialogbox',400,200,'input_tra','Input TRA');
}
function input_moreinfo(){
	clickDialog('dialogbox',400,300,'input_moreinfo','More Info');
}
function show_custlist(){
	clickDialog('dialogbox',900,400,'customerlist','Customer List');
}
function show_studentlist(){
	clickDialog('dialogbox',700,400,'studentlist','Student List');
}
function viewPDFpreview(url,num){
	$('#xlogin').dialog({
		autoOpen: false,
		width: 750,
		height: 600,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:'Receipt Preview'
	});
	$('#xlogin').html("<iframe style='width:700px;height:500px;' id='myiframe' name='myname' src='" + url+num + "' /><br/><input type='button' value='Print' onclick='reprintReceipt();'/>");
	$('#xlogin').dialog('open');
}
function previewReceipt(){
	var num = prompt("Enter TRA/DR Number:");
	if(num !=""){
		if($.isNumeric(num)){
			viewPDFpreview('./reports/receipt_tra.php?receipt_num=',num);
		}else{
			alertMsg("Please input number value only...");
		}
	}else{
		alertMsg("Please provide TRA/DR Num...");
	}
}
function previewToSession(){
	var num = prompt("Enter TRA/DR Number:");
	if(num !=""){
		if($.isNumeric(num)){
			$.ajax({
				url: './content/pos_ajax.php?execute=previewToSession&receipt_num='+num,
				type:"POST",
				success:function(data){
					if(data=="success"){
						//window.location.reload();
						window.location=document.URL;
					}else{
						alertMsg(data);
					}
				}
			});
		}else{
			alertMsg("Please input number value only...");
		}
	}else{
		alertMsg("Please provide TRA/DR Num...");
	}
}

function loadiFrame(src){
	$("#iframeplaceholder").html("<iframe id='myiframe' name='myname' src='" + src + "' />");
	//$("#iframeplaceholder").html("<iframe type='application/pdf' id='myiframe' name='myname' src='" + src + "' />");
	//$("#iframeplaceholder").html("<embed type='application/pdf' src='" + src + "' id='myiframe'></embed>");
	//style='z-index:9999;width:700px;height:500px;position:absolute;margin:30px 0 5px -800px;'
}
function PrintIframe(change,payment_type) { 
	$("#myiframe").load( 
		function() {
			window.frames['myname'].focus();
			window.frames['myname'].print();
			window.frames['myname'].close();
			window.frames['myname'].onafterprint = function () {
				if(payment_type=="Cash"){
					displayChange(parseFloat(change));
				}else{
					//window.location="index.php";
					//window.location.reload();
					window.location=document.URL;
					//window.location="<?=$location?>";
				}
			}
		}
	 );
}
function loadiFrame2(src){
	$("#iframeplaceholder2").html("<iframe id='myiframe2' name='myname2' src='" + src + "' />");
}
function PrintIframe2(change,payment_type) { 
	$("#myiframe2").load( 
		function() {
			window.frames['myname2'].focus();
			window.frames['myname2'].print();
			window.frames['myname2'].close();
		}
	 );
}
function updatesalesvoid(){
	var refid=$("#salesvoid_receipt_num").val();
	$.ajax({
		url: './content/pos_ajax.php?execute=updatesalesvoid&refid='+refid,
		type:"POST",
		success:function(data){
			if(data=="success"){
				alertMsg('Receipt No: '+refid + ' was successfully voided...');
				//window.location="index.php";
				//window.location.reload();
				window.location=document.URL;
			}else{
				alertMsg(data);
			}
		}
	});
}
function salesvoid(){
	loginPermission('salesvoid');
}

// $('div#frm_prevchange').on('dialogclose', function(event) {
 // changeCategory();
// });
function changeCategory(){
	$('#dialogbox3').dialog({
		autoOpen: false,
		width: 300,
		height: 200,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		title:'Select Category',
		open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
	});
	htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=selectcategory',async:false});
	$('#dialogbox3').html(htmlobj.responseText);
	$('#dialogbox3').dialog('open');
}
function displayChange(val){
	//clickDialog('dialogbox',400,150,'displayChange&change='+val,'Change');
	$('#frm_prevchange').dialog({
		autoOpen: false,
		width: 700,
		height: 200,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		title:'Previous Change',
		open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
	});
	htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=displayChange&change='+val,async:false});
	$('#frm_prevchange').html(htmlobj.responseText);
	$('#frm_prevchange').dialog('open');
}
function suspend(){
	var frm = document.frm_pos;
	frm.xtype.value="suspended";
	document.frm_pos.submit();
}
function discounting_approval(){
	var discounting_restricted = "<?=$discounting_restricted?>";
	if(discounting_restricted=="true"){
		loginPermission('discounting');
	}else{
		clickDialog('dialogbox',400,300,'discounting_frm&total_amount='+parseFloat(($('#xtotal').html()).replace(',','')),'Discounting');
		//$('#disc_percent').focus();
	}
}
function saveOrderSlip(data){
	$.ajax({
		url: './content/pos_ajax.php?execute=saveOrderSlip&OrderSlip='+data,
		type:"POST",
		success:function(data){
			if(data=="success"){
				//window.location.reload();
				//window.location.reload();
				$("#barcode").focus();
			}else{
				alertMsg(data);
			}
		}
	});
}
function joinOrder(){
	var val = $("#orderlist tbody tr.selected").find('td:eq(0)').text();
	//alertMsg(val);
	$.ajax({
		url: './content/pos_ajax.php?execute=joinorderToSession&order_receipt='+val,
		type:"POST",
		success:function(data){
			if(data=="success"){
				removeOrder(val);
			}else{
				alertMsg(data);
			}
		}
	});
}
function transferOrder(val){
	//window.opener.setValue(val);
	var total = $("#pos_total").val();
	if(total==""){
		$.ajax({
			url: './content/pos_ajax.php?execute=saveorderToSession&order_receipt='+val,
			type:"POST",
			success:function(data){
				if(data=="success"){
					removeOrder(val);
				}else{
					alertMsg(data);
				}
			}
		});
	}else{
		var r=confirm("There's a pending entry, do you want to join order?");
		if (r==true){
			joinOrder();
		}
	}
}
function removeOrder(val){
	$.ajax({
		url: './content/pos_ajax.php?execute=removeOrder&order_receipt='+val,
		type:"POST",
		success:function(data){
			if(data=="success"){
				$('#dialogbox').dialog('close');
				//window.location="index.php";
				//window.location.reload();
				window.location=document.URL;
			}else{
				alertMsg(data);
			}
		}
	});
}
function order_list2(){
	clickDialog("dialogbox",500,550,"order_list","List Of Order");
	jQuery.tableNavigation();
}
function order_list(){
	if (window.showModalDialog) {
		window.showModalDialog('./content/order_list.php',"Order List","dialogWidth:500px;dialogHeight:500px");
	} else {
		window.open('./content/order_list.php',"Order List",'height=500,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}


$(".tbl tr").hover(
	function(){
		$(this).children().addClass('selected');
		$(this).siblings().children().removeClass('selected');
	},
	function(){
		$(this).children().removeClass('selected');
		$(this).siblings().children().removeClass('selected');
	}
);
$("#modal-launcher, #modal-background, #modal-close").click(function () {
	$('#payment').focus();
});

$("#mytbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		delProdsale($("tr.selected").find('td:eq(0)').first().text());
	}else if(chCode==113){ //pressing f2
		qtyclick($("tr.selected").find('td:eq(0)').first().text());
		//console.log($("tr.selected").find('td:eq(0)').first().text()+"\n");
	}else if(chCode==119){ //pressing f8
		priceclick($("tr.selected").find('td:eq(0)').first().text());
	}else if(chCode==115){ //pressing f4
		uomlist($("tr.selected").find('td:eq(0)').first().text());
		jQuery.tableNavigationUom();
	}
});

function cashDetails(){
	clickDialog("dialogbox",400,500,"cashDetails","Cash Count");
	cashDetailsShow();
}
function cashDetails_del(refid){
	$.ajax({
		url: './content/pos_ajax.php?execute=cashDetails_del',
		type:"POST",
		data: {refid:refid},
		success:function(data){
			cashDetailsShow();
		}
	});
}
function cashDetailsShow(){
	$.ajax({
		url: './content/pos_ajax.php?execute=cashDetailsShow',
		type:"POST",
		success:function(data){
			$("#result").html(data);
		}
	});
}
function cashDetails_add(){
	var money = $("#money").val();
	var count = $("#count").val();
	var total = $("#total").val();
	if(total!=""){
		$.ajax({
			url: './content/pos_ajax.php?execute=cashDetails_add',
			type:"POST",
			data: {money:money,count:count,total:total},
			success:function(data){
				if(data=="success"){
				$("#money").val("");
				$("#count").val("");
				$("#total").val("");
				cashDetailsShow();
				}
			}
		});
	}
}
function cashOut(){
	clickDialog("dialogbox",400,300,"cashout","Cash Out");
	$("textarea[name=cashout_remarks]").focus();
}

function viewReading(){
	window.open('./reports/reading_end.php?readingnum=<?=$_SESSION['readingnum']?>',"Reading End",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
}
function reprintApproval(){
	loginPermission('reprint');
}
function reprintReports(){
	clickDialog("reprintreceipt",250,200,"reprint","RePrint Documents");
	jQuery.tableNavigation();
}
function reprintReceipt(){
	clickDialog("reprintreceipt",700,500,"reprintreceipt","Receipt List");
	jQuery.tableNavigation();
}
function printWS(){
	clickDialog("reprintreceipt",500,500,"printWS","Print WS");
	jQuery.tableNavigation();
}
function readingEnd(){
	loginPermission('reading_end');
}
function viewReceipt(receipt_type,num) {
	if (window.showModalDialog) {
		window.showModalDialog(receipt_type+num,"Receipt","dialogWidth:350px;dialogHeight:350px");
	} else {
		window.open(receipt_type+num,"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}

function tenderPayment(e){
	e.preventDefault();
	var total = $("#xtotal").html();
	//total = Number(total.replace(/[^0-9\.]+/g,""));
	total = Number(total.replace(/,/g, ""));
	var required_cust_name = "<?php echo $required_customer_name?>";
	var customer = required_cust_name=="true"?$("#name_cust").html():'?';
	var view = "<?php echo $view?>";view=new Number(view);
	var orderslip = $("#orderslip").val();
	var name_student = $("#name_student").html();
	console.log(name_student);
	if(total==""||total==0){
		alertMsg("No transaction yet...");
		//location.reload();
	}else if(customer==""){
		alertMsg("Pls specify name of customer...");
		location.reload();
	}else if(view==5 && orderslip==""){
		//$("#orderslip").focus();
		alertMsg("Pls input OrderSlip...","orderslip");
	}else{
		if(total<0){
			//document.frm_pos.submit();
			openRefundBox(total);
		}else{
			if(system_name=='CSACCI'){
			if(name_student==""||name_student=="Patient Name: []  "){
				alertMsg("You didn't specify the Patient name...");
				setTimeout(function(){ location.reload(); }, 2000);
			}
			}
			if(view==4){
				clickDialog('dialogbox',400,200,'restaurant_mode','Restaurant Mode');
			}else{
				openPaymentBox();
				//boxTenderPayment();
			}
		}
	}
}
function openRefundBox(amt){
	$.ajax({
		url: './content/pos_ajax.php?execute=openRefundBox&total='+amt,
		type:"POST",
		success:function(data){
			$('#tenderPayment').html(data);
			$('#payment').focus();
			$("#modal-background").toggleClass("active");
		}
	});
}
function openPaymentBox(){
	//var total = document.frm_pos.total.value;
	var total = $("#xtotal").html();
	total = Number(total.replace(/[^0-9\.]+/g,""));
	$.ajax({
		url: './content/pos_ajax.php?execute=tender_payment&total='+total,
		type:"POST",
		success:function(data){
			$('#tenderPayment').html(data);
			$('#payment').focus();
			$("#modal-background").toggleClass("active");
		}
	});
}
function sumPaymentAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".payment_amt").each(function() {
		var value = $(this).val();
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return sum;
	//return new Number(sum).formatMoney(2);
}
function pricechange(bcode,newprice){
	var price_markup = new Number('<?=$allow_cost_markup?>');
	var allow_price_below_cost = '<?=$allow_price_below_cost?"true":"false";?>';
	var cost = new Number($("tr.selected").find('td:eq(7)').text())==0?new Number($("tbody tr:first").find('td:eq(7)').text()):new Number($("tr.selected").find('td:eq(7)').text());
	// if(newprice>(cost * (1+ price_markup))){
		// alertMsg("Not allowed Price Mark-Up above "+(price_markup*100)+"%");
		// return false;
	// }
	if(allow_price_below_cost=="false"){
		if(newprice < cost){
			alertMsg("Not allowed price below cost...");
			loginPermission('belowcost',{barcode:bcode,newprice:newprice});
			return;
		}
	}
		$.ajax({
			url: './content/pos_ajax.php?execute=pricechange&barcode='+bcode+'&newprice='+newprice,
			type:"POST",
			success:function(data){
				if(data=="success"){
					//window.location.reload();
					window.location=document.URL;
				}else{
					alertMsg(data);
				}
			}
		});
	
}
function updatePayment(val){
	var total = $('#total').val();
	if(new Number(val) < new Number(total)){ //if less than add to details
		var txt = "<tr>"+
					"<td><input type='text' name='payment_type[]' value='"+$("#payment_type option:selected").val()+"' style='width:100%;border:none;'/></td>"+
					"<td style='text-align:right;'><input readonly type='text' class='payment_amt' name='payment[]' value='"+val+"' style='text-align:right;width:100%;border:none;'/></td>"+
					"</tr>";
		$("#multiple_payment").prepend(txt);
		$("#payment").val("");
		$("#payment_type").val("Cash");
		var payment_total = sumPaymentAmt();
		if($("#multiple_payment tbody tr.payment_total").length==0){ //if greater than add total
			$("#multiple_payment").append("<tr class='payment_total'><td>Total</td><td style='text-align:right;'>"+payment_total+"</td></tr>");
		}else{
			$("#multiple_payment tbody tr.payment_total").html("<td>Total</td><td style='text-align:right;'>"+payment_total+"</td>");
		}
		if(payment_total>=new Number(total)){
			$("#multiple_payment").append("<tr><td colspan='2'><input type='button' value='Process' style='height:30px;width:100%;' onclick='savingPayment("+payment_total+")'/></td></tr>");
		}
		//alertMsg("Insufficient Amount...");
	}else{
		savingPayment(val);
	}
}
function savingPayment(val){
	var total = $('#total').val();
	$('#change').val((val-total).toFixed(2));
	document.frm_pos.submit();
}
function paymentGetChar(event,val){
	//var chCode = ('charCode' in event) ? event.charCode : event.keyCode;
	var chCode = event.keyCode==0 ? event.charCode : event.keyCode;
	var key = (event.which) ? event.which : event.keyCode
	if ((key < 48 || key > 57) && !(key == 8 || key == 9 || key == 13 || key == 37 || key == 39 || key == 46 || key == 45 || key == 27) ){
		return false;
	}else{
		if(chCode==27){ //pressing esc
			var r=confirm("Are you sure to ESC payment form?");
			if (r==true){
			  //window.location.reload();
			  $('#barcode').val("");
			  $('#barcode').focus();
			  $('#tenderPayment').html("");
			  $("#modal-background").toggleClass();
			}
		}else if(chCode == 13){
			if(val==null || val==""){
				alertMsg("Payment should not be empty...","payment");
			}else{
				updatePayment(val);
			}
		}
		return true;
	}
}

</script>