<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
			@page{
				size: A4;
				margin:1cm;
			}
			thead { display: table-header-group; }
			tfoot { display: table-footer-group; }
		}
</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$info = $db->getWHERE("a.*,b.user certifiedby,c.user approver,d.user prepared,(select description from tbl_journal_category where code=a.type) type_desc",
		"tbl_vouchering a 
		left join tbl_user b on a.certifiedcorrect=b.id 
		left join tbl_user c on a.approvedby=c.id
		left join tbl_user d on a.preparedby=d.id ",
		"where a.id='{$_REQUEST['refid']}' and a.type='{$_REQUEST['type']}'");
$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_REQUEST['refid']}' and type='{$_REQUEST['type']}'  order by dr desc");
if($mode=="main"){
	if($_REQUEST['center']||$_REQUEST['center']!=''){
		$_REQUEST['center'] = ($_REQUEST['center']=="ucmambalin"?"ucmambaling":$_REQUEST['center']);
		$info = $db->getWHERE("a.*,b.user certifiedby,c.user approver,d.user prepared,(select description from tbl_journal_category where code=a.type) type_desc",
			"tbl_vouchering a 
			left join tbl_user b on a.certifiedcorrect=b.id 
			left join tbl_user c on a.approvedby=c.id
			left join tbl_user d on a.preparedby=d.id ",
			"where a.id='{$_REQUEST['refid']}'");
		$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_REQUEST['refid']}' and type='{$_REQUEST['type']}'  order by dr desc");
	}
}
// if($_REQUEST['center']||$_REQUEST['center']!=''){
	// if($mode=="branch"){
		// $info = $db->getWHERE("a.*,b.user certifiedby,c.user approver,d.user prepared",
			// "tbl_vouchering a 
			// left join tbl_user b on a.certifiedcorrect=b.id 
			// left join tbl_user c on a.approvedby=c.id
			// left join tbl_user d on a.preparedby=d.id ",
			// "where a.id='{$_REQUEST['refid']}'");
		// $journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_REQUEST['refid']}' order by dr desc");
	// }else{
		// $_REQUEST['center'] = ($_REQUEST['center']=="ucmambalin"?"ucmambaling":$_REQUEST['center']);
		// $info = $db->getWHERE("a.*,b.user certifiedby,c.user approver,d.user prepared",
			// "tbl_vouchering_{$_REQUEST['center']} a 
			// left join tbl_user b on a.certifiedcorrect=b.id 
			// left join tbl_user c on a.approvedby=c.id
			// left join tbl_user d on a.preparedby=d.id ",
			// "where a.id='{$_REQUEST['refid']}'");
		// $journal = $db->resultArray("*","tbl_journal_entry_{$_REQUEST['center']}","where refid='{$_REQUEST['refid']}'  order by dr desc");
	// }
// }else{
	// $info = $db->getWHERE("a.*,b.user certifiedby,c.user approver,d.user prepared",
			// "tbl_vouchering a 
			// left join tbl_user b on a.certifiedcorrect=b.id 
			// left join tbl_user c on a.approvedby=c.id
			// left join tbl_user d on a.preparedby=d.id ",
			// "where a.id='{$_REQUEST['refid']}'");
	// $journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_REQUEST['refid']}'  order by dr desc");
	// $cheque =  $db->getWHERE("a.*,b.*","tbl_bank_entry a left join tbl_bank_account b on a.bank_refid=b.id",
		// "where a.voucher_ref='{$_REQUEST['refid']}'");
// }

?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:900px;">
<div style="clear:both;height:85px;"></div>
<div style="float:left;width:45%;">
	<div class="lbl">&nbsp;</div>
	<div style="float:left;width:60%;"><?php echo strtoupper($info['reference']) ?></div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="float:right;width:45%;">
	<div class="lbl">&nbsp;</div>
	<div style="float:left;"><?php echo $info['date'] ?></div>
	<div style="clear:both;height:5px;"></div>
</div>
<div style="clear:both;height:5px;"></div>
<div class="lbl">&nbsp;</div>
<div style="float:left;width:85%;"></div>
<div style="clear:both;height:20px;"></div>
<div style="float:left;width:75%;height:100px;"><?php echo $info['remarks']?$info['remarks']:"&nbsp;" ?></div>
<div style="float:left;width:20%;height:100px;text-align:right;"><?php echo $info['total']?number_format($info['total'],2):"&nbsp;" ?></div>
<div style="clear:both;height:5px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" width="50%">
	<tbody>
	<?php
	if($journal){
		$sumdr=0;$sumcr=0;
		foreach($journal as $key=>$val){
			$reference="";
			if($db->strpos_arr($val['account_desc'],array("ACCOUNTS RECEIVABLE","A/R")) !== false){
				$ref=$db->getWHERE("customer_name","tbl_customers","where cust_id='{$val['ar_refid']}'");
				$reference=$ref['customer_name'];
			}
			if($db->strpos_arr($val['account_desc'],array("ACCOUNTS PAYABLE","A/P")) !== false){
				$ref=$db->getWHERE("supplier_name","tbl_supplier","where id='{$val['ap_refid']}'");
				$reference=$ref['supplier_name'];
			}
			if(strpos($val['account_desc'],"CASH IN BANK") !== false){
				$reference=$val['bank']." ".$val['check_number']." ".$val['check_date'];
			}
			echo '<tr>
				<td>'.$val['account_code'].'</td>
				<td>'.$val['account_desc'].'</td>
				<td>'.$reference.'</td>
				<td style="text-align:right;">'.($val['dr']==0?"":number_format($val['dr'],2)).'</td>
				<td style="text-align:right;">'.($val['cr']==0?"":number_format($val['cr'],2)).'</td>
			</tr>';
			$sumdr+=$val['dr'];$sumcr+=$val['cr'];
		}
	}
	?>
	</tbody>
	<tfoot>
		<tr style="border-top:1px solid #000;">
			<th colspan="3">Total</th>
			<th id="dr_amt_total" style="text-align:right;"><?=number_format($sumdr,2)?></th>
			<th id="cr_amt_total" style="text-align:right;"><?=number_format($sumcr,2)?></th>
		</tr>
	</tfoot>
</table>
<div style="clear:both;height:20px;"></div>
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