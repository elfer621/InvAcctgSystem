<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$date = $_REQUEST['date']?$_REQUEST['date']:date('Y-m-d');
$all = mysql_query("select * from tbl_reading where start_date='".$date."' and end_date!='0000-00-00'");
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
	<?php
while($allrow=mysql_fetch_assoc($all)){
	$sql ="select sum(amount) as total_sales,payment from tbl_sales_receipt_{$allrow['counter']} where counter_num='".$allrow['counter']."' and type != 'VOID' group by payment";
	$qry = mysql_query($sql);
	while($row=mysql_fetch_assoc($qry)){
		$info[$row['payment']]=$row['total_sales'];
	}
	$trans = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$allrow['counter']}","where counter_num='".$allrow['counter']."'");
	$void = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$allrow['counter']}","where counter_num='".$allrow['counter']."' and type='VOID'");
	$cashout = $db->getWHERE("sum(amount) as total_cashout","tbl_cashout","where counter_num='".$allrow['counter']."'");
	?>
	Counter Num: <?=$_SESSION['counter_num']?> <span style="float:right;"><?=date('Y-m-d h:i:s A')?></span>
	Reading Num: <span style="float:right;"><?=$allrow['reading_num']?></span>
	<hr/>
	<?php
	echo "Total Transaction: <span style='float:right;'>".number_format($trans['total'],2)."</span><br/>";
	echo "Total Void: <span style='float:right;'>".number_format($void['total'],2)."</span><br/>";
	$cash=0;
	foreach($info as $key => $val){
		if($key=="" || $key=="CASH"){
			$cash += $val;
		}else{
			if($key=="CHEQUE"){
				$qrycheque = mysql_query("select * from tbl_chequepayment_details where reading='{$allrow['reading_num']}' and counter='{$allrow['counter']}'");
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
	<?$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$allrow['counter']."' and reading='".$allrow['reading_num']."'");?>
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
	<?php /*
	<fieldset style="width:100%;text-align:center;">
		<legend>Accumulated Sales:</legend>
		<?php $accum = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$allrow['counter']}","where counter_num='".$allrow['counter']."' and type!='VOID'");
			echo number_format($accum['total'],2);
		?>
	</fieldset>
	*/?>
	<hr/>
	<div style="clear:both;height:25px;"></div>
<?php } ?>
</div>
<?php
$db->closeDb();
//session_destroy();
?>
<script>
onload=function(){
	//window.print();
	//self.close();
	var loc = window.opener.location;
	window.opener.location=loc;
}
</script>