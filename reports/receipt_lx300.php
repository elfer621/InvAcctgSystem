<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = mysql_query("select * from tbl_sales_receipt_{$_SESSION['counter_num']} a
left join tbl_customers_trans b on a.receipt_id=b.receipt and a.reading=b.reading 
where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'";
$sql_item = mysql_query($x);
$custname = $db->getWHERE("*","tbl_customers","where cust_id='{$info['cust_id']}'");
$width="450px";
?>
<div style="width:<?=$width?>;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:13px;">
<div style="text-align:center;width:100%;">

</div>

<?php echo $db->customeFormat($info['receipt_id'],9); ?><span style="float:right;"><?php echo $info['date'] ?></span><br/>
<div style="float:left;width:200px;">
Counter:<span style="float:right;"><?php echo $db->customeFormat($info['counter_num']) ?></span>
</div>
<div style="float:right;width:200px;">
Reading:<span style="float:right;"><?php echo $db->customeFormat($_SESSION['readingnum']) ?></span>
</div>
<div style="clear:both;height:5px;"></div>
<div style="border-bottom:1px solid black;border-top:1px solid black;padding:3px;">
	<span>Desc / Qty / Price</span><span style="float:right;width:80px;text-align:center;">Amt</span>
</div>
<?php
$xtotal =0;
while($row_items = mysql_fetch_assoc($sql_item)){
	echo $row_items['item_desc']."<br/>";
	echo "<span style='font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;
	@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format($row_items['total'],2)."</span></span> <br/>";
	$xtotal+=($row_items['selling']*$row_items['qty']);
}
?>
<div style="height:5px;border-bottom:1px solid black;padding:3px;">&nbsp;</div>
<?php
echo "Total:     <span style='float:right;'>".number_format($xtotal,2)."</span><br/>";
echo "Tender:     <span style='float:right;'>".number_format($info['tender'],2)."</span><br/>";
echo "Change:     <span style='float:right;'>".number_format($info['change'],2)."</span><br/>";
?>
<div style="clear:both;height:5px;"></div>
<?php
echo "<div style='float:left;width:".$width.";'>VAT Sales:     <span style='float:right;'>".number_format($info['amount'],2)."</span></div><br/>";
echo "<div style='float:left;width:".$width.";'>NON-VAT Sales:     <span style='float:right;'>".number_format(0,2)."</span></div><br/>";
echo "<div style='float:left;width:".$width.";'>VAT Amt:     <span style='float:right;'>".number_format(($info['amount'] / 9.333),2)."</span></div><br/>";
echo "<div style='float:left;width:".$width.";'>Cashier:     <span style='float:right;'>".$info['cashier']."</span></div>";
echo "<div style='float:left;width:".$width.";'>Customer:     <span style='float:right;'>".$custname['customer_name']."</span></div>";
?>
<div style="clear:both;height:30px;"></div>
<div style="text-align:center;width:100%;"><?=$db->receipt_footer;?></div>

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
//exec($_SERVER['DOCUMENT_ROOT']."/pos/reports/msg.vbs");
?>
<script>
/*onload=function(){
	var change = "<?=$info['change']?>";
	window.print();
	self.close();
	window.onunload = function(){
		//var loc = window.opener.location;
		//window.opener.location=loc+"?change="+parseFloat(change);
		window.opener.displayChange(parseFloat(change));
	}
}*/
</script>