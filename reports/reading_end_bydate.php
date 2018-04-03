<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
//and (payment != 'TERM 15DAYS' or payment != 'TERM 30DAYS')
$sql ="select sum(amount) as total_sales,payment from tbl_sales_receipt_{$_SESSION['counter_num']} where date<='".$_REQUEST['date']."' and counter_num='".$_SESSION['counter_num']."' and type != 'VOID' group by payment";
$qry = mysql_query($sql);
while($row=mysql_fetch_assoc($qry)){
	$info[$row['payment']]=$row['total_sales'];
}
$trans = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and date<='".$_REQUEST['date']."'");
$void = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and date<='".$_REQUEST['date']."' and type='VOID'");
$cashout = $db->getWHERE("sum(amount) as total_cashout","tbl_cashout","where counter_num='".$_SESSION['counter_num']."' and date<='".$_REQUEST['date']."'");
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
	border-style: none;
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
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<div style="width:300px;font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;font-size:15px;">
	<div style="text-align:center;width:100%;"><?php echo $db->receipt_header; ?></div>
	<h3>X-Reading <span style="float:right;">Num: <?=$_REQUEST['readingnum']?></span></h3>
	Counter Num: <?=$_SESSION['counter_num']?> <span style="float:right;"><?=date('Y-m-d',$_REQUEST['date'])?></span>
	<hr/>
	<?php
	echo "Total Transaction: <span style='float:right;'>".number_format($trans['total'],2)."</span><br/>";
	echo "Total Void: <span style='float:right;'>".number_format($void['total'],2)."</span><br/>";
	foreach($info as $key => $val){
		if($key=="" || $key=="CASH"){
			$cash += $val;
		}else{
			/*if($key=="CHEQUE"){
				$qrycheque = mysql_query("select * from tbl_chequepayment_details where date='{$_REQUEST['readingnum']}' and counter='{$_SESSION['counter_num']}'");
			}*/
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
	<?$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$_SESSION['counter_num']."' and reading='".$_REQUEST['readingnum']."'");?>
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
	<div style="clear:both;height:10px;"></div>
	<fieldset style="width:100%;text-align:center;border:0;">
		<legend>Accumulated Total:</legend>
		<?php $accum = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$_SESSION['counter_num']."' and type!='VOID' and reading<=".$_REQUEST['readingnum']);?>
		<div style="float:left;width:120px;">Sales:</div>
		<div style="float:left;"><?php echo number_format($accum['total'],2) ?></div>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;width:120px;">VAT:</div>
		<div style="float:left;"><?php echo number_format($accum['total']/ 9.333,2) ?></div>
		<div style="clear:both;height:5px;"></div>
	</fieldset>
	<div id="prodsold"></div>
</div>
<script>
onload=function(){
	var print_productsold = '<?= $print_productsold ?>';
	if(print_productsold==1){
		productSold();
	}
}
function productSold(){
	var reading = '<?= $_REQUEST['readingnum'] ?>';
	var counter = '<?= $_SESSION['counter_num'] ?>';
	$.ajax({
		url: './sales_reports_sm.php?rep=perreading&reading='+reading+'&counter='+counter,
		type:"POST",
		success:function(data){
			$("#prodsold").html(data);
		}
	});
}
</script>