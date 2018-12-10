<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
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
			float:left;margin-right:10px;width:80px;
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
$date = "b.date between '{$_REQUEST['begdate']}' and '{$_REQUEST['enddate']}'";
$sup = $_REQUEST['supplier_id']?"and b.supplier_id='{$_REQUEST['supplier_id']}'":"";
$prod = $_REQUEST['prodname']?"and a.item_desc like '%{$_REQUEST['prodname']}%'":"";
$sql ="select a.*,b.*,c.supplier_name from tbl_stockin_items a 
left join tbl_stockin_header b on a.stockin_refid=b.id 
left join tbl_supplier c on b.supplier_id=c.id 
where $date $sup $prod order by b.date asc,a.item_desc asc";
$sql_item = mysql_query($sql);
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:900px;">
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">Receiving Reports <?=($_SESSION['connect']?"(".strtoupper($_SESSION['connect']).")":"ACCOUNTING")?></span></h2>
<form name="frm" method="post">
<div style="width:45%;float:left;">
	<div class="lbl">Supplier Name:</div>
	<div style="float:left;">
		<?php 
		$branch_sup=$db->resultArray("id,supplier_name name","tbl_supplier","order by supplier_name asc");
		?>
		<select name="supplier_id" id="supplier_id" style="float:left;width:200px;">
			<option value="">Select</option>
			<?php foreach($branch_sup as $key=>$val){ ?>
				<option <?=$_REQUEST['supplier_id']==$val['id']?"selected":""?> value="<?=$val['id']?>"><?=$val['name']?></option>
			<?php } ?>
		</select>
	</div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Product Name:</div>
	<input type="text" name="prodname" style="float:left;width:150px;" value="<?=$_REQUEST['prodname']?$_REQUEST['prodname']:""?>"/>
</div>
<div style="width:45%;float:left;">
<div class="lbl">BegDate:</div>
<input type="text" name="begdate" id="begdate" style="float:left;width:80px;" value="<?=$_REQUEST['begdate']?$_REQUEST['begdate']:date('Y-m-01')?>"/>
<div class="lbl">EndDate:</div>
<input type="text" name="enddate" id="enddate" style="float:right;width:80px;" value="<?=$_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d')?>"/>
<div style="clear:both;height:5px;"></div>
<input type="submit" value="Execute" style="float:right;width:100px;height:40px;"/>
</div>
</form>
<div style="clear:both;height:5px;"></div>
<div style="min-height:400px;">
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th style="min-width:80px;">Date</th>
				<th style="min-width:80px;">SI</th>
				<th style="min-width:80px;">Supplier</th>
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
		$total=0;
		while($row_items = mysql_fetch_assoc($sql_item)){
			echo "<tr>";
			echo "<td>".$row_items['date']."</td>";
			echo "<td>".$row_items['sinum']."</td>";
			echo "<td style='font-size:10px;'>".$row_items['supplier_name']."</td>";
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
		</tbody>
		<tfoot>
			<tr style="border-top:1px solid #000;">
				<td colspan='8' style="text-align:right;">Total Amount:</td>
				<td style="text-align:right;"><?=number_format($total,2)?></td>
			</tr>
			
		</tfoot>
	</table>
</div>
<div style="clear:both;height:50px;"></div>
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
$(document).ready(function() {
	$('#begdate').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
	$('#enddate').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
});
</script>
</body>
</html>