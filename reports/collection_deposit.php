<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
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
		text-align:center;
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl td {
		border-width: 1px;
		border-style: none;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
</style>

<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$db->openDb();
$sql= "select a.*,b.customer_name from tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id where date<'".date('Y-m-d')."' and (deposited_date is null or deposited_date='".date('Y-m-d')."')";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
$coll=$db->resultArray("a.*,b.customer_name","tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id","where date='".date('Y-m-d')."'");
$dep=$db->resultArray("a.*,b.customer_name","tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id","where deposited_date='".date('Y-m-d')."'");
$ending_bal=$db->resultArray("a.*,b.customer_name","tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id","where deposited_date is null");
?>
<div class="print" style="width:900px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<h2><?=$db->stockin_header;?><br/>Summary of Collection and Deposit<br/></h2>
	<div style="clear:both;height:10px;"></div>
	
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Type</th>
				<th>Trans Date</th>
				<th>Check Number</th>
				<th>Check Date</th>
				<th>Reference</th>
				<th>Amount</th>
				<th>Time</th>
			</tr>
		</thead>
		<tbody>
			<? 	while($row=mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td>PDC</td>
				<td style="text-align:center;"><?= $row['date']?></td>
				<td style="text-align:center;"><?= $row['check_details']?></td>
				<td style="text-align:center;"><?= $row['check_date']?></td>
				<td style="text-align:center;"><?= $row['receipt']." ".$row['customer_name']?></td>
				<td style="text-align:right;"><?= number_format($row['check_amt'],2)?></td>
				<td>&nbsp;</td>
			</tr>
			<? $begbal_total+=$row['check_amt'];} ?>
			<tr style="font-weight:bold;">
				<td colspan="5">TOTAL BEGINNING BALANCE</td>
				<td style="text-align:right;border-top:1px solid gray;"><?=number_format($begbal_total,2);?></td>
			</tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<?php foreach($coll as $key => $val){ ?>
				<tr>
					<td>COLL</td>
					<td style="text-align:center;"><?= $val['date']?></td>
					<td style="text-align:center;"><?= $val['check_details']?></td>
					<td style="text-align:center;"><?= $val['check_date']?></td>
					<td style="text-align:center;"><?= $val['receipt']." ".$val['customer_name']?></td>
					<td style="text-align:right;"><?= number_format($val['check_amt'],2)?></td>
					<td>&nbsp;</td>
				</tr>
			<?php $coll_total+=$val['check_amt'];} ?>
			<tr style="font-weight:bold;">
				<td colspan="5">TOTAL COLLECTIONS FOR THE DAY</td>
				<td style="text-align:right;;border-top:1px solid gray;"><?=number_format($coll_total,2);?></td>
			</tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr style="font-weight:bold;">
				<td colspan="5">TOTAL COLLECTIONS (Beginning + Collections)</td>
				<td style="text-align:right;"><?=number_format($begbal_total+$coll_total,2);?></td>
			</tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr style="font-weight:bold;"><td colspan="7">LESS: DEPOSITS</td></tr>
			<?php foreach($dep as $key => $val){ ?>
				<tr>
					<td>DEP</td>
					<td style="text-align:center;"><?= $val['date']?></td>
					<td style="text-align:center;"><?= $val['check_details']?></td>
					<td style="text-align:center;"><?= $val['check_date']?></td>
					<td style="text-align:center;"><?= $val['receipt']." ".$val['customer_name']?></td>
					<td style="text-align:right;"><?= number_format($val['check_amt'],2)?></td>
					<td>&nbsp;</td>
				</tr>
			<?php $dep_total+=$val['check_amt'];} ?>
			<tr style="font-weight:bold;">
				<td colspan="5">TOTAL DEPOSITS FOR THE DAY</td>
				<td style="text-align:right;;border-top:1px solid gray;"><?=number_format($dep_total,2);?></td>
			</tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr style="font-weight:bold;"><td colspan="7">ENDING BALANCE</td></tr>
			<?php foreach($ending_bal as $key => $val){ ?>
				<tr>
					<td>PDC</td>
					<td style="text-align:center;"><?= $val['date']?></td>
					<td style="text-align:center;"><?= $val['check_details']?></td>
					<td style="text-align:center;"><?= $val['check_date']?></td>
					<td style="text-align:center;"><?= $val['receipt']." ".$val['customer_name']?></td>
					<td style="text-align:right;"><?= number_format($val['check_amt'],2)?></td>
					<td>&nbsp;</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
