<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
<!--<link rel="stylesheet" href="../css/tblstyle.css" type="text/css" />-->
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: solid;
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
		.logo {
			background-image: url('../images/tkc.png');
			background-size: 90px 60px;
			background-repeat: no-repeat;
			background-position: left top;
			text-align:left;
			padding-top:10px;
			width:100%;
		}
		.logo h1{
			position:relative;
			left: 110px;
		}
		@media print {
			* {-webkit-print-color-adjust:exact;}
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
mysql_query("SET SESSION group_concat_max_len = 10000000");
$date = $_REQUEST['begdate']?"where a.date='{$_REQUEST['begdate']}'":"";
$sql="select a.*,b.total,cust.customer_name from tbl_sales_invoice_header a 
				left join (select refid,sum(amount) total from tbl_sales_invoice_items group by refid) b on a.id=b.refid 
				left join tbl_customers cust on a.cust_id=cust.cust_id 
				$date";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<div class="logo">
		<h1>TKC</h1>
		<span style="font-size:12px;">17 Bulacao Pardo, Cebu City 6000<br/>
		Tel. Nos.: 505-01111,505-0222,505-0333 Fax No.: 505-0666<br/>
		TIN : 005-255-269-000</span>
	</div>
	<h2 align="center">DAILY SALES REPORT</h2>
	<div style="clear:both;height:20px;"></div>
	<form method="post" name="frmFilter">
		<fieldset>
			<legend>Filter</legend>
			<div style="float:left;width:50px;">Beg Date</div>
			<input type="text" name="begdate" id="begdate" value="<?=$_REQUEST['begdate']?>" style="float:left;width:150px;margin-right:20px;"/>
			<input type="submit" value="Execute" style="float:right;width:150px;"/>
		</fieldset>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%" >
		<thead>
			<tr>
				<th>SI #</th>
				<th>Customer Name</th>
				<th>Date</th>
				<th>Amount</th>
				<th>Prepared By</th>
			</tr>
		</thead>
		<tbody>
			<? 	
			$total=0;
			while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><?=$row['id']?></td>
					<td><?=$row['customer_name']?></td>
					<td style="text-align:center;"><?=$row['date']?></td>
					<td style="text-align:right;"><?=number_format($row['total'],2)?></td>
					<td style="text-align:right;"><?=$row['preparedby']?></td>
				</tr>
			<?php $total+=$row['total'];} ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">Total Sales</td>
				<td colspan="2" style="text-align:center;font-size:15px;"><?=number_format($total,2)?></td>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
</body>
<script>
$(document).ready(function(){
	$('#begdate,#enddate').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
});
</script>
</html>