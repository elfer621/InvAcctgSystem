<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
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
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
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
	<style type="text/css" media="print" >
		thead {display: table-header-group !important;}
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
	if($db->strpos_arr($val,array("Campus")) == false){
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}else{
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}
}
$sql = "SELECT 
			counter,
			concat(min(receipt),' - ',max(receipt)) receipt_range,
			sum(if(category_id = 1,qty*selling,0)) book_sales,
			sum(if(category_id = 1,qty*cost,0)) book_cost,
			sum(if(category_id = 1,qty*vat,0)) book_vat,
			sum(if(category_id = 2,qty*selling,0)) idsling_sales,
			sum(if(category_id = 2,qty*cost,0)) idsling_cost,
			sum(if(category_id = 2,qty*vat,0)) idsling_vat,
			sum(qty*selling) total_sales,
			sum(qty*cost) total_cost,
			sum(qty*vat) total_vat,
			(select variable_values from settings where variable_name='session_connect') campus 
		from (select a.*,b.date from tbl_sales_items a left join view_receipt b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading) tbl ".($where?"where $where":"")." group by counter asc with rollup";
$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
		
	}else{
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			}
		}
	}
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
<body class="print" style="margin:0 auto 0;width:1200px;font-size:12px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="print tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="10"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Sales Reports Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="10">
					<?php
						foreach($output as $key => $val){
							echo $val."<br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<th rowspan="2">CAMPUS</th>
				<th rowspan="2">COUNTER</th>
				<th rowspan="2">RECEIPT NO</th>
				<th colspan="3">VAT INCLUSIVE</th>
				<th colspan="1">VAT EXEMPT</th>
				<th rowspan="2">TOTAL SALES</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>VATABLE</th>
				<th>VAT</th>
				
				<th>BOOKS</th>
				
			</tr>
		</thead>
		<tbody>
			<?php 
			$grand_bs=0;$grand_bc=0;$grand_bv=0;$grand_is=0;$grand_ic=0;$grand_iv=0;$grand_ts=0;$grand_tc=0;$grand_tv=0;
			foreach($list as $key => $val){ ?>
				<?php if($val['counter']==null){ ?>
					<tr style="border:1px solid black;font-weight:bold;">
						<td colspan="3" style="text-align:center;">Sub Total</td>
						<td style="text-align:right;"><?=number_format($val['idsling_sales'],2)?></td>
						<td style="text-align:right;"><?=number_format($val['idsling_sales']/1.12,2)?></td>
						<td style="text-align:right;"><?=number_format($val['idsling_vat'],2)?></td>
						
						<td style="text-align:right;"><?=number_format($val['book_sales'],2)?></td>
						
						<td style="text-align:right;"><?=number_format(($val['idsling_sales']+$val['book_sales']),2)//."|".number_format($val['total_sales'],2)?></td>
					</tr>
				<?php 
					$grand_bs+=$val['book_sales'];$grand_bc+=$val['book_cost'];$grand_bv+=$val['book_vat'];
					$grand_is+=$val['idsling_sales'];$grand_ic+=$val['idsling_cost'];$grand_iv+=$val['idsling_vat'];
					$grand_ts+=($val['idsling_sales']+$val['book_sales']);$grand_tc+=$val['total_cost'];$grand_tv+=$val['total_vat'];
				}else{ ?>
					<tr>
						<td style="text-align:center;"><?=strtoupper($val['campus'])?></td>
						<td style="text-align:center;"><?=$val['counter']?></td>
						<td style="text-align:center;"><?=$val['receipt_range']?></td>
						
						<td style="text-align:right;"><?=number_format($val['idsling_sales'],2)?></td>
						<td style="text-align:right;"><?=number_format($val['idsling_sales']/1.12,2)?></td>
						<td style="text-align:right;"><?=number_format($val['idsling_vat'],2)?></td>
						
						<td style="text-align:right;"><?=number_format($val['book_sales'],2)?></td>
						
						<td style="text-align:right;"><?=number_format(($val['idsling_sales']+$val['book_sales']),2)//."|".number_format($val['total_sales'],2)?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr style="border:1px solid black;font-weight:bold;">
				<td colspan="3" style="text-align:center;">Grand Total</td>
				<td style="text-align:right;"><?=number_format($grand_is,2)?></td>
						<td style="text-align:right;"><?=number_format($grand_is/1.12,2)?></td>
						<td style="text-align:right;"><?=number_format($grand_iv,2)?></td>
						
						<td style="text-align:right;"><?=number_format($grand_bs,2)?></td>
						
						<td style="text-align:right;"><?=number_format($grand_ts,2)?></td>
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