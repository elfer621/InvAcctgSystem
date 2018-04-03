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
			/*letter-spacing: 0.1em;*/
		}
	</style>
	</head>
	<body style="width:1300px;font-family:Sans-Serif;">
		<div style="width:23%;float:left;padding-left:20px;padding-top:65px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<?php 
				$basic_rent = ($info['rent_amount']/1.07);
				$wholding= ($basic_rent*.05);
				$vat = ($basic_rent*0.12)+($info['other_amount']/9.3333333);
				$vatable = ($info['total_amount']+$wholding)-$vat;
				
				
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
					<td style="text-align:right;"><?=number_format($info['total_amount']+$wholding,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($vat,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($vatable,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($wholding,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['total_amount'],2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($vatable,2);?></td>
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
					<td style="text-align:right;"><?=number_format($vat,2);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($info['total_amount']+$wholding,2);?></td>
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
		<div style="width:73%;float:right;padding-top:140px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td style="width:100px;">&nbsp;</td>
					<td style="width:95px;">&nbsp;</td>
					<td style="width:420px;"></td>
					<td style="text-align:left;" colspan="2"><?=$info['date']?></td>
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
					<td><?=ucwords(strtolower($info['receivedfrom']))?></td>
					<td colspan="2" style="text-align:left;padding-left:15px;"><?=$info['tin']?></td>
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
					<td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ucwords(strtolower($info['customer_address']));//ucwords(strtolower(mb_strimwidth($info['customer_address'], 0, 70, '...')))?></td>
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
					<td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ucwords(strtolower($info['nature_of_business']))?></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="5" style="text-align:center;"><?=ucwords($db->intToWords((double)$info['total_amount']))?></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left;padding-left:60px;"><?=number_format($info['total_amount'],2)?></td>
					<td colspan="3" style="text-align:left;padding-left:240px;"><?=ucwords(strtolower($info['paymentof']))?></td>
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
					<td colspan="5" style="padding-left:10px;"><?= ($info['check_date']=="0000-00-00"?"":$info['check_details']." ".$info['check_date']." ".number_format($info['check_amt'],2))?></td>
				</tr>
			</table>
		</div>
	</body>
</html>