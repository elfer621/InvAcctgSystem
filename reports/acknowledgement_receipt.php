<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$info=$db->getWHERE("a.*,b.*",
	"tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id",
	"where receipt='".$_REQUEST['receipt']."'");
?>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<style type="text/css">
		@page {
			margin: 0;
		}
		table td {
			font-size:13px;
			white-space: nowrap;
		}
	</style>
	</head>
	<body style="width:1300px;font-family:Sans-Serif;font-size:10px!important;">
		<div style="width:99%;float:right;padding-top:125px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td colspan="2"><?=$info['date']?></td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td colspan="3">&nbsp;&nbsp;&nbsp;<?=$info['receivedfrom']?></td>
					<td colspan="2" style="text-align:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$info['tin']?></td>
					
				</tr>
				
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$info['customer_address']?></td>
				</tr>
				
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$info['nature_of_business']?></td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ucwords($db->intToWords((double)$info['total_amount']))?></td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:100px;">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($info['total_amount'],2)?></td>
					<td style="width:100px;">&nbsp;</td>
					<td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$info['paymentof']?></td>
				</tr>
			</table>
		</div>
	</body>
</html>