<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
//and (payment != 'TERM 15DAYS' or payment != 'TERM 30DAYS')
$sql ="select sum(amount) as total_sales,payment,count(*) num from tbl_sales_receipt_{$_SESSION['counter_num']} where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."' and type != 'VOID' group by payment";
$qry = mysql_query($sql);
while($row=mysql_fetch_assoc($qry)){
	$info[$row['payment']]=$row['total_sales'];
	$info[$row['payment']]['tx']=$row['num'];
}
$trans = $db->getWHERE("max(receipt_id) mxid,min(receipt_id) minid,sum(amount) as total","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
$void = $db->getWHERE("sum(amount) as total,count(*) num","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."' and type='VOID'");
$cashout = $db->getWHERE("sum(amount) as total_cashout","tbl_cashout","where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
$voucher=$db->getWHERE("sum(amount) as total","tbl_voucherpayment_details","where counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
?>
<style type="text/css">
table.tbl {
	border-width: 0px;
	border-spacing: 0px;
	border-style: none;
	border-collapse: collapse;
	font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
	
}
table.tbl th {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	height:20px;
	/*background-color:rgb(237,238,240);*/
	text-align:center;
	font-size:12px;
}
table.tbl td {
	font-size:11px;
	border-width: 1px;
	border-style: none;
	border-color: gray;
	background-color: white;
	height:20px;
}
.lbl{
	float:left;margin-right:10px;width:120px;
}
</style>
<div style="width:300px;font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;font-size:15px;">
	<div style="text-align:center;width:100%;"><?php echo $db->receipt_header; ?></div>
	<h3>Z-Reading</h3>
	Dated: <span style="float:right;"><?=date('Y-m-d h:i:s A')?></span><br/>
	Counter Num: <?=$_SESSION['counter_num']?> <span style="float:right;"><?="Reading #: ".$_SESSION['readingnum']?></span>
	
	<hr/>
	<?php
	//echo "Total Transaction: <span style='float:right;'>".number_format($trans['total'],2)."</span><br/>";
	echo "Total Void: Tx: {$void['num']}<span style='float:right;'>".number_format($void['total'],2)."</span><br/>";
	$cashnum=0;$vouchernum=0;
	foreach($info as $key => $val){
		if($key=="" || $key=="CASH"){
			$cash += $val;
			$cashnum+=$val['tx'];
		}else if($key=="SPLITPAYMENT"or$key=="VOUCHER"){
			$sv+=$val;
			$vouchernum+=$val['tx'];
		}else{
			if($key=="CHEQUE"){
				$qrycheque = mysql_query("select * from tbl_chequepayment_details where reading='{$_REQUEST['readingnum']}' and counter='{$_SESSION['counter_num']}'");
			}
			echo "Total {$key}: <span style='float:right;'>".number_format($val,2)."</span><br/>";
		}
	}
	$cash = $cash+($sv-$voucher['total']);
	echo "Total Cash:   Tx: ".$cashnum."<span style='float:right;'>".number_format($cash,2)."</span><br/>";
	echo "Total Voucher:  Tx: ".$vouchernum." <span style='float:right;'>".number_format($voucher['total'],2)."</span><br/>";
	echo "Total Deduction: <span style='float:right;'>".number_format($cashout['total_cashout'],2)."</span><br/>";
	?>
	<hr/>
	<div style="width:100%;">
	Cash On Drawer: <span style="float:right;"><?=number_format($cash-$cashout['total_cashout'],2)?></span>
	</div>
	<div style="clear:both;height:10px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
			<tr>
				<td>NON-VAT SALES</td>
				<td><?=number_format($cash+$voucher['total'],2)?></td>
			</tr>
			<tr>
				<td>VAT SALES</td>
				<td>0.00</td>
			</tr>
			<tr>
				<td>VAT AMOUNT</td>
				<td>0.00</td>
			</tr>
			<tr>
				<td>Start Inv.#</td>
				<td><?=$trans['minid']?></td>
			</tr>
			<tr>
				<td>End Inv.#</td>
				<td><?=$trans['mxid']?></td>
			</tr>
	</table>
	<? if($voucher){
		$qryvoucher = mysql_query("select * from tbl_voucherpayment_details where counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
	?>
		<div style="clear:both;height:30px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<th>Voucher #</th>
				<th>Details</th>
				<th>Amount</th>
			</tr>
	<?	while($row = mysql_fetch_assoc($qryvoucher)){ ?>
			<tr>
				<td><?=$row['voucher_num']?></td>
				<td><?=$row['voucher_details']?></td>
				<td style="text-align:right;"><?=number_format($row['amount'],2)?></td>
			</tr>
	<? } echo "</table>"; }?>
	
	<? if($qrycheque){ ?>
		<div style="clear:both;height:30px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<th>Voucher #</th>
				<th>Details</th>
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
	<?$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th>Money</th>
			<th>Count</th>
			<th>Total</th>
		</tr>
	<?$cdtotal=0;while($row=mysql_fetch_assoc($qry)){?>
		<tr>
			<td align="center"><?=$row['money']?></td>
			<td align="center"><?=$row['count']?></td>
			<td align="center"><?=number_format($row['total'],2)?></td>
		</tr>
	<?$cdtotal+=$row['total'];}?>
		<tr>
			<td colspan="2" align="center">Total</td>
			<td align="center" style="border-top:1px solid black;"><?=number_format($cdtotal,2)?></td>
		</tr>
	</table>
	<div style="clear:both;height:5px;"></div>
	<fieldset style="width:100%;text-align:center;">
		<legend>Accumulated Sales:</legend>
		<?php $accum = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and type!='VOID'");
			echo number_format($accum['total'],2);
		?>
	</fieldset>
</div>
<?php
//$db->closeDb();
//session_destroy();
?>
<script>
onload=function(){
	window.print();
	//self.close();
	var loc = window.opener.location;
	window.opener.location=loc;
}
</script>