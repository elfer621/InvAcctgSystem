<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<script src="../jquery.table_navigation_uom.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selecteduom {background-color: red; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selecteduom a {color: white;}
	</style>
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
require_once"../class/pagination.class.php";
$db=new dbConnect();
$p=new pagination();
$db->openDb();
$barcode = $_REQUEST['sku_id'];
$info = $db->getWHERE("*","tbl_barcodes","where barcode='".$barcode."'");
$sql = "select * from tbl_barcodes where sku_id='".$info['sku_id']."'";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
?>
<body>
	<div style="height:405px;overflow:auto;">
		<table class="navigateableuom"  cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcode</th>
					<th>Price</th>
					<th>Unit</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:transferOrder('<?php echo $row['barcode'] ?>')" class="activationuom"><?php echo $row['barcode'] ?></a></td>
					<td><?= number_format($row['price'],2)?></td>
					<td><?= $row['unit'] ?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;height:20px;"></div>
</body>
<script>
	$(document).ready(function(){
		jQuery.tableNavigationUom();
	});
</script>
</html>
