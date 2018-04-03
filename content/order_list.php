<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<script type="text/vbscript" src="../js/test.vbs"></script>
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
$sql = "select * from tbl_order_receipt order by id asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
//echo $_SERVER['DOCUMENT_ROOT']."/pos/reports/test.vbs";
//exec($_SERVER['DOCUMENT_ROOT']."/pos/reports/test.vbs");
exec("calc");
?>
<body onload="xmsg">
	<div style="height:405px;overflow:auto;">
		<table id="mytbl" class="navigateable" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Receipt</th>
					<th>User</th>
					<th>Amount</th>
					<th>Tender</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:transferOrder('<?php echo $row['receipt_id'] ?>')" class="activation"><?php echo $row['receipt_id'] ?></a></td>
					<td><?= $row['cashier']?></td>
					<td><?= number_format($row['amount'],2) ?></td>
					<td><?= number_format($row['tender'],2) ?></td>
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
	function transferOrder(val){
		//window.opener.setValue(val);
		$.ajax({
			url: 'pos_ajax.php?execute=saveorderToSession&order_receipt='+val,
			type:"POST",
			success:function(data){
				if(data=="success"){
					removeOrder(val);
				}else{
					alert(data);
				}
			}
		});
	}
	function removeOrder(val){
		$.ajax({
			url: 'pos_ajax.php?execute=removeOrder&order_receipt='+val,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.close();
					//window.opener.location.reload();
					var loc = window.opener.location;
					window.opener.location=loc;
				}else{
					alert(data);
				}
			}
		});
	}
	$("#mytbl").bind('keydown',function(e){
		var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
		if(chCode==27){ //press delete
			window.close();
		}
	});
	$(document).bind('keydown',function(e){
		var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
		if(chCode==27){ //press delete
			window.close();
		}
	});
</script>
</html>
