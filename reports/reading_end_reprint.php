<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
//and (payment != 'TERM 15DAYS' or payment != 'TERM 30DAYS')
$sql ="select sum(amount) as total_sales,payment from tbl_sales_receipt_{$_REQUEST['counter_num']} where reading='".$_REQUEST['readingnum']."' and counter_num='".$_REQUEST['counter_num']."' and type != 'VOID' group by payment";
$qry = mysql_query($sql);
while($row=mysql_fetch_assoc($qry)){
	$info[$row['payment']]=$row['total_sales'];
}
$trans = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_REQUEST['counter_num']}","where counter_num='".$_REQUEST['counter_num']."' and reading='".$_REQUEST['readingnum']."'");
$void = $db->getWHERE("sum(amount) as total","tbl_sales_receipt_{$_REQUEST['counter_num']}","where counter_num='".$_REQUEST['counter_num']."' and reading='".$_REQUEST['readingnum']."' and type='VOID'");
$cashout = $db->getWHERE("sum(amount) as total_cashout","tbl_cashout","where counter_num='".$_REQUEST['counter_num']."' and reading='".$_REQUEST['readingnum']."'");
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
	<h3>X-Reading (Reprint) <span style="float:right;">Num: <?=$_REQUEST['readingnum']?></span></h3>
	Counter Num: <?=$_REQUEST['counter_num']?> <span style="float:right;"><?=date('Y-m-d')?></span>
	<hr/>
	<?php
	echo "Total Transaction: <span style='float:right;'>".number_format($trans['total'],2)."</span><br/>";
	echo "Total Void: <span style='float:right;'>".number_format($void['total'],2)."</span><br/>";
	foreach($info as $key => $val){
		if($key=="" || $key=="CASH"){
			$cash += $val;
		}else{
			if($key=="CHEQUE"){
				$qrycheque = mysql_query("select * from tbl_chequepayment_details where reading='{$_REQUEST['readingnum']}' and counter='{$_REQUEST['counter_num']}'");
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
	<?$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$_REQUEST['counter_num']."' and reading='".$_REQUEST['readingnum']."'");?>
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
</div>
<?php
//$db->closeDb();
//session_destroy();
?>
<script>
onload=function(){
	window.print();
	//self.close();
	//var loc = window.opener.location;
	//window.opener.location=loc;
}
</script>