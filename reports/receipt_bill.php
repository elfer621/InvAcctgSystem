<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = mysql_query("select * from tbl_order_receipt where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_order_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'";
$sql_item = mysql_query($x);
?>
<div style="width:300px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:13px;">
<div style="text-align:center;width:100%;">
<?php echo $db->receipt_header; ?>
</div>
<div style="text-align:center;width:100%;"><h3>ORDER BILL</h3></div>
<div style="float:left;width:120px;">
Table:<span style="float:right;"><?php echo $db->customeFormat($_REQUEST['receipt_num']) ?></span>
</div>
<div style="float:right;width:120px;">
Counter:<span style="float:right;"><?php echo $db->customeFormat($info['counter_num']) ?></span>
</div>
<div style="clear:both;height:5px;"></div>
<hr/>
<div style="width:100%;"> <!--border-bottom:1px solid black;border-top:1px solid black;padding:3px;-->
	<span>Desc / Qty / Price</span><span style="float:right;width:80px;text-align:center;">Amt</span>
</div>
<hr/>
<?php
while($row_items = mysql_fetch_assoc($sql_item)){
	echo $row_items['item_desc']."<br/>";
	echo "<span style='font-size:15px;'>&nbsp;&nbsp;&nbsp;&nbsp;{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;
	@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format($row_items['selling']*$row_items['qty'],2)."</span></span> <br/>";
	$total_amount+=($row_items['selling']*$row_items['qty']);
}
echo "<hr/>";
echo "Total:     <span style='float:right;'>".number_format($total_amount,2)."</span><br/>";
echo "<br/>";
echo "<div style='float:left;width:180px;'>VAT Sales:     <span style='float:right;'>".number_format($total_amount,2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>NON-VAT Sales:     <span style='float:right;'>".number_format(0,2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>VAT Amt:     <span style='float:right;'>".number_format(($total_amount / 9.333),2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>Cashier:     <span style='float:right;'>".$info['cashier']."</span></div><br/>";
?>
<div style="clear:both;height:30px;"></div>
</div>
<?php
$db->closeDb();
?>
