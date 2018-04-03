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
			float:left;margin-right:10px;width:90px;
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
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = "select a.* from tbl_stockout_header as a 
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_stockout_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
$branches=$db->getWHERE("id,name","tbl_branch","where id='{$info['supplier_id']}'");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:850px;">
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">STOCK TRANSFER <?=($_SESSION['connect']?"(".strtoupper($_SESSION['connect']).")":"ACCOUNTING")?></span></h2>
<div style="float:left;width:45%;">
	<div class="lbl">NO:</div>
	<div style="float:left;"><?php echo $info['id'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">From:</div>
	<div style="float:left;width:65%;border-bottom:1px solid #000;"><?php echo strtoupper($_SESSION['connect']) ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">To:</div>
	<div style="float:left;width:65%;border-bottom:1px solid #000;"><?php echo strtoupper($branches['name']) ?></div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="float:right;width:45%;">
	<div class="lbl">Date:</div>
	<div style="float:left;"><?php echo $info['date'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Number of Box:</div><div style="float:left;width:150px;border-bottom:1px solid #000;">&nbsp;</div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="clear:both;height:5px;"></div>
<div class="lbl">Remarks:</div>
<div style="float:left;width:85%;border-bottom:1px solid #000;"><?php echo $info['remarks'] ?></div>
<div style="clear:both;height:5px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
	<thead>
		<tr>
			<th style="min-width:80px;">Barcode</th>
			<th>Prod Desc</th>
			<th style="min-width:80px;">Qty / Unit</th>
			<th style="min-width:80px;">Selling</th>
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
		echo "<td style='text-align:right;'>".number_format($row_items['selling'],2)."</td>";
		echo "<td style='text-align:right;'>".(100*$row_items['discount']."%")."</td>";
		echo "<td style='text-align:right;'>".number_format($row_items['qty']*$row_items['selling'],2)."</td>";
		echo "</tr>";
		$total+=($row_items['qty']*$row_items['selling']);
	}
	
	?>
	<tfoot>
		<tr style="border-top:1px solid #000;">
			<td colspan='5' style="text-align:right;">Total Amount:</td>
			<td style="text-align:right;"><b><?=number_format($total,2)?></b></td>
		</tr>
	</tfoot>
</table>
<div style="clear:both;height:50px;"></div>
<div style="float:left;width:45%">
	<div style="float:left;margin-right:5px;">
		<div class="lbl">Prepared By:</div>
		<div style="clear:both;height:25px;"></div>
		<div style="border-bottom:1px solid #000;width:150px;">&nbsp;</div>
	</div>
	<div style="float:left;margin-right:5px;">
		<div class="lbl">Checked By:</div>
		<div style="clear:both;height:25px;"></div>
		<div style="border-bottom:1px solid #000;width:150px;">&nbsp;</div>
	</div>
</div>
<div style="float:right;width:45%">
	<div style="float:left;margin-right:5px;">
		<div class="lbl">Delivered By:</div>
		<div style="clear:both;height:25px;"></div>
		<div style="border-bottom:1px solid #000;width:150px;">&nbsp;</div>
	</div>
	<div style="float:left;margin-right:5px;">
		<div class="lbl">Received By:</div>
		<div style="clear:both;height:25px;"></div>
		<div style="border-bottom:1px solid #000;width:150px;">&nbsp;</div>
	</div>
</div>
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