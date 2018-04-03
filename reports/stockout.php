<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<style type="text/css">
		table.tbl {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl thead th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			/*background-color:rgb(237,238,240);*/
			text-align:center;
			font-size:13px;
			padding:1px;
		}
		table.tbl td {
			font-size:13px;
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			padding:1px;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
		}
		@media print {
			@page{margin:1cm;}
			thead { display: table-header-group; }
			tfoot { display: table-footer-group; }
		}
</style>
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = "select a.*,b.supplier_name from tbl_stockout_header as a left join tbl_supplier as b on a.supplier_id=b.id
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_stockout_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1200px;">
<h2><?= $db->stockin_header;?> [Stock Out]</h2>
<?php echo "No.: ".$info['id'] ?><span style="float:right;"><?php echo $info['date'] ?></span><br/>
<div class="lbl">Supplier Name:</div>
<div style="float:left;"><?php echo $info['supplier_name'] ?></div>
<div style="clear:both;height:5px;"></div>
<div class="lbl">Remarks:</div>
<div style="float:left;"><?php echo $info['remarks'] ?></div>
<div style="clear:both;height:5px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
	<thead>
		<tr>
			<th style="min-width:80px;">Barcode</th>
			<th>Prod Desc</th>
			<th style="min-width:80px;">Qty / Unit</th>
			<th style="min-width:80px;">Cost</th>
			<th style="min-width:40px;">Disc</th>
			<th style="min-width:100px;">Total</th>
		</tr>
	</thead>
	<?php
	while($row_items = mysql_fetch_assoc($sql_item)){
		echo "<tr>";
		echo "<td>".$row_items['barcode']."</td>";
		echo "<td>".$row_items['item_desc']."</td>";
		echo "<td style='text-align:right;'>{$row_items['qty']} {$row_items['unit']}</td>";
		echo "<td style='text-align:right;'>".number_format($row_items['cost'],2)."</td>";
		echo "<td style='text-align:right;'>".(100*$row_items['discount']."%")."</td>";
		echo "<td style='text-align:right;'>".number_format($row_items['total'],2)."</td>";
		echo "</tr>";
	}
	echo "<tr>";
	echo "<th colspan='5'>Total:</th>";
	echo "<th>".number_format($info['total'],2)."</th>";
	echo "</tr>";
	?>
</table>
<div style="clear:both;height:50px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="50%">
	<tr>
		<th>Received by:</th>
		<th>Check by:</th>
	</tr>
	<tr>
		<td style="border:1px solid gray;"></td>
		<td style="border:1px solid gray;"></td>
	</tr>
</table>
</div>
<?php
$db->closeDb();
//echo chr(27).chr(112).chr(0).chr(100).chr(250);
/*$handle = fopen("PRN", "w");
fwrite($handle, 'text to printer');
fwrite($handle, chr(27).chr(112).chr(0).chr(100).chr(250));
fclose($handle);*/
//exec("F:\calculator.au3");
//exec("test.au3");
//exec("F:/xampp/htdocs/pos/reports/msg.vbs");
?>
<script>
onload=function(){
	window.print();
	//self.close();
}
</script>
</body>
</html>