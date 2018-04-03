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
			@page{margin:1cm;}
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
$db->openDb('main');
$sql = "select a.*,b.*,a.id poid from tbl_po_header as a left join tbl_supplier as b on a.supplier_id=b.id
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_po_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
//print_r($info);
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1200px;">
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">PURCHASE ORDER</span></h2>
<div style="float:left;width:45%;">
	<div class="lbl">PO NO:</div>
	<div style="float:left;"><?php echo $info['poid'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Supplier Name:</div>
	<div style="float:left;width:75%;"><?php echo $info['supplier_name'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Contact Person:</div><div style="float:left;width:75%;"><?=$info['contact_person']?></div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="float:right;width:45%;">
	<!--div class="lbl">Reference:</div>
	<div style="float:left;">UC-METC</div>
	<div style="clear:both;height:5px;"></div-->
	<div class="lbl">Date:</div>
	<div style="float:left;"><?php echo $info['date'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Contact Number:</div><div style="float:left;width:150px;border-bottom:1px solid #000;">&nbsp;</div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="clear:both;height:5px;"></div>
<div class="lbl">Remarks:</div>
<div style="float:left;width:85%;border-bottom:1px solid #000;"><?php echo $info['remarks'] ?></div>
<div style="clear:both;height:5px;"></div>
<?php 
if($_SESSION['default_db']!="rber_db"){
	if($info['supplier_name']=="MARTA"){ ?>
	<p>The following is our REGULAR PURCHASE ORDER to be delivered not later that <span style="width:150px;border-bottom:1px solid #000;"><?=$info['date']?></span> at <span style="width:150px;border-bottom:1px solid #000;">UC-MAMBALING</span></p>
	<?php }else{ ?>
	<p>The following is our CONSIGNMENT ORDER to be delivered not later that <span style="width:150px;border-bottom:1px solid #000;"><?=$info['delivery_date']?></span> at <span style="width:150px;border-bottom:1px solid #000;">UC-MAMBALING</span></p>
	<?php }
} ?>
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
	$total=0;
	while($row_items = mysql_fetch_assoc($sql_item)){
		echo "<tr>";
		echo "<td>".$row_items['barcode']."</td>";
		echo "<td>".$row_items['item_desc']."</td>";
		echo "<td style='text-align:right;'>{$row_items['qty']} {$row_items['unit']}</td>";
		echo "<td style='text-align:right;'>".number_format($row_items['cost'],2)."</td>";
		echo "<td style='text-align:right;'>".(100*$row_items['discount']."%")."</td>";
		echo "<td style='text-align:right;'>".number_format($row_items['total'],2)."</td>";
		echo "</tr>";
		$total+=$row_items['total'];
	}
	?>
	<tfoot>
		<tr style="border-top:1px solid #000;">
			<td colspan='5' style="text-align:right;">Total Amount:</td>
			<td style="text-align:right;"><?=number_format($total,2)?></td>
		</tr>
		<tr>
			<td colspan='5' style="text-align:right;">Less Volume Discount:</td>
			<td style="text-align:right;"><?=number_format($info['volume_discount'],2)?></td>
		</tr>
		<tr>
			<td colspan='5' style="text-align:right;">Sub Total:</td>
			<td style="text-align:right;"><?=number_format($total-$info['volume_discount'],2)?></td>
		</tr>
		<tr>
			<td colspan='5' style="text-align:right;">Additional Discount:</td>
			<td style="text-align:right;"><?=number_format($info['additional_discount'],2)?></td>
		</tr>
		<tr>
			<td colspan='5' style="text-align:right;">Grand Total:</td>
			<td style="text-align:right;border-top:1px solid #000;border-bottom:1px solid #000;"><?=number_format($info['total'],2)?></td>
		</tr>
	</tfoot>
</table>
<div style="clear:both;height:50px;"></div>
<div style="float:left;width:45%">
	<div class="lbl">Order By:</div>
	<div style="clear:both;height:25px;"></div>
	<div style="border-bottom:1px solid #000;width:250px;">&nbsp;</div>
</div>
<div style="float:right;width:45%">
	<div class="lbl">Approved By:</div>
	<div style="clear:both;height:25px;"></div>
	<div style="border-bottom:1px solid #000;width:250px;">&nbsp;</div>
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