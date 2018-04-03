<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" >
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<style type="text/css">
	table.tbl {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		
	}
	table.tbl th {
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
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = mysql_query("select a.*,b.cust_id from tbl_sales_receipt_{$_SESSION['counter_num']} a
	left join tbl_customers_trans b on a.receipt_id=b.receipt and a.reading=b.reading 
	where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'";
$sql_item = mysql_query($x);
$custname = $db->getWHERE("*","tbl_customers","where cust_id='{$info['cust_id']}'");
$moreinfo = $db->getWHERE("*","tbl_sales_moreinfo","where sales_refid='{$_REQUEST['receipt_num']}'");
?>
<body style="font-size:13px;">
<div class="landscape" style="font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;width:800px;padding-left:5px;">
	<?php //echo $info['receipt_id'] ?>
	<div style="clear:both;height:120px;"></div>
	<div style="float:right;margin-right:100px;"><?php echo date("m",strtotime($info['date']))."<span>-".date("d",strtotime($info['date']))."-</span>".date("y",strtotime($info['date'])) ?></div>
	<div style="clear:both;"></div>
	<div style="float:left;margin-left:150px;"><?php echo $custname['customer_name'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;margin-left:150px;"><?php echo $custname['customer_address'] ?></div>
	<div style="clear:both;height:70px;"></div>
	<div style="padding-left:10px;">
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<?php
		while($row_items = mysql_fetch_assoc($sql_item)){
			?>
			<tr>
				<td style="width:100px;text-align:center;"><?=$row_items['qty']." ".$row_items['unit']?></td>
				<td style="width:400px;text-align:left;"><?=$row_items['item_desc']?></td>
				<td style="width:100px;text-align:center;"><?=number_format($row_items['selling'],2)?></td>
				<td style="width:150px;text-align:right;"><?=number_format($row_items['total'],2)?></td>
			</tr>
			<?
		}
		?>
		</table>
	</div>
	<div style="clear:both;height:10px;"></div>
	<div style="float:right;width:150px;text-align:center;border:1px solid black;"><?=number_format($info['amount'],2)?></div>
	<div style="clear:both;height:10px;"></div>
	<div style="float:left;"><?php echo $info['payment'] ?></div>
	<div style="clear:both;height:5px;"></div>
	<table class="tbl2" cellspacing="0" cellpadding="0" width="50%" style="margin-left:20px;">
		<tr>
			<td>WS</td>
			<td>OB</td>
			<td>Location</td>
			<td>Cashier</td>
		</tr>
		<tr>
			<td><?= $moreinfo['ws']?></td>
			<td><?= $moreinfo['ob']?></td>
			<td><?= $moreinfo['location']?></td>
			<td><?= $info['cashier']?></td>
		</tr>
	</table>
	
</div>
<?php
$db->closeDb();
?>

</body>
</html>