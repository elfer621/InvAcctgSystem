<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = mysql_query("select * from tbl_sales_receipt_{$_SESSION['counter_num']} where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'";
$sql_item = mysql_query($x);
$custname = $db->getWHERE("a.cust_id,customer_name,customer_address","tbl_customers_trans a left join tbl_customers b on a.cust_id=b.cust_id","where receipt='{$_REQUEST['receipt_num']}' and reading='{$_SESSION['readingnum']}' and counter='{$_SESSION['counter_num']}'");
?>
<div style="width:300px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:13px;">
<div style="text-align:center;width:100%;">
<?php echo $db->receipt_header; ?>
</div>

<?php echo "OR#: ".$db->customeFormat($info['receipt_id'],9); ?><span style="float:right;"><?php echo $info['timestamp'] ?></span><br/>
<div style="float:left;width:120px;">
Counter:<span style="float:right;"><?php echo $db->customeFormat($info['counter_num']) ?></span>
</div>
<div style="float:right;width:120px;">
Reading:<span style="float:right;"><?php echo $db->customeFormat($_SESSION['readingnum']) ?></span>
</div>
<?php if($custname){echo "</br>Cust Name: ".$custname['customer_name'];} ?>
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
	@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format($row_items['total'],2)."</span></span> <br/>";
}
echo "<hr/>";
echo "Total:     <span style='float:right;'>".number_format($info['amount'],2)."</span><br/>";
echo "Tender:     <span style='float:right;'>".number_format($info['tender'],2)."</span><br/>";
echo "Change:     <span style='float:right;'>".number_format($info['change'],2)."</span><br/>";

echo "<div style='float:left;width:180px;'>VAT Sales:     <span style='float:right;'>".number_format($info['amount'],2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>NON-VAT Sales:     <span style='float:right;'>".number_format(0,2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>VAT Amt:     <span style='float:right;'>".number_format(($info['amount'] / 9.333),2)."</span></div><br/>";
echo "<div style='float:left;width:180px;'>Cashier:     <span style='float:right;'>".$info['cashier']."</span></div><br/>";
echo "<div style='float:left;width:180px;'>Payment:     <span style='float:right;'>".$info['payment']."</span></div><br/>";
if($info['payment']=="CHEQUE"){
	$paymentInfo = $db->getWHERE("*","tbl_chequepayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='{$_SESSION['readingnum']}' and counter='{$_SESSION['counter_num']}'");
	echo "<div style='float:left;'>Details:</div><br/>";
	echo $paymentInfo['cheque_details'];
}else if($info['payment']=="CREDITCARD"){
	$paymentInfo = $db->getWHERE("*","tbl_creditcardpayment_details","where receipt='{$_REQUEST['receipt_num']}' and reading='{$_SESSION['readingnum']}' and counter='{$_SESSION['counter_num']}'");
	echo "<div style='float:left;'>Details:</div><br/>";
	echo $paymentInfo['cc_num']."/".$paymentInfo['cc_custname']."/".$paymentInfo['cc_approval']."/".$paymentInfo['card_type'];
}
?>
<div style="clear:both;height:10px;"></div>
<div style="text-align:center;width:100%;"><h3>DISCOUNTING</h3></div>
<div style="float:left;width:120px;">Disc Type:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['disc_type'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Cust Name:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['cust_name'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">ID Num:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['id_num'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Details:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['details'] ?></div>
<div style="clear:both;height:5px;"></div>
<div style="float:left;width:120px;">Total Disc:</div>
<div style="float:left;"><?php echo $_SESSION['disc_info']['total_disc'] ?></div>
<div style="clear:both;height:25px;"></div>
<hr/>
<div style="text-align:center;width:100%;font-size:10px;">SIGNATURE</div>
<div style="clear:both;height:30px;"></div>
<div style="text-align:center;width:100%;"><?=$db->receipt_footer;?></div>

</div>
<?php
$db->closeDb();
unset($_SESSION['disc_info']);
?>
