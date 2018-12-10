<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
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
$date = $_REQUEST['begdate']?"where refid in (select id from tbl_sales_invoice_header where date between '{$_REQUEST['begdate']}' and '{$_REQUEST['enddate']}')":"";
$sql="select a.*,b.agent_name,b.id code from 
	(select y.cust_id,y.rep,sum(unitprice*qty) totselling,sum(cost*qty) totcost from (select * from tbl_sales_invoice_items $date) x 
		left join tbl_sales_invoice_header y on y.id=x.refid group by y.rep) a 
left join req_agent b on a.rep=b.id 
";

$res = $con->resultArray($con->Nection()->query($sql));
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2><?=$db->stockin_header;?><br/>Sales Summary Per Agent (<?=($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING")?>)</h2>
	<div style="clear:both;height:20px;"></div>
	<fieldset>
		<legend>Filter</legend>
		<form method="post">
		<div style="float:left;margin-right:10px;">Beg Date</div>
		<input type="text" name="begdate" id="begdate" style="float:left;width:150px;margin-right:20px;" value="<?=$_REQUEST['begdate']?$_REQUEST['begdate']:date('Y-m-01')?>"/>
		<div style="float:left;margin-right:10px;">End Date</div>
		<input type="text" name="enddate" id="enddate" style="float:left;width:150px;margin-right:20px;" value="<?=$_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d')?>"/>
		<input type="submit" value="Execute" style="float:left;width:200px;"/>
		</form>
	</fieldset>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Code</th>
				<th>Agent Name</th>
				<th>Sales</th>
				<th>Cost</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($res as $key => $row){ 
				
			?>
				<tr>
					<td><?= $row['code']?></td>
					<td><?= $row['agent_name']?></td>
					<td style="text-align:right;"><?= number_format($row['totselling'],2) ?></td>
					<td style="text-align:right;"><?= number_format($row['totcost'],2) ?></td>
					<td style="text-align:center;">
						<a href="javascript:viewReport('./sales_invoice_per_agent_summary_perproduct.php?rep=<?=$row['code']?>&begdate=<?=$_REQUEST['begdate']?>&enddate=<?=$_REQUEST['enddate']?>');">Per Prod</a> | 
						<a href="javascript:viewReport('./sales_invoice_per_agent_summary_percust.php?rep=<?=$row['code']?>&begdate=<?=$_REQUEST['begdate']?>&enddate=<?=$_REQUEST['enddate']?>');">Per Cust</a></td>
				</tr>
			<? 
				
			} ?>
		</tbody>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
	<div style="clear:both;height:20px;"></div>
</body>
<script>
$(document).ready(function() {
	$('#begdate,#enddate').datepicker({
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
function viewReport(page){
	var win=window.open(page,'_blank');
	win.focus();
}
</script>
</html>