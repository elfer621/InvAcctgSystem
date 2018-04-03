<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<style type="text/css">
		table.tbl,table.tbl2, p {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			font-size:15px;
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
			font-size:15px;
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			font-size:15px;
		}
		table.tbl2 td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
			font-size:15px;
		}
		p {
			margin:0;
			padding:0;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
			font-size:15px;
		}
		.logo {
			background-image: url('../images/RBERlogo.jpg');
			background-size: 200px 110px;
			background-repeat: no-repeat;
			
		}
		@page {
			size: A4;
			margin: 50px 25px 0 25px;
			font-size:15px;
		}
		@media print {
		  * {-webkit-print-color-adjust:exact;}
		  body {
			margin: 50px 25px 0 25px;
		  }
		  .footer {
			  position:absolute;bottom:150px;
		  }
		}
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
$header = $db->getWHERE("a.*,b.*","tbl_quotation_header a left join tbl_customers b on a.cust_id=b.cust_id","where id='".$_REQUEST['refid']."'");
$items = $db->resultArray("*","tbl_quotation_items","where refid='".$_REQUEST['refid']."'");
?>

<body class="page" style="margin:0 auto;width:900px;font-size:12px;height:100%;">
		<div class="logo" style="text-align:center;width:100%;">
			<h1>RBER Industrial & Trading Corporation</h1>
			<span style="font-size:13px;font-weight:bold;">M. Ceniza St., Casuntingan, Mandaue City, Cebu
				<br/>Telefax No. (63-32)343-7275, Tel Nos. (63-32)520-2005,513-3531,513-3532
				<br/>Email: rber_indl@yahoo.com / rberindustrial888@gmail.com
			</span>
			<div style="clear:both;height:50px;"></div>
			<hr/>
			<div style="clear:both;height:80px;"></div>
			<h1>Q U O T A T I O N</h1>
		</div>
		<div style="clear:both;height:50px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td style="width:80px;">Date</td><td><?=date('F j, Y',strtotime($header['date']))?></td>
				<td style="width:120px;">Fax No.</td><td><?=$header['fax_number']?></td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr>
				<td style="width:80px;">To</td><td><b><?=$header['customer_name']?></b></td>
				<td style="width:120px;">Tel. No.</td><td><?=$header['contact_number']?></td>
			</tr>
			<tr>
				<td style="width:80px;">&nbsp;</td><td><?=$header['customer_address']?></td>
				<td style="width:120px;">eMail Add.</td><td><?=$header['email_add']?></td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr>
				<td style="width:80px;">Attn</td><td><b><?=$header['attn']?></b></td>
				<td style="width:120px;">&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4">We are pleased to offer, for your acceptance, our quotation for the supply of the following unit(s) described below:</td></tr>
		</table>
		<div style="clear:both;height:10px;"></div>
		<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" border="1">
			<tr>
				<th colspan="2">Validity of Offer</th>
				<th colspan="2">Payment Terms</th>
				<th colspan="2">Deliver</th>
				<th colspan="2">RFQ No.</th>
			</tr>
			<tr>
				<td colspan="2"><?=$header['validity_of_offer']?></td>
				<td colspan="2"><?=$header['payment_terms']?></td>
				<td colspan="2"><?=$header['delivery']?></td>
				<td colspan="2"><?=$header['rfqnum']?></td>
			</tr>
			<tr style="border:none;"><td colspan="8" style="border:none;"></td></tr>
			<tr style="border:none;"><td colspan="8" style="border:none;"></td></tr>
		</table>
		<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" border="1">
			<tr>
				<th style="width:50px;">No.</th>
				<th colspan="3">Item / Specification</th>
				<th>Qty</th>
				<th>Unit</th>
				<th>Price</th>
				<th>Amount</th>
			</tr>
			<?php $count=1;$total=0;foreach($items as $key => $val){ ?>
				<tr>
					<td><?=$count?></td>
					<td colspan="3" style="text-align:left;padding-left:10px;"><?=$val['item_spec']?></td>
					<td><?=$val['qty']?></td>
					<td><?=$val['unit']?></td>
					<td><?=number_format($val['unitprice'],2)?></td>
					<td><?=number_format($val['amount'],2)?></td>
				</tr>
			<?php $count++;$total+=$val['amount'];} ?>
			<tr>
				<td colspan="7">TOTAL Php</td>
				<td><?=number_format($total,2)?></td>
			</tr>
		</table>
		<div style="clear:both;height:10px;"></div>
		<div style="width:100%;"><?=$header['agreement']?></div>
		<div style="clear:both;height:10px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4">Thank you for giving us the opportunity to quote on your requirement.  And we look forward to receive your valued order soon.</td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
		</table>
		<div style="clear:both;height:5px;"></div>
		<table class="tbl footer" cellspacing="0" cellpadding="0" width="100%">
			<tr><td colspan="4">Very truly yours,</td></tr>
			<tr><td colspan="4">RBER Industrial & Trading Corporation</td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4"><b><?=$header['footer1']?></b></td></tr>
			<tr><td colspan="4"><?=$header['footer2']?></td></tr>
			<tr><td colspan="4"><?=$header['footer3']?></td></tr>
		</table>
		<div style="clear:both;height:5px;">&nbsp;</div>
		
</body>

<script>
$(document).ready(function() {
	
});


</script>
</html>