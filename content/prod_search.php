<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selected {background-color: blue; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
	</style>
</head>
<?php
error_reporting(0);
session_start();
require_once"../class/dbConnection.php";
require_once"../class/pagination.class.php";
$db=new dbConnect();
$p=new pagination();
$db->openDb();
if($_POST){
	if($_REQUEST['search_prodname']!=" "&&$_REQUEST['search_prodname']!=""){
	$sql = "select product_name,barcode,price,unit,concat(base_inv,' ',base_unit) as stockonhand from tbl_product_name as a
			right join tbl_barcodes as b on a.sku_id=b.sku_id 
		where product_name like '%".$_REQUEST['search_prodname']."%' or b.barcode like '%".$_REQUEST['search_prodname']."%' order by product_name asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
	}
}
?>
<body style="font-size:17px;">
	<div style="height:405px;overflow:auto;">
		<table class="navigateable" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcodes</th>
					<th>Desc</th>
					<th>Price</th>
					<th>Unit</th>
					<th>Stock OnHand</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:itemSelected('<?php echo $row['barcode'] ?>')" class="activation"><?php echo $row['barcode'] ?></a></td>
					<td><?= $row['product_name']?></td>
					<td><?= number_format($row['price'],2) ?></td>
					<td><?= $row['unit'] ?></td>
					<td><?= $row['stockonhand'] ?></td>
				</tr>
				<? $count++;} ?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;height:20px;"></div>
	<form method="post" autocomplete="off">
		<fieldset>
		<legend>Search: </legend>
		<input type="text" id="search_prodname" name="search_prodname" style="float:left;width:80%" value=""/>
		<input type="submit" value="Execute" style="float:right;width:15%" />
		</fieldset>
	</form>
	<div style="clear:both;height:20px;"></div>
</body>
<script>
	$('#search_prodname').keyup(function(e){
		switch(e.which)
			{
				case 38: //arrow up
				case 40: //arrow down
					if(!e.ctrlKey) {
						jQuery.tableNavigation();
						$("#search_prodname").blur();
					}
				break;
				case 27:
					window.close();
				break;
			}
	});
	$(".navigateable").keyup(function(e){
		var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
		if(chCode==27){
			$("#search_prodname").focus();
		}
	});
	
	$(document).ready(function(){
		$("#search_prodname").focus();
	});
	function itemSelected(val){
		window.opener.setValue(val);
		window.close();
		return false;
	}
	
</script>
</html>
