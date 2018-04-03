<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
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
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl td {
		border-width: 1px;
		border-style: none;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
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

<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$tbl = $_REQUEST['type']?$_REQUEST['type']:"tbl";
switch($_REQUEST['rep']){
	case'perreading':
		$sql = "select sum(items.qty) as total_qty,sum(items.qty * items.selling) as sales,sum(items.qty * items.cost) as cost, 
			items.barcode,items.item_desc,items.unit,items.skuid,items.selling,b.type
			from {$tbl}_sales_items items
			left join  tbl_sales_receipt_1 b on items.receipt=b.receipt_id 
			where items.reading='{$_REQUEST['reading']}' and items.counter='{$_REQUEST['counter']}' and b.type!='VOID' group by item_desc,divmul order by item_desc";
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
$info = $db->getWHERE("min(receipt_id) as receipt_start,max(receipt_id) as receipt_end","tbl_sales_receipt_{$_REQUEST['counter']}","where reading='{$_REQUEST['reading']}' and counter_num='{$_REQUEST['counter']}'");
?>
<div style="width:300px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:12px;">
	<h2>Product Sold</h2>
	<?php if($_REQUEST['rep']=="perreading"){ ?>
	<div style="float:left;width:120px;">
	Counter:<span style="float:right;"><?php echo $db->customeFormat($_REQUEST['counter']) ?></span>
	</div>
	<div style="float:right;width:120px;">
	Reading:<span style="float:right;"><?php echo $db->customeFormat($_REQUEST['reading']) ?></span>
	</div>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;width:120px;">
	OR Start:<span style="float:right;"><?php echo $db->customeFormat($info['receipt_start'],7) ?></span>
	</div>
	<div style="float:right;width:120px;">
	OR End:<span style="float:right;"><?php echo $db->customeFormat($info['receipt_end'],7) ?></span>
	</div>
	<div style="clear:both;height:5px;"></div>
	<?php } ?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Desc</th>
				<th>Qty</th>
				<th>Selling</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><?= $row['item_desc']?></td>
				<td style="text-align:right;"><?= $row['total_qty']."<span style='font-size:10px;'>".$row['unit']."</span>"?></td>
				<td style="text-align:right;"><?= number_format($row['selling'],2) ?></td>
				<td style="text-align:right;"><?= number_format($row['sales'],2) ?></td>
			</tr>
			<? $total['sales']+=$row['sales'];$total['cost']+=$row['cost'];} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">Sub Total</th>
				<th colspan="2"><?=number_format($total['sales'],2)?></th>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
