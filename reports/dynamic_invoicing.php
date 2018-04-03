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
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
			font-size:15px;
		}
		p {
			padding:0px;
			margin:0;
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
$header = $db->getWHERE("a.*,b.*","tbl_{$_REQUEST['tbltype']}_header a left join tbl_customers b on a.cust_id=b.cust_id","where id='".$_REQUEST['refid']."'");
$items = $db->resultArray("*","tbl_{$_REQUEST['tbltype']}_items","where refid='".$_REQUEST['refid']."'");
?>

<body class="page" style="margin:0 auto;width:900px;font-size:12px;height:100%;">
		<div style="clear:both;height:200px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td style="width:80px;"></td><td><b><?=$header['customer_name']?></b></td>
				<td style="width:120px;"></td><td style="text-align:right;font-size:12px;width:120px;"><?=date('F j, Y',strtotime($header['date']))?></td>
			</tr>
			<tr>
				<td style="width:80px;">&nbsp;</td><td></td>
				<td style="width:120px;"></td><td style="text-align:right;"><?=$header['ponum']?></td>
			</tr>
			<tr>
				<td style="width:80px;"></td><td><?=$header['tin']?></td>
				<td style="width:120px;"></td><td style="text-align:right;"><?=$header['regnum']?></td>
			</tr>
			<tr>
				<td style="width:80px;"></td><td><?=$header['customer_address']?></td>
				<td style="width:120px;"></td><td style="text-align:right;"><?=$header['payment_terms']?></td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr>
				<td style="width:80px;"></td><td><b><?=$header['nature_of_business']?></b></td>
				<td style="width:120px;">&nbsp;</td><td>&nbsp;</td>
			</tr>
		</table>
		<div style="clear:both;height:50px;"></div>
		<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" border="1">
			<?php $count=1;$total=0;$num=35;
			foreach($items as $key => $val){ 
				$tbl .= '<tr>
					<td>'.($val['qty']==0?"":$val['qty']).'</td>
					<td>'.$val['unit'].'</td>
					<td colspan="3" style="text-align:left;padding-left:10px;">'.$val['item_spec'].'></td>
					<td>'.($val['unitprice']?number_format($val['unitprice'],2):"").'</td>
					<td style="text-align:right;">'.number_format($val['amount'],2).'</td>
				</tr>';
			$count++;$total+=$val['amount'];
			}  
			if($header['agreement']&&$_REQUEST['tbltype']=="billing_statement"){
				echo "<tr>
				<td style='vertical-align: top;' rowspan='".($num-$count)."' colspan='6'>".$header['agreement']."</td>
				<td style='text-align:right;width:200px;'>".number_format($total,2)."</td>
				</tr>";
				for($x=1;$x<=($num-$count);$x++){
					echo "<tr><td>&nbsp;</td></tr>";
				}
			}else{
				echo $tbl;
				for($x=1;$x<=($num-$count);$x++){
					echo "<tr><td colspan='5'>&nbsp;</td></tr>";
				}
			}
			?>
			<tr>
				<td colspan="2"><?=$header['taxtype']=="vatable"?number_format($total,2):""?></td>
				<td colspan="4" style="text-align:right;"><?=$_REQUEST['tbltype']=="sales_invoice"?"":"Total Sales: (VAT Inc)"?></td>
				<td style="text-align:right;"><?=number_format($total*1.12,2)?></td>
			</tr>
			<tr>
				<td colspan="2"><?=$header['taxtype']=="vatexempt"?number_format($total,2):""?></td>
				<td colspan="4" style="text-align:right;"><?=$_REQUEST['tbltype']=="sales_invoice"?"":"Less: VAT"?></td>
				<td style="text-align:right;"><?=number_format($total*0.12,2)?></td>
			</tr>
			<tr>
				<td colspan="2"><?=$header['taxtype']=="zerorated"?number_format($total,2):""?></td>
				<td colspan="4" style="text-align:right;"><?=$_REQUEST['tbltype']=="sales_invoice"?"":"Amt: Net of VAT"?></td>
				<td style="text-align:right;"><?=number_format($total,2)?></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td colspan="4" style="text-align:right;"><?=$_REQUEST['tbltype']=="sales_invoice"?"":"Add: VAT"?></td>
				<td style="text-align:right;"><?=number_format($total*0.12,2)?></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td colspan="4" style="text-align:right;"></td>
				<td style="text-align:right;"><?=number_format($total*1.12,2)?></td>
			</tr>
		</table>
		<div style="clear:both;height:5px;">&nbsp;</div>
		<div style="font-size:9px;psition:absolute;bottom:20px;"><?=$header['preparedby']?></div>
</body>

<script>
$(document).ready(function() {
	
});


</script>
</html>