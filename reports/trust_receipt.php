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
$sql = "select a.*,b.supplier_name from tbl_{$_REQUEST['page']}_header as a left join tbl_supplier as b on a.supplier_id=b.id
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_{$_REQUEST['page']}_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:900px;">
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">TRUST RECEIPT</span></h2>
<?php echo "DATE: ".$info['date'] ?><br/>
<p>The company, LIZGAN DISTRIBUTORS, INC. entrusts to you the textbooks as listed in the TEXTBOOK TRANSFER RECEIPT NO. <span style="text-decoration:underline;"><?=$info['id']?></span> valued at P <span style="text-decoration:underline;"><?=number_format($info['total'],2)?></span>.</p>
<p>The said books are received in good condition. Thus any shortages will be charged to your account. Books not sold will be properly inventoried and accounted for.</p>
<br/><br/>
<p>CONFORME RECEIVED BY:<br/><br/><div style="border-bottom:1px solid #000;width:180px;">&nbsp;</div></p>

<div style="clear:both;height:50px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="80%">
	<tr>
		<td>Prepared By:</td>
		<td>Check By:</td>
		<td>Delivered By:</td>
	</tr>
	<tr><td colspan="3"></td></tr>
	<tr>
		<td style="border:none;">_________________</td>
		<td style="border:none;">_________________</td>
		<td style="border:none;">_________________</td>
	</tr>
</table>
<div style="clear:both;height:250px;"></div>
<h2><?= $db->stockin_header;?><br/><span style="font-size:17px;">TRUST RECEIPT</span></h2>
<?php echo "DATE: ".$info['date'] ?><br/>
<p>The company, LIZGAN DISTRIBUTORS, INC. entrusts to you the textbooks as listed in the TEXTBOOK TRANSFER RECEIPT NO. <span style="text-decoration:underline;"><?=$info['id']?></span> valued at P <span style="text-decoration:underline;"><?=number_format($info['total'],2)?></span>.</p>
<p>The said books are received in good condition. Thus any shortages will be charged to your account. Books not sold will be properly inventoried and accounted for.</p>
<br/><br/>
<p>CONFORME RECEIVED BY:<br/><br/><div style="border-bottom:1px solid #000;width:180px;">&nbsp;</div></p>

<div style="clear:both;height:50px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="80%">
	<tr>
		<td>Prepared By:</td>
		<td>Check By:</td>
		<td>Delivered By:</td>
	</tr>
	<tr><td colspan="3"></td></tr>
	<tr>
		<td style="border:none;">_________________</td>
		<td style="border:none;">_________________</td>
		<td style="border:none;">_________________</td>
	</tr>
</table>
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
	//window.print();
	//self.close();
}
</script>
</body>
</html>