<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
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
</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/numToWords.php";
$db=new dbConnect();
$db->openDb();
$num2words = new numwordsnew();
$info = $db->getWHERE("a.*,b.user certifiedby,c.user approver",
		"tbl_vouchering a left join tbl_user b on a.certifiedcorrect=b.id left join tbl_user c on a.approvedby=c.id",
		"where a.id='{$_REQUEST['refid']}'");
$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_REQUEST['refid']}'");
$cheque =  $db->getWHERE("a.*,b.*","tbl_bank_entry a left join tbl_bank_account b on a.bank_refid=b.id",
		"where a.voucher_ref='{$_REQUEST['refid']}'");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1200px;">
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td style="font-size:9px;height:10px;">ACCOUNT NO</td>
		<td style="font-size:9px;height:10px;">ACCOUNT NAME</td>
		<td style="font-size:9px;height:10px;">CHECK NO</td>
	</tr>
	<tr>
		<td><?=$cheque['bank_account']?></td>
		<td><?=$cheque['bank_name']?></td>
		<td><?=$cheque['cheque_num']?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td style="border-bottom:1px solid #000;"><span class="print"><?=$info['date']?></span></td>
	</tr>
	<tr>
		<td>PAY TO THE ORDER OF</td>
		<td style="width:75%;text-align:center;border-bottom:1px solid #000;"><span class="print"><?php echo strtoupper($info['payee']) ?></span></td>
		<td style="width:15%;border-bottom:1px solid #000;"><span class="print"><?=number_format($info['total'],2)?></span></td>
	</tr>
	<tr>
		<td>PESOS</td>
		<td colspan="2" style="border-bottom:1px solid #000;">
			<?php
			$wordings = $num2words->convertCurrencyToWords($cheque['amount']);
			$wordings = strpos($wordings,"cents")?$wordings." only":$wordings." pesos only";
			echo '<span class="print">'.ucwords($wordings).'</span>';
			?>
		</td>
	</tr>
</table>

</div>
<?php
$db->closeDb();
?>
<script>
onload=function(){
	//window.print();
	//self.close();
}
</script>
</body>
</html>