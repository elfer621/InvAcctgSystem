<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
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
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl tbody td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
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
	body,td {
			font-size:15px !important;
		}
</style>
<style type="text/css" media="print" >
	thead {display: table-header-group !important;}
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
$sql="select b.description,sum(amount) total,group_concat('<tr><td>',a.date,'</td><td>',a.reference,'</td><td>',concat(a.qty,a.unit),'</td><td>',a.rate,'</td><td style=\"text-align:right;\">',a.amount,'</td>' separator '</tr>' ) details from tbl_soa_other_charges_daily a left join tbl_soa_other_charges_category_name b on a.category_id=b.category_id where a.custid='{$_REQUEST['custid']}' and a.forthemonth='{$_REQUEST['forthemonth']}' group by a.category_id";
$list=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
// echo "<pre>";
// print_r($list);
// echo "</pre>";
?>
<div class="print" style="width:950px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="print tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="5"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Other Daily Charges</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<th>Category Name</th>
				<th>Reference</th>
				<th>Qty/Unit</th>
				<th>Rate</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<? 	foreach($list as $key => $row){ ?>
				<tr><td colspan="5" style="font-weight:bold;"><?=$row['description']?></td></tr>
				<?=$row['details']?>
				<tr><td colspan="3" style="font-weight:bold;">Total</td><td colspan="2" style="text-align:right;font-weight:bold;"><?=number_format($row['total'],2)?></td></tr>
				<tr><th colspan="5" style="border:none!important;">&nbsp;</th></tr>
			<? 	
			} ?>
		</tbody>
		<tfoot>
			
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
<script>
function viewReceipt(num,readingnum,counter) {
	if (window.showModalDialog) {
		window.showModalDialog('./receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt","dialogWidth:350px;dialogHeight:350px");
	} else {
		window.open('./receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
</script>
