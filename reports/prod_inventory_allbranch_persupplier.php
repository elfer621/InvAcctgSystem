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
set_time_limit(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
//$db->openDb("main");
$db->openDb();
$con=new dbUpdate();
//$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:(new DateTime('+1 day'))->format('Y-m-d');
//$xdate = $_REQUEST['my_date']?$_REQUEST['my_date']:date('Y-m-d');

$sql1 = "select y.id,y.supplier_name,count(*) as num from tbl_product_name x left join tbl_supplier y on x.supplier_id=y.id group by y.id";
$q = mysql_query($sql1);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2><?=$db->stockin_header;?><br/>Product Inventory</h2>
	<input type="button" value="Export" onclick="ExportToExcel('tbldetails');" style="float:left;margin-left:10px;"/>
	<div style="clear:both;height:20px;"></div>
	<table id="tbldetails" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th rowspan="2">Barcodes</th>
				<th rowspan="2">Desc</th>
				<th rowspan="2">Cost</th>
				<th rowspan="2">Selling</th>
				<th colspan="6">Stock OnHand</th>
				<th rowspan="2">Total Worth</th>
			</tr>
			<tr>
				<th>UC-Main</th>
				<th>UC-LM</th>
				<th>UC-Mambaling</th>
				<th>UC-Banilad</th>
				<th>Warehouse</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<? 	
		while($r=mysql_fetch_assoc($q)){
			// $sql = "select a.*,b.*,c.* from tbl_inv_allbranch a 
				// left join tbl_product_name b on a.sku_id=b.sku_id
				// left join tbl_barcodes c on a.sku_id=c.sku_id
				// where b.supplier_id='{$r['id']}'";
			// $qry = mysql_query($sql);
			$res = $db->resultArray("a.*,b.*,c.*","tbl_inv_allbranch a 
				left join tbl_product_name b on a.sku_id=b.sku_id
				left join tbl_barcodes c on a.sku_id=c.sku_id","where b.supplier_id='{$r['id']}'");

				echo "<tr><td colspan='11'>{$r['supplier_name']}</td></tr>";
				foreach($res as $k => $row){ 
					$ucmain=$db->outputInvBal($row['ucmain'],$row['sku_id']);
					$uclm=$db->outputInvBal($row['uclm'],$row['sku_id']);
					$ucmambaling=$db->outputInvBal($row['ucmambaling'],$row['sku_id']);
					$ucbanilad=$db->outputInvBal($row['ucbanilad'],$row['sku_id']);
					$warehouse=$db->outputInvBal($row['warehouse'],$row['sku_id']);
					$total=$ucmain+$uclm+$ucmambaling+$ucbanilad+$warehouse;
					//if($ucmain>0||$uclm>0||$ucmambaling>0||$ucbanilad>0||$warehouse>0){	
					
					?>
						<tr>
							<td><a href="javascript:viewTrans('<?php echo $row['sku_id'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['barcode'] ?></a></td>
							<td><?= $row['product_name']?></td>
							<td style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
							<td style="text-align:right;"><?= number_format($row['price'],2) ?></td>
							<td style="text-align:center;"><?= $ucmain==0?"":$db->outputInvBal($ucmain,$row['sku_id']); ?></td>
							<td style="text-align:center;"><?= $uclm==0?"":$db->outputInvBal($uclm,$row['sku_id']); ?></td>
							<td style="text-align:center;"><?= $ucmambaling==0?"":$db->outputInvBal($ucmambaling,$row['sku_id']); ?></td>
							<td style="text-align:center;"><?= $ucbanilad==0?"":$db->outputInvBal($ucbanilad,$row['sku_id']); ?></td>
							<td style="text-align:center;"><?= $warehouse==0?"":$db->outputInvBal($warehouse,$row['sku_id']); ?></td>
							<td style="text-align:center;"><?=$total?></td>
							<td style="text-align:center;"><?=number_format($total*$row['cost'],2)?></td>
						</tr>
					<? $subtotal+=$total*$row['cost'];
					//}
				}
			
		}		?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="10">Sub Total</th>
				<th><?= number_format($subtotal,2) ?></th>
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