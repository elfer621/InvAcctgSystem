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
		tr.selected {background-color: red; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
	</style>
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
require_once"../class/pagination.class.php";
$db=new dbConnect();
$p=new pagination();
$db->openDb();
$readingNum = $db->getReadingnum($_SESSION['counter_num']);
$sql = "select * from tbl_sales_receipt_{$_SESSION['counter_num']} where counter_num='".$_SESSION['counter_num']."' and reading='".$readingNum."' order by id desc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
?>
<body>
	<div style="height:405px;overflow:auto;">
		<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>ReceiptNum</th>
					<th>Total</th>
					<th>Tender</th>
					<th>Change</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="#" class="activation"><?=$row['receipt_id']?></a></td>
					<td><?=number_format($row['amount'],2)?></td>
					<td><?=number_format($row['tender'],2)?></td>
					<td><?=number_format($row['change'],2)?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;height:20px;"></div>
</body>
<script>
	$(document).ready(function(){
		jQuery.tableNavigation();
	});
	function viewReceipt(num) {
		if (window.showModalDialog) {
			window.showModalDialog('../reports/receipt.php?receipt_num='+num,"Receipt","dialogWidth:350px;dialogHeight:350px");
		} else {
			window.open('../reports/receipt.php?receipt_num='+num,"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
	$("#mytbl").bind('keydown',function(e){
		var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
		if(chCode==13){ //pressing delete button
			viewReceipt($("tr.selected").find('td:eq(0)').text());
		}else if(chCode==27){
			window.close();
		}
		
	});
</script>
</html>