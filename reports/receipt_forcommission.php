<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
if($_POST){
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	foreach($_REQUEST['prodlist'] as $key => $val){
		$sql = "update tbl_sales_items set instructor_code='{$val['instructor_code']}',studentid='{$_REQUEST['studentid']}' where receipt='{$_REQUEST['ornum']}' and counter='{$_REQUEST['counternum']}' and skuid='{$val['skuid']}'";
		$qry=mysql_query($sql);
		if($qry){
			echo "Updated SKUID:{$val['skuid']}<br/>";
		}else{
			echo "Error SKUID:{$val['skuid']}<br/>";
		}
	}
}
$settings = $db->getWHERE("*","settings","where variable_name='receipt_header'");
$readingnum = $_REQUEST['readingnum']?$_REQUEST['readingnum']:$_SESSION['readingnum'];
$counter = $_REQUEST['counter']?$_REQUEST['counter']:$_SESSION['counter_num'];
$sql = mysql_query("select * from tbl_sales_receipt_$counter where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$counter."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$counter."' and reading='".$readingnum."'";
$sql_item = mysql_query($x);
$custname = $db->getWHERE("a.cust_id,customer_name,customer_address","tbl_customers_trans a left join tbl_customers b on a.cust_id=b.cust_id","where receipt='{$_REQUEST['receipt_num']}' and reading='$readingnum' and counter='$counter'");
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../jqgrid/js/jquery-1.7.2.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<div style="width:300px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;float:left;">

<input type="button" value="Subject List" onclick="showStudentDetails()" style="float:left;"/>
<input type="button" value="Submit" onclick="$('#frmcommision').submit();" style="float:right;"/>
<div style="clear:both;height:5px;"></div>
<div style="text-align:center;width:100%;">
<?php echo str_replace(array("|permit|","|serial|","|machine|"),array($permit,$serial,$machine),$settings['variable_values']); ?>
</div>
<form id="frmcommision" method="post">
<?=$_REQUEST['reprint']?'<h3 style="text-align:center;">DUPLICATE COPY</h3><br/>':""?>
<?=$info['payment']=='REFUND'?'<h3 style="text-align:center;">CASH REFUND</h3><br/>':""?>
<?=$info['type']=='VOID'?'<h3 style="text-align:center;">VOID TRANSACTION</h3><br/>':""?>
<?php echo "Date: <span style='float:right;'>".$info['timestamp']."</span><br/>"; ?>
<?php echo "OR#: <input type='text' style='float:right;border:none;text-align:right;' id='ornum' name='ornum' value='".$info['receipt_id']."' readonly />"; ?><br/>
<div style="float:left;width:120px;">
Counter:<input type="text" style="float:right;width:40px;border:none;" id='counternum' name='counternum' value='<?php echo $info['counter_num'] ?>' readonly />
</div>
<div style="float:right;width:120px;">
Reading:<span style="float:right;"><?php echo $db->customeFormat($readingnum) ?></span>
</div>
<?php if($custname){echo "</br>Cust Name: ".$custname['customer_name']."</br>".$custname['customer_address'];} ?>
<div style="clear:both;height:5px;"></div>
<hr/>
<table id="mytbl" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td style="width:100px;">Campus:</td>
		<td style="text-align:right;"><?=strtoupper($_SESSION['connect'])?></td>
	</tr>
	<tr>
		<td style="width:100px;">StudentID:</td>
		<td style="text-align:right;" ><input type="text" id="studentid" name="studentid" value="<?=$info['studentid']?>" readonly style="border:none;text-align:right;"/></td>
	</tr>
	<tr>
		<td style="width:100px;">StudentName:</td>
		<td style="text-align:right;" onclick="show_studentlist('<?=strtoupper($info['studentname'])?>')"><?=strtoupper($info['studentname'])?></td>
	</tr>
	<tr>
		<td style="width:100px;">Course & Yr:</td>
		<td style="text-align:right;"><?=$info['course']." ".$info['yr']?></td>
	</tr>
</table>
<div style="clear:both;height:5px;"></div>
<hr/>
<div style="width:100%;"> <!--border-bottom:1px solid black;border-top:1px solid black;padding:3px;-->
	<table border='0' style="width:100%;">
		<tr>
			<td>Desc / Qty / Price</td>
			<td>Amt</td>
			<td style="text-align:right;">Instructor #</td>
		</tr>
	</table>
</div>
<hr/>
<?php
$total_amount=0;
if($info['type']=='VOID'){
	//echo "<div style='text-align:center;width:100%;'><h3>RECEIPT WAS VOID</h3></div>";
	$prod = $db->getWHERE("*","tbl_sales_voiditems","where receipt='{$_REQUEST['receipt_num']}' and counter='{$_SESSION['counter_num']}' and reading='$readingnum'");
	foreach(unserialize($prod['item_array']) as $k=>$row_items){
		echo strtoupper($row_items['item_desc'])."<br/>";
		echo "<span style='font-size:15px;'>&nbsp;&nbsp;&nbsp;&nbsp;{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;
		@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format(($row_items['selling']*$row_items['qty']),2)."</span></span> <br/>";
	$total_amount+=($row_items['selling']*$row_items['qty']);
	}
}else{
	echo "<table border='0'>";
	$count=0;
	while($row_items = mysql_fetch_assoc($sql_item)){
		echo "<tr>
			<td colspan='2'>".strtoupper($row_items['item_desc'])."</td>
			<td>
				<input type='hidden' name='prodlist[{$count}][skuid]' value='{$row_items['skuid']}' style='width:80px;'/>
				<input type='text' name='prodlist[{$count}][instructor_code]' value='".($row_items['instructor_code']?$row_items['instructor_code']:"")."' style='width:80px;'/>
			</td>
		</tr>
		<tr>
			<td>{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."</td>
			<td style='text-align:right;' colspan='2'>".number_format(($row_items['selling']*$row_items['qty']),2)."</td>
		</tr>";
		// echo strtoupper($row_items['item_desc'])."<br/>";
		// echo "<span style='font-size:15px;'>&nbsp;&nbsp;&nbsp;&nbsp;{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;
		// @ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format(($row_items['selling']*$row_items['qty']),2)."</span></span> <br/>";
	$total_amount+=($row_items['selling']*$row_items['qty']);
	//$vat+=$row_items['vat'];
	$count++;
	}
	echo "</table>";
}
echo "<hr/>";
echo "Total:     <span style='float:right;'>".number_format($info['amount'],2)."</span><br/>";
if($info['payment']=="SPLITPAYMENT"){
	$sp = $db->resultArray("*","tbl_sales_splitpayment","where receipt_id='{$_REQUEST['receipt_num']}' and counter='{$_SESSION['counter_num']}' and reading='$readingnum'");
	foreach($sp as $key => $val){
		echo "Tender: {$val['payment_type']}    <span style='float:right;'>".number_format($val['payment_amt'],2)."</span><br/>";
	}
}else{
	echo "Tender: {$info['payment']}    <span style='float:right;'>".number_format($info['tender'],2)."</span><br/>";
}
if($info['payment']=="VOUCHER"){
	echo "Voucher Bal:     <span style='float:right;'>".number_format($info['change'],2)."</span><br/>";
}else{
	echo "Change:     <span style='float:right;'>".number_format($info['change'],2)."</span><br/>";
}
echo "<div style='clear:both;height:10px;'></div>";
echo "<div style='float:left;width:230px;'>VAT Sales:     <span style='float:right;'>".number_format($info['vat']-($info['vat']/9.333),2)."</span></div><br/>";
echo "<div style='float:left;width:230px;'>VAT-ExemptSales:     <span style='float:right;'>".number_format($total_amount-$info['vat'],2)."</span></div><br/>";
echo "<div style='float:left;width:230px;'>Zero-Rated Sales:     <span style='float:right;'>".number_format(0,2)."</span></div><br/>";
//echo "<div style='float:left;width:200px;'>VAT Amt:     <span style='float:right;'>".number_format(($total_amount / 9.333),2)."</span></div><br/>";
echo "<div style='float:left;width:230px;'>SC/PWD Disc:     <span style='float:right;'>".number_format(0,2)."</span></div><br/>";
echo "<div style='float:left;width:230px;'>VAT Amt:     <span style='float:right;'>".number_format($info['vat']/9.333,2)."</span></div><br/>";
echo "<div style='clear:both;height:5px;'></div>";
echo "<div style='float:left;width:230px;'>Cashier:     <span style='float:right;'>".$info['cashier']."</span></div><br/>";
echo "<div style='float:left;width:230px;'>OrderSlip:     <span style='float:right;'>".$info['orderslip']."</span></div><br/>";
echo "<div style='float:left;width:230px;'>Payment:     <span style='float:right;'>".$info['payment']."</span></div><br/>";
echo '<div style="clear:both;height:5px;"></div>';
echo "</form>";
if($info['payment']=="CHEQUE"){
	$paymentInfo = $db->getWHERE("*","tbl_chequepayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='$readingnum' and counter='$counter'");
	echo "<div style='float:left;'>Details:</div><br/>";
	echo $paymentInfo['cheque_details'];
}else if($info['payment']=="CREDITCARD"){
	$paymentInfo = $db->getWHERE("*","tbl_creditcardpayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='$readingnum' and counter='$counter'");
	echo "<div style='float:left;'>Details:</div><br/>";
	echo $paymentInfo['cc_num']."/".$paymentInfo['cc_custname']."/".$paymentInfo['cc_approval']."/".$paymentInfo['card_type'];
}else if($info['payment']=="VOUCHER"){
	$paymentInfo = $db->getWHERE("*","tbl_voucherpayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='$readingnum' and counter='$counter'");
	echo "<div style='float:left;'>Details:</div><br/>";
	echo $paymentInfo['voucher_num']." / ".$paymentInfo['voucher_details']." / ".number_format($paymentInfo['amount'],2);
}else if($info['payment']=="SPLITPAYMENT"){
	print_r($paymentInfo);
	$paymentInfo = $db->getWHERE("*","tbl_voucherpayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='$readingnum' and counter='$counter'");
	echo "<div style='padding-left:10px;'>";
	echo "<div style='float:left;width:200px;'>VOUCHER: <span style='float:right;'>".number_format($paymentInfo['amount'],2)."</span></div><br/>";
	echo $paymentInfo['voucher_num']." / ".$paymentInfo['voucher_details']."<br/>";
	echo "<div style='float:left;width:200px;'>CASH: <span style='float:right;'>".number_format($total_amount-$paymentInfo['amount'],2)."</span></div><br/>";
	echo "</div>";
}
if($_SESSION['disc_info']){ ?>
<div style="clear:both;height:10px;"></div>
<div style="text-align:center;width:100%;"><h3>DISCOUNTING</h3></div>
<div style="float:left;width:120px;">Disc Type:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['disc_type'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Cust Name:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['cust_name'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">ID Num:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['id_num'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Details:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['details'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Total Disc:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['total_disc'] ?></div>
<div style="clear:both;height:25px;"></div>
<hr/>
<div style="text-align:center;width:100%;font-size:10px;">SIGNATURE</div>
<?php } ?>
<div style="clear:both;height:5px;"></div>
<?php if($info['type']!='VOID' && $info['payment']!='REFUND'){ ?>

<table id="mytbl" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td style="width:100px;">SOLD TO:</td>
		<td style="border-bottom:1px solid #000;"><?=strtoupper($info['studentname'])?></td>
	</tr>
	<tr>
		<td>ADDRESS:</td>
		<td style="border-bottom:1px solid #000;text-align:center;">CEBU</td>
	</tr>
	<tr>
		<td>TIN:</td>
		<td style="border-bottom:1px solid #000;">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">BUS STYLE, IF ANY:</td>
	</tr>
	<tr>
		<td colspan="2" style="border-bottom:1px solid #000;">&nbsp;</td>
	</tr>
</table>
<div style="clear:both;height:30px;"></div>
<div style="text-align:center;width:100%;"><?=$db->receipt_footer;?></div>
<?php } ?>
<?php if($info['payment']=='REFUND'){ ?>
<table id="mytbl" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td style="width:100px;" colspan="2">Cash Received By:</td>
	</tr>
	<tr>
		<td style="width:100px;" colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td style="border-bottom:1px solid #000;" colspan="2"><?=strtoupper($info['studentname'])?></td>
	</tr>
</table>
<div style="clear:both;height:30px;"></div>
<?php } ?>
</div>
<div id="studentdetails" style="margin-left:10px;float:left;"></div>
<div id="dialog"></div>
<?php
$db->closeDb();
if($_SESSION['disc_info']){
	unset($_SESSION['disc_info']);
}
?>
<script>
function show_studentlist(name){
	clickDialogUrl('dialog',700,400,'../content/pos_ajax.php?execute=studentlist&name='+name+'&com=true','Student List');
}
function lookup(inputString) {
	var type=$('input[name="gradelevel"]:checked').val();
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#autoSuggestionsList').hide();
	} else {
		$.post("../content/pos_ajax.php?execute=studentsearch&type="+type, {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#autoSuggestionsList').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
function fill(id,name,course,yr) {
	$("#idnum").val(id);
	$("#sname").val(name);
	$("#course").val(course);
	$("#yr").val(yr);
	setTimeout("$('#autoSuggestionsList').hide();", 200);
}
function showStudentDetails(){
	$.ajax({
			url: '../content/pos_ajax.php?execute=studentdetails&refid='+$("#studentid").val(),
			type:"POST",
			success:function(data){
				$("#studentdetails").html(data);
			}
		});
}
function update(){
	var ornum = $('#ornum').val();
	var counternum = $('#counternum').val();
	var studentid = $("#idnum").val();
	$.ajax({
		url: '../content/pos_ajax.php?execute=updateReceiptWithStudentID&counternum='+counternum+'&ornum='+ornum+'&studentid='+studentid,
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
</script>