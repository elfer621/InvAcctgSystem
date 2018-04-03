<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
		}
		table.tbl td {
			border-width: 1px;
			border-style: solid;
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
</head>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
//$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:(new DateTime('+1 day'))->format('Y-m-d');
$begdate = $_REQUEST['begdate']?$_REQUEST['begdate']:date('Y-m-01');
$enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d');

$sql="select sum(amount) total_amount,type,group_concat(id) ids from tbl_expenses_list where date_recorded between '$begdate' and '$enddate'".($_REQUEST['acct_type']?"and type='{$_REQUEST['acct_type']}'":"")." group by type order by date_recorded desc";
// $sql="select sum(amount) total_amount,type,group_concat('tin:',tin,'|date_invoice:',date_invoice,'|description:',description,'|invoice_num:',invoice_num,'|amount:',amount,'|address:',address separator '|') list from tbl_expenses_list where date_recorded between '$begdate' and '$enddate' group by type";
mysql_query("SET SESSION group_concat_max_len = 10000000");
$res = $con->resultArray($con->Nection()->query($sql));

?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:10px;"></div>
	<h2><?=$db->stockin_header;?><br/>Purchases / Expenses (<?=($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING")?>)</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;" type="text" id="begdate" name="begdate" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;" type="text" id="enddate" name="enddate" value="<?=$enddate?>"/>
		<select name="acct_type" style="float:left;width:250px;margin-left:10px;height:20px;">
			<option value="">Select Acct Type</option>
			<?php $acct =  $con->resultArray($con->Nection()->query("select distinct type from tbl_expenses_list order by type asc"));
			foreach($acct as $k=> $v){
				echo "<option value='{$v['type']}'>{$v['type']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Search" name="search_date" style="float:left;margin:0 5px;"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>TIN</th>
				<th>DATE RECEIVED</th>
				<th>DESC</th>
				<th>INVOICE NUM</th>
				<th>AMOUNT</th>
				<th>ADDRESS</th>
			</tr>
		</thead>
		<tbody>
		<? 	
		$count=0;
		foreach($res as $key => $row){ 
			?>
				<tr>
					<td colspan="6" style="font-weight:bold;"><?=$row['type']?></td>
				</tr>
				<?php
				$list = $con->resultArray($con->Nection()->query("select * from tbl_expenses_list where id in ({$row['ids']})"));
				foreach($list as $k => $val){
					?>
					<tr>
						<td><?=$val['tin']?></td>
						<td><?=$val['date_invoice']?></td>
						<td><?=$val['description']?></td>
						<td><?=$val['invoice_num']?></td>
						<td style="text-align:right;"><?=number_format($val['amount'],2)?></td>
						<td><?=$val['address']?></td>
					</tr>
					<?
				}
				?>
				
				<tr>
					<td colspan="4" style="font-weight:bold;text-align:center;"><?=$row['type']?> Total</td>
					<td colspan="2" style="text-align:right;font-weight:bold;"><?=number_format($row['total_amount'],2)?></td>
				</tr>
			<? $total+=$row['total_amount'];
			$count++;	
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="4">Sub Total</th>
				<th colspan="2"><?= number_format($total,2) ?></th>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#begdate,#enddate').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});
function viewTrans(sku_id,prod_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:prod_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=prodtrans&sku_id='+sku_id,async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}

</script>
</html>