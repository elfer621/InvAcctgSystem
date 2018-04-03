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
//$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:(new DateTime('+1 day'))->format('Y-m-d');
$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:date('Y-m-d');
$prod = $_REQUEST['prod_name']?"and prod.product_name like '%{$_REQUEST['prod_name']}%'":"";
$sup = $_REQUEST['supplier_name']?"and sup.supplier_name like '%{$_REQUEST['prod_name']}%'":"";
$sql="select tbl_2.*,prod.lotno,prod.expdate,prod.product_name,bcode.barcode,bcode.price,bcode.cost,bcode.unit,tbl_2.item_desc from (select skuid,sum(in_total) in_total,sum(out_total) out_total,sum(in_total-out_total) bal_total,item_desc from (
	select skuid,coalesce(sum(qty*coalesce(divmul,1)),0) in_total,0 out_total,item_desc from tbl_stockin_items group by skuid
	union
	select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total,item_desc from tbl_sales_items group by skuid
	union
	select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total,item_spec from tbl_sales_invoice_items group by skuid
	union
	select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total,item_desc from tbl_stockout_items group by skuid) tbl_1 group by skuid) tbl_2 
	left join tbl_product_name prod on tbl_2.skuid=prod.sku_id 
	left join tbl_barcodes bcode on tbl_2.skuid=bcode.sku_id 
	left join tbl_supplier sup on prod.supplier_id=sup.id
	where bal_total>0 $prod $sup order by prod.product_name asc";
$res = $con->resultArray($con->Nection()->query($sql));
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2><?=$db->stockin_header;?><br/>Product Inventory (<?=($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING")?>)</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Date</div>
		<input style="float:left;" type="text" id="my_date" name="my_date" value="<?=$xdate?>"/>
		<div style="float:left;margin:0 5px;width:80px;">ProductName</div>
		<input type="text" name="prod_name" style="float:left;width:100px;" value="<?=$_REQUEST['prod_name']?>"/>
		<div style="float:left;margin:0 5px;width:80px;">Supplier</div>
		<input type="text" name="supplier_name" style="float:left;width:100px;" value="<?=$_REQUEST['supplier_name']?>"/>
		<input type="submit" value="Search" name="search_date" style="float:left;margin:0 5px;"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Barcodes</th>
				<th>Desc</th>
				<th>Lot No</th>
				<th>Exp Date</th>
				<th>Cost</th>
				<th>Stock OnHand</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($res as $key => $row){ 
				
			?>
				<tr>
					<td><a href="javascript:viewTrans('<?php echo $row['skuid'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['barcode'] ?></a></td>
					<td><?= $row['item_desc']?></td>
					<td><?= $row['lotno']?></td>
					<td><?= $row['expdate']?></td>
					<td style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
					<td style="text-align:center;"><?= $row['bal_total'] ?></td>
					<td style="text-align:center;"><?= number_format($row['bal_total'] * $row['cost'],2) ?></td>
				</tr>
			<? $total+=($row['bal_total'] * $row['cost']);
				
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