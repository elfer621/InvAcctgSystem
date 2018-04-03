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
		<div style="width:23%;float:left;padding-top:60px;padding-left:20px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<?php 
				$total_sales=0;
				$array=unserialize($info['particular']);
				for($x=0;$x<count($array['particular']);$x++){ ?>
					<tr>
						<td><?=$array['particular'][$x]?></td>
						<td style="text-align:right;"><?=number_format($array['amt'][$x],2)?></td>
					</tr>
				<?php $total_sales+=$array['amt'][$x];} ?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format(($info['rent_amount']/1.07)*1.12,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format(($info['rent_amount']/1.07)*.12,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['rent_amount']/1.07,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format(($info['rent_amount']/1.07)*.05,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format(($info['rent_amount']/1.07)*.95,2);?></td>
				</tr>
				<tr>
					<td>Other Charges</td>
					<td style="text-align:right;"><?=number_format(($info['total_amount']/1.12)-(($info['rent_amount']/1.07)*.95),2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['total_amount']/1.12,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['total_amount']/9.3333333,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['total_amount'],2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;padding-top:5px;">x</td>
				</tr>
			</table>
		</div>
		<div style="width:73%;float:right;padding-top:150px;">
			<table cellspacing="0" cellpadding="0" border="1" width="100%">
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:150px;">&nbsp;</td>
					<td></td>
					<td></td>
					<td style="text-align:center;"><?=$info['date']?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><?=strtoupper($info['receivedfrom'])?></td>
					<td></td>
					<td style="text-align:center;"><?=$info['tin']?></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td colspan="3"><?=strtoupper($info['customer_address'])?></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><?=strtoupper($info['nature_of_business'])?></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="4" style="text-align:center;"><?=strtoupper($db->intToWords((double)$info['total_amount']))?></td>
				</tr>
				<tr>
					<td></td>
					<td><?=number_format($info['total_amount'],2)?></td>
					<td></td>
					<td></td>
					<td><?=$info['paymentof']?></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="5" style="padding-left:10px;"><?=$info['check_details']." ".$info['check_date']." ".number_format($info['check_amt'],2)?></td>
				</tr>
			</table>
		</div>
	</body>
</html>