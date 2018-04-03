<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$reading = $_SESSION['readingnum']?$_SESSION['readingnum']:$_REQUEST['readingnum'];
$sql = mysql_query("select * from tbl_sales_receipt_{$_SESSION['counter_num']} where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$reading."'";
$sql_item = mysql_query($x);
?>
<div style="width:700px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;font-size:13px;">
<?php echo $db->customeFormat($info['receipt_id']) ?><span style="float:right;"><?php echo $info['date'] ?></span><br/>
Counter:<span style="float:right;"><?php echo $info['counter_num'] ?></span>
<hr/>
<div style="font-size:14px;">
	<span>Desc / Qty / Price</span><span style="float:right;width:80px;text-align:center;">Amt</span>
</div>
<hr/>
<?php
$xtotal =0;
while($row_items = mysql_fetch_assoc($sql_item)){
	echo $row_items['item_desc']."<br/>";
	echo "<span>&nbsp;&nbsp;&nbsp;&nbsp;{$row_items['qty']}{$row_items['unit']}&nbsp;&nbsp;&nbsp;&nbsp;
	@ &nbsp;&nbsp;".number_format($row_items['selling'],2)."<span style='float:right;'>".number_format($row_items['total'],2)."</span></span> <br/>";
$xtotal+=($row_items['selling']*$row_items['qty']);}
echo "<hr/>";
echo "Total:     <span style='float:right;'>".number_format($xtotal,2)."</span><br/>";
echo "Tender:     <span style='float:right;'>".number_format($info['tender'],2)."</span><br/>";
echo "Change:     <span style='float:right;'>".number_format($info['change'],2)."</span><br/>";
echo "<div style='float:left;'>Cashier:     <span style='margin-left:10px;'>".$info['cashier']."</span></div>";
?>
<div style="clear:both;height:30px;"></div>
<div style="text-align:center;width:700px;">This is not an Official Receipt.</div>
<div style="text-align:center;width:700px;">Please ask for an Official Receipt.</div>
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