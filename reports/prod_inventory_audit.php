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
if($_POST){
	$sku = implode("','",$_REQUEST['chckbox']);
	$sql = "delete from tbl_product_name where sku_id not in ('$sku')";
	$qry = mysql_query($sql);
	if(!$qry){
		echo "Step 1:".mysql_error();
	}else{
		$sql = "delete from tbl_barcodes where sku_id not in ('$sku')";
		$qry = mysql_query($sql);
		if(!$qry){
			echo "Step 2:".mysql_error();
		}else{
			foreach($_REQUEST['chckbox'] as $key => $val){
				$new_skuid = $db->genSKU(9);
				$sql = "update tbl_product_name set sku_id='$new_skuid' where sku_id='$val'";
				$qry = mysql_query($sql);
				if($qry){
					$sql="update tbl_barcodes set sku_id='$new_skuid' where sku_id='$val'";
					$qry = mysql_query($sql);
					if(!$qry){
						echo "Step 4:".mysql_error();
					}
				}else{
					echo "Step 3:".mysql_error();
				}
			}
		}
	}
}
$xdate = date('Y-m-d');
$sql = "select a.sku_id,product_name,barcode,cost,unit from tbl_product_name as a
				left join (select * from tbl_barcodes where divmul=1) as b on a.sku_id=b.sku_id 
			 order by product_name asc";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2>Product Inventory</h2>
	<form name="frm_cust" method="post">
		<input type="submit" value="Clear" name="search_date"/>
		<div style="clear:both;height:20px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" /></th>
					<th>SKU ID</th>
					<th>Desc</th>
					<th>Cost</th>
					<th>Stock OnHand</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ 
					$inv = $db->invBal($row['sku_id'],$xdate);
				?>
				<tr>
					<td><input type="checkbox" name="chckbox[]" value="<?php echo $row['sku_id'] ?>"/></td>
					<td><a href="javascript:viewTrans('<?php echo $row['sku_id'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['sku_id'] ?></a></td>
					<td><?= $row['product_name']?></td>
					<td style="text-align:right;"><input type="text" value="<?= number_format($row['cost'],2)?>" style="width:80px;text-align:right;" /></td>
					<td style="text-align:center;"><input type="text" name="inv[]" value="<?=$inv?>" style="width:50px;text-align:right;"/><?= $row['unit'] ?></td>
					<td style="text-align:right;"><?= number_format($inv * $row['cost'],2) ?></td>
				</tr>
				<? $total+=$inv * $row['cost'];} ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">Sub Total</th>
					<th><?= number_format($total,2) ?></th>
				</tr>
			</tfoot>
		</table>
	</form>
	<div id="dialog"></div>
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