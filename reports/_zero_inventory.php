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
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:date('Y-m-d');
$sql = "select a.sku_id,product_name,barcode,cost,unit from tbl_product_name as a
				right join (select * from tbl_barcodes where divmul=1) as b on a.sku_id=b.sku_id 
			 order by product_name asc";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2>Product Inventory</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Date</div>
		<input style="float:left;" type="text" id="my_date" name="my_date" value="<?=$xdate?>"/>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Barcodes</th>
				<th>Desc</th>
				<th>Cost</th>
				<th>Stock OnHand</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<? 	while($row = mysql_fetch_assoc($qry)){ 
				$inv = $db->invBal($row['sku_id'],$xdate);
				if($inv <= 0){
				$inv_output = $db->outputInvBal($inv,$row['sku_id']);
			?>
				<tr>
					<td><a href="javascript:viewTrans('<?php echo $row['sku_id'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['barcode'] ?></a></td>
					<td><?= $row['product_name']?></td>
					<td style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
					<td style="text-align:center;"><?= $inv_output ?></td>
					<td style="text-align:center;"><?= number_format($inv * $row['cost'],2) ?></td>
				</tr>
			<? $total+=$inv * $row['cost'];
				}
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="4">Sub Total</th>
				<th><?= number_format($total,2) ?></th>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#my_date').datepicker({
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