<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
		}
		table.tbl2 td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
		}
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$tbl = $_REQUEST['type']?$_REQUEST['type']:"tbl";
switch($_REQUEST['rep']){
	case'perreading':
		$sql = "select sum(qty) as total_qty,sum(qty * selling) as sales,sum(qty * cost) as cost, 
			barcode,item_desc,unit,skuid
			from {$tbl}_sales_items where reading='{$_REQUEST['reading']}' and counter='{$_REQUEST['counter']}' group by item_desc,divmul order by item_desc";
	break;
	default:
		$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
		$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
		$sql = "select sum(qty) as total_qty,sum(qty * selling) as sales,sum(qty * cost) as cost, 
			barcode,item_desc,unit,skuid
			from {$tbl}_sales_items where date_format(`timestamp`,'%Y-%m-%d') between '$begdate' and '$enddate' group by item_desc,divmul order by item_desc";
	break;
}
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2><?=$db->stockin_header;?><br/>Sales Reports<br/><span style="font-size:17px;"><?=($_SESSION['connect']?"(".strtoupper($_SESSION['connect']).")":"ADMIN")?></span></h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;margin-right:50px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
		<select name="type" style="float:left;margin-left:10px;margin-right:10px;">
			<option <?= $tbl=="tbl"?"selected":''?> value="tbl">New</option>
			<option <?= $tbl=="old"?"selected":''?> value="old">Old</option>
		</select>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Barcodes</th>
				<th>Desc</th>
				<th>Qty</th>
				<th>Sale</th>
				<!--<th>Cost</th>
				<th>Gain</th>
				<th>%</th>-->
			</tr>
		</thead>
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><a href="javascript:viewTrans('<?php echo $row['skuid'] ?>','<?php echo $row['item_desc'] ?>')"><?php echo $row['barcode'] ?></a></td>
				<td><?= $row['item_desc']?></td>
				<td style="text-align:right;"><?= $row['total_qty']." ".$row['unit']?></td>
				<td style="text-align:right;"><?= number_format($row['sales'],2) ?></td>
				<?php /*
				<td style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
				<td style="text-align:right;"><?= number_format($row['sales']-$row['cost'],2) ?></td>
				<td style="text-align:center;"><?=number_format((($row['sales']-$row['cost'])/$row['cost'])*100,2)?></td>
				*/ ?>
			</tr>
			<? $total['qty']+=$row['total_qty'];$total['sales']+=$row['sales'];$total['cost']+=$row['cost'];} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">Sub Total</th>
				<th><?=$total['qty']?></th>
				<th><?=number_format($total['sales'],2)?></th>
				<?php /*
				<th><?=number_format($total['cost'],2)?></th>
				<th><?=number_format($total['sales']-$total['cost'],2)?></th>
				<th><?=number_format((($total['sales']-$total['cost'])/$total['cost'])*100,2)?></th>
				*/ ?>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#beg_date').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
});
function viewTrans(sku_id,prod_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:prod_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=prodtrans&sku_id='+sku_id+'&type=<?=$tbl?>&reading=<?=$_REQUEST['reading']?>',async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}

</script>
</html>