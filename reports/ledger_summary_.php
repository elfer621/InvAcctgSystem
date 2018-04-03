<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
			padding:3px;
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			padding:0 3px;
		}
		table.tbl2 td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
		}
		body,td {
			font-size:15px !important;
		}
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
// echo $_REQUEST['where'];
// echo "<br/>";
// $output = preg_split( "/ (=|!=|>|<|>=|<=|like) /", $_REQUEST['where'] );
// $exploded = $con->multiexplode(array("=","!=",">","<",">=","<=","like"),$_REQUEST['where']);
// print_r($output);
// echo "<br/>";
// print_r($exploded);
$output = preg_split( "/ (and) /", $_REQUEST['where'] );
//print_r($output);
$sql = "select date,reference,type,refid,payto,dr,cr,check_number,bank,check_date,account_desc from 
	(select a.*,b.reference,b.remarks from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id) tbl ".($_REQUEST['where']?"where {$_REQUEST['where']}":"").";";
//echo $sql;
$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	$arrs[]=$con->pdoStyle($con->con_ucmambaling['ip'],$con->con_ucmambaling['db'],$sql);
	$arrs[]=$con->pdoStyle($con->con_uclm['ip'],$con->con_uclm['db'],$sql);
	$arrs[]=$con->pdoStyle($con->con_ucmain['ip'],$con->con_ucmain['db'],$sql);
	$arrs[]=$con->pdoStyle($con->con_ucbanilad['ip'],$con->con_ucbanilad['db'],$sql);
	$arrs[]=$con->pdoStyle($con->con_main['ip'],$con->con_main['db'],$sql);
}
	$list = array();
	foreach($arrs as $arr) {
		if(is_array($arr)) {
			$list = array_merge($list, $arr);
		}
	}
	//print_r($result);
	
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<body style="margin:0 auto 0;width:1200px;font-size:12px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;">
	<h2><?=$db->stockin_header;?><br/>Ledger Entry<br/></h2>
	<div style="clear:both;height:10px;"></div>
	<?php
		foreach($output as $key => $val){
			echo $val."<br/>";
		}
	?>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Entry Date</th>
				<th>Reference</th>
				<th>Journal</th>
				<th>Entry #</th>
				<th>Payee</th>
				<th>Account Desc</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Running Balance</th>
				<th>Check Ref.</th>
				<th>Check Date</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$rt=0;
			foreach($list as $key => $val){ ?>
				<tr>
					<td style="text-align:right;"><?=$val['date']?></td>
					<td style="text-align:left;"><?=$val['reference']?></td>
					<td style="text-align:center;"><?=$val['type']?></td>
					<td style="text-align:right;"><?=$val['refid']?></td>
					<td style="text-align:left;"><?=$val['payto']?></td>
					<td style="text-align:left;"><?=$val['account_desc']?></td>
					<td style="text-align:right;"><?=($val['dr']!=0?number_format($val['dr'],2):"")?></td>
					<td style="text-align:right;"><?=($val['cr']!=0?number_format($val['cr'],2):"")?></td>
					<td style="text-align:right;"><?=number_format(($rt+=($val['cr']-$val['dr'])),2)?></td>
					<td style="text-align:right;"><?=$val['check_number']." ".$val['bank']?></td>
					<td style="text-align:right;"><?=($val['check_date']=="0000-00-00"?"":$val['check_date'])?></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#beg_date').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
});
function viewTrans(sku_id,prod_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:prod_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=prodtrans&sku_id='+sku_id+'&type=<?=$tbl?>&reading=<?=$_REQUEST['reading']?>',async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}

</script>
</html>