<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
//and (payment != 'TERM 15DAYS' or payment != 'TERM 30DAYS')
$counter_num = $_SESSION['counter_num']?$_SESSION['counter_num']:$_REQUEST['counter_num'];
$sql ="select sum(amount) as total_sales,payment from tbl_sales_receipt_{$counter_num} where reading='".$_REQUEST['readingnum']."' and counter_num='".$counter_num."' and type != 'VOID' group by payment";
$qry = mysql_query($sql);
while($row=mysql_fetch_assoc($qry)){
	$info[$row['payment']]=$row['total_sales'];
}
$trans = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$counter_num}","where counter_num='".$counter_num."' and reading='".$_REQUEST['readingnum']."'");
$void = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$counter_num}","where counter_num='".$counter_num."' and reading='".$_REQUEST['readingnum']."' and type='VOID'");
$cashout = $db->getWHERE("sum(amount) as total_cashout","tbl_cashout","where counter_num='".$counter_num."' and reading='".$_REQUEST['readingnum']."'");
?>
<style type="text/css">
	table.tbl {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Arial,Verdana, Geneva, Helvetica, Sans-Serif;
		
	}
	table.tbl th {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		height:20px;
		text-align:center;
	}
	table.tbl td {
		border-width: 1px;
		border-style: none;
		border-color: gray;
		background-color: white;
		height:20px;
	}
	table.tbl2 td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		text-align:center;
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
	</style>
<div style="width:900px;font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;font-size:15px;">
	<h3>X-Reading <span style="float:right;">Num: <?=$_REQUEST['readingnum']?></span></h3>
	Counter Num: <?=$counter_num?> <span style="float:right;"><?=date('Y-m-d')?></span>
	<hr/>
	<?php
	echo "Total Transaction: <span style='float:right;'>".number_format($trans['total'],2)."</span><br/>";
	echo "Total Void: <span style='float:right;'>".number_format($void['total'],2)."</span><br/>";
	foreach($info as $key => $val){
		if($key=="" || $key=="CASH"){
			$cash += $val;
		}else{
			if($key=="CHEQUE"){
				$qrycheque = mysql_query("select * from tbl_chequepayment_details where reading='{$_REQUEST['readingnum']}' and counter='{$counter_num}'");
			}
			echo "Total {$key}: <span style='float:right;'>".number_format($val,2)."</span><br/>";
		}
	}
	echo "Total Cash: <span style='float:right;'>".number_format($cash,2)."</span><br/>";
	echo "Total Deduction: <span style='float:right;'>".number_format($cashout['total_cashout'],2)."</span><br/>";
	
	?>
	<hr/>
	Cash On Drawer: <span style="float:right;"><?=number_format($cash-$cashout['total_cashout'],2)?></span>
	<? if($qrycheque){ ?>
		<div style="clear:both;height:30px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<th>Cheque Details</th>
				<th>Amount</th>
			</tr>
	<?	while($cheques = mysql_fetch_assoc($qrycheque)){ ?>
			<tr>
				<td><?=$cheques['cheque_details']?></td>
				<td style="text-align:right;"><?=number_format($cheques['amount'],2)?></td>
			</tr>
	<?	}
		echo "</table>";
	}?>
	<div style="clear:both;height:30px;"></div>
	<?
	$sql= "select tbl_a.*,tbl_cust.customer_name from (select * from tbl_sales_receipt_{$counter_num} where reading='".$_REQUEST['readingnum']."' and counter_num='".$counter_num."' and type != 'VOID') as tbl_a 
		left join (select b.customer_name,a.cust_id,a.receipt from tbl_customers_trans a left join tbl_customers b on a.cust_id=b.cust_id) as tbl_cust on tbl_cust.receipt=tbl_a.receipt_id";
	$qry=mysql_query($sql);?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th>TRA/DR</th>
			<th>Customer Name</th>
			<th>Total</th>
			<th>Terms</th>
		</tr>
	<?$cdtotal=0;while($row=mysql_fetch_assoc($qry)){?>
		<?php $items = $db->resultArray("*","tbl_sales_items","where receipt='".$row['receipt_id']."'"); 
		$receipt_total = 0;
		foreach($items as $key => $val){
		?>
			<tr>
				<td align="right"><?=$val['qty'].$val['unit']?></td>
				<td align="right"><?=$val['item_desc']?></td>
				<td align="center"><?=number_format($val['selling'],2)?></td>
				<td align="right"><?=number_format($val['total'],2)?></td>
			</tr>
		<?php $receipt_total+=$val['total'];} ?>
		<tr>
			<td align="center"><a href="javascript:viewReceipt(<?=$_REQUEST['readingnum'].','.$row['receipt_id']?>)"><b><?=$row['receipt_id']?></b></a></td>
			<td align="center"><b><?=strtoupper($row['customer_name'])?></b></td>
			<td align="center"><b><?=$row['payment']?></b></td>
			<td align="right" style="border-top:1px solid black;border-bottom:1px solid black;"><b><?=number_format($receipt_total,2)?></b></td>
		</tr>
	<?$cdtotal+=$receipt_total;} //$row['amount']?>
		<tr>
			<td colspan="2" align="center"><b>Total</b></td>
			<td colspan="2" align="center" style="border-top:1px solid black;"><b><?=number_format($cdtotal,2)?></b></td>
		</tr>
	</table>
	<div style="clear:both;height:30px;"></div>
	<?
	$sql= "select * from tbl_cashout where counter_num='{$counter_num}' and reading='{$_REQUEST['readingnum']}'";
	$qry=mysql_query($sql);
	if($qry){
	?>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<th>Date</th>
				<th>Remarks</th>
				<th>Total</th>
			</tr>
		<?$cototal=0;while($row=mysql_fetch_assoc($qry)){?>
			<tr>
				<td align="center"><?=$row['date']?></td>
				<td align="center"><?=$row['remarks']?></td>
				<td align="center"><?=number_format($row['amount'],2)?></td>
			</tr>
		<?$cototal+=$row['amount'];}?>
			<tr>
				<td colspan="2" align="center">Total</td>
				<td align="center" style="border-top:1px solid black;"><?=number_format($cototal,2)?></td>
			</tr>
		</table>
	<?}?>
</div>
<?php
$db->closeDb();
//session_destroy();
?>
<script>
/*onload=function(){
	window.print();
	self.close();
	var loc = window.opener.location;
	window.opener.location=loc;
}*/
function viewReceipt(readingnum,num) {
	if (window.showModalDialog) {
		window.showModalDialog("./receipt.php?receipt_num="+num+"&readingnum="+readingnum,"Receipt","dialogWidth:350px;dialogHeight:350px");
	} else {
		window.open("./receipt.php?receipt_num="+num+"&readingnum="+readingnum,"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
</script>