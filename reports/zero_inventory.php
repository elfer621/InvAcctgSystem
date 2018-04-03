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

$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:date('Y-m-d');
$sql="select tbl_2.*,prod.product_name,bcode.barcode,bcode.price,bcode.cost,bcode.unit,tbl_2.item_desc from (select skuid,sum(in_total) in_total,sum(out_total) out_total,sum(in_total-out_total) bal_total,item_desc from (
	select skuid,coalesce(sum(qty*coalesce(divmul,1)),0) in_total,0 out_total,item_desc from tbl_stockin_items group by skuid
	union
	select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total,item_desc from tbl_sales_items group by skuid
	union
	select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total,item_desc from tbl_stockout_items group by skuid) tbl_1 group by skuid) tbl_2 
	left join tbl_product_name prod on tbl_2.skuid=prod.sku_id 
	left join tbl_barcodes bcode on tbl_2.skuid=bcode.sku_id 
	where bal_total<=0 order by prod.product_name asc";
$res = $con->resultArray($con->Nection()->query($sql));
// echo "<pre>";
// print_r($res);
// echo "</pre>";
// exit;
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2><?=$db->stockin_header;?><br/>Product Inventory (<?=($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING")?>)</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Date</div>
		<input style="float:left;" type="text" id="my_date" name="my_date" value="<?=$xdate?>"/>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Barcode</th>
				<th>Desc</th>
				<th>Cost</th>
				<th>Stock OnHand</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($res as $key => $row){ 
			?>
				<tr>
					<td><a href="javascript:viewTrans('<?php echo $row['skuid'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['skuid'] ?></a></td>
					<td><?= $row['barcode']?></td>
					<td><?= $row['item_desc']?></td>
					<td style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
					<td style="text-align:center;"><?= $row['bal_total'] ?></td>
					<td style="text-align:center;"><?= number_format($row['bal_total'] * $row['cost'],2) ?></td>
				</tr>
			<? $total+=($row['bal_total'] * $row['cost']);
				
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="5">Sub Total</th>
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