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
$sql = "select * from tbl_stockout_header order by id desc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
unset($_SESSION['stockout_header']);
unset($_SESSION['stockout']);
?>
<body>
	<div style="height:405px;overflow:auto;">
		<table class="navigateable">
			<thead>
				<tr>
					<th>RefID</th>
					<th>Date</th>
					<th>Supplier</th>
					<th>Remarks</th>
					<th>Total</th>
					<th>Menu</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ 
					$supinfo = $db->getWHERE("*","tbl_supplier","where id='".$row['supplier_id']."'");
				?>
				<tr>
					<td><a href="javascript:transferStockin('<?php echo $row['id'] ?>')" class="activation"><?php echo $row['id'] ?></a></td>
					<td><?= $row['date']?></td>
					<td><?= $supinfo['supplier_name'] ?></td>
					<td><?= $row['remarks']?></td>
					<td><?= number_format($row['total'],2) ?></td>
					<td>
						<img src="../images/print.png" style="width:20px;height:20px;float:left;" onclick="printPreview('<?php echo $row['id'] ?>')"/>
						<img src="../images/del.png" style="width:20px;height:20px;float:left;" onclick="delStockin('<?php echo $row['id'] ?>')"/>
					</td>
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
	function transferStockin(val){
		//window.opener.setValue(val);
		$.ajax({
			url: 'pos_ajax_stockin.php?execute=viewStockout&refid='+val,
			type:"POST",
			success:function(data){
				window.close();
					window.opener.location.reload();
			}
		});
	}
	function delStockin(val){
		var r = confirm("Are you sure you want to delete this?");
		if (r == true){
			$.ajax({
				url: 'pos_ajax_stockin.php?execute=delStockout&refid='+val,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	}
	function printPreview(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockout.php?refid='+refid,"Stock Out","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockout.php?refid='+refid,"Stock Out",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
</script>
</html>
