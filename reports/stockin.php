<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<style type="text/css">
		table.tbl {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
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
			@page {
				size: A4;
				margin: 0;
			}
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
$sql = "select a.*,b.supplier_name from tbl_{$_REQUEST['page']}_header as a left join tbl_supplier as b on a.supplier_id=b.id
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_{$_REQUEST['page']}_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:900px;">
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">Receiving Reports <?=($_SESSION['connect']?"(".strtoupper($_SESSION['connect']).")":"ACCOUNTING")?></span></h2>
<?php echo "RR No.: ".$info['id'] ?><span style="float:right;"><?php echo $info['date'] ?></span><br/>
<div class="lbl">Supplier Name:</div>
<div style="float:left;"><?php echo $info['supplier_name'] ?></div>
<div style="clear:both;height:5px;"></div>
<div class="lbl">Remarks:</div>
<div style="float:left;"><?php echo $info['remarks'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="min-height:400px;">
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
		<tbody>
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
		?>
		</tbody>
		<tfoot>
			<tr style="border-top:1px solid #000;">
				<td colspan='5' style="text-align:right;">Total Amount:</td>
				<td style="text-align:right;"><?=number_format($info['total'],2)?></td>
			</tr>
			<?php /*
			<tr>
				<td colspan='5' style="text-align:right;">Less Volume Discount:</td>
				<td style="text-align:right;"><?=number_format(0,2)?></td>
			</tr>
			<tr>
				<td colspan='5' style="text-align:right;">Sub Total:</td>
				<td style="text-align:right;"><?=number_format($info['total'],2)?></td>
			</tr>
			<tr>
				<td colspan='5' style="text-align:right;">Additional Discount:</td>
				<td style="text-align:right;"><?=number_format(0,2)?></td>
			</tr>
			<tr>
				<td colspan='5' style="text-align:right;">Grand Total:</td>
				<td style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;"><?=number_format($info['total'],2)?></td>
			</tr>
			*/?>
		</tfoot>
	</table>
</div>
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