<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
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
			font-size:13px !important;
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

$output = preg_split( "/ (and) /", $_REQUEST['where'] );
$flag=false;
foreach($output as $key => $val){
	if($db->strpos_arr($val,array("Campus")) == true){
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("begdate")) == true){
		$begdate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("enddate")) == true){
		$enddate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
$where = $where?"where $where":"where a.fiscal_year='".date('Y')."'";
if(date('Y')=='2017'){
	$date1 = "and (`date` < '".trim($begdate[1])."' and `date` >= '2017-04-30')";
}else{
	$date1 = "and `date` < '".trim($begdate[1])."'";
}
$date2 = "and (`date` between '".trim($begdate[1])."' and '".trim($enddate[1])."')";

$bf_sql = "select if(a.center='' or a.center is null,(select variable_values from settings where variable_name='session_connect'),a.center) cost_center,sum(dr-cr) balfwd from tbl_journal_entry a $where $date1";
$recorded_center = "(select variable_values from settings where variable_name='session_connect') rec_center";
$costcenter = "if(COALESCE(a.center,'')='',(select variable_values from settings where variable_name='session_connect'),a.center) cost_center";
$sql = "select distinct a.id,a.*,b.reference,b.remarks,$costcenter,$recorded_center,concat(emp.firstname,' ',emp.lastname) empname from tbl_journal_entry a 
	left join tbl_vouchering b on a.refid=b.id and COALESCE(a.type,'')=COALESCE(b.type,'') 
	left join tbl_employees emp on a.ar_nontrade_refid=emp.id 
	$where $date2 order by a.type,a.date asc";
$sql = str_replace("`date`","a.`date`",$sql);
//echo $_SESSION['connect'];
$arrs=array();
$arrs2=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname,$sql);
	$arrs2[]=$con->pdoStyle($con->ipadd2,$con->dbname,$bf_sql);
}else{
	
	if($_SESSION['settings']['connection_type']=="multiple"){
		foreach($_SESSION['conlist'] as $key => $val){
			$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			$arrs2[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$bf_sql);
		}
	}else{
		$arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
		$arrs2[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$bf_sql);
	}
}
	$list = array();
	foreach($arrs as $arr) {
		if(is_array($arr)) {
			$list = array_merge($list, $arr);
		}
	}
	$list2 = array();
	foreach($arrs2 as $arr) {
		if(is_array($arr)) {
			$list2 = array_merge($list2, $arr);
		}
	}
	//print_r($result);
	// echo "<pre>";
	// print_r($arrs);
	// echo "</pre>";
	// echo $db->sum_array($list2,"balfwd");
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<body style="margin:0 auto 0;width:1200px;font-size:12px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="12" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Ledger Entry</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="7">
				<?php
					foreach($output as $key => $val){
						echo $val."<br/>";
					}
				?>
				</td>
			</tr>
			<tr>
				<th>Center</th>
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
				<th>More Info</th>
			</tr>
		</thead>
		<tbody>
			<?php $balfwd = $db->sum_array($list2,"balfwd"); ?>
			<tr>
				<td colspan="7" style="text-align:center;">Balance Forwarded</td>
				<td></td>
				<td></td>
				<td style="text-align:right;"><?=number_format($balfwd,2)?></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php 
			$rt=$balfwd?$balfwd:0;
			usort($list, $db->make_cmp(['type' => "asc",'date' => "asc"]));
			foreach($list as $key => $val){ 
			$rt+=($val['dr']-$val['cr']);
			?>
				<tr>
					<td style="text-align:left;font-size:10px !important;"><?=($val['rec_center']==$val['cost_center']?$val['cost_center']:$val['rec_center']."|".$val['cost_center'])?></td>
					<td style="text-align:right;font-size:10px !important;"><?=$val['date']?></td>
					<td style="text-align:left;font-size:10px !important;"><?=$val['reference']?></td>
			<td style="text-align:center;"><?=$val['type']?></td>
			<td style="text-align:right;"><?=$val['refid']?></td>
					<td style="text-align:left;"><?=$val['payto']?></td>
					<td style="text-align:left;"><?=$val['account_desc']?></td>
					<td style="text-align:right;"><?=($val['dr']!=0?number_format($val['dr'],2):"")?></td>
					<td style="text-align:right;"><?=($val['cr']!=0?number_format($val['cr'],2):"")?></td>
					<td style="text-align:right;"><?=$rt<0?"(".number_format(($rt * -1),2).")":number_format($rt,2)?></td>
					<td style="text-align:right;"><?=$val['check_number']." ".$val['bank']?></td>
					<td style="text-align:right;"><?=($val['check_date']=="0000-00-00"?"":$val['check_date'])?></td>
					<td style="text-align:right;"><?=$val['empname']." ".$val['ar_nontrade_refinfo']." ".$val['ar_nontrade_remarks']?></td>
				</tr>
			<?php 
			$total_dr+=$val['dr'];
			$total_cr+=$val['cr'];
			} ?>
		</tbody>
		<tfoot>
			<tr style="border-top:1px solid black;font-weight:bold;">
				<td colspan="7">Total</td>
				<td style="text-align:right;"><?=number_format($total_dr,2)?></td>
				<td style="text-align:right;"><?=number_format($total_cr,2)?></td>
				<td style="text-align:right;"><?=number_format($rt,2)?></td>
				<td colspan="3"></td>
			</tr>
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