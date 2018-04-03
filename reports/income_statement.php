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
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
$sql = "select a.*,b.category_name from (select category_id,sum(qty) as total_qty,sum(qty * selling) as sales,sum(qty * cost) as cost, 
	barcode,item_desc,unit
	from tbl_sales_items where date_format(`timestamp`,'%Y-%m-%d') between '$begdate' and '$enddate' group by category_id) a 
	left join tbl_category b on a.category_id=b.category_id";
$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	}else{
		$arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
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
// $qry = mysql_query($sql);
// if(!$qry){
	// echo mysql_error();
// }
?>
<body style="margin:0 auto 0;width:900px;font-size:14px;">
	<h2>Income Statement</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;margin-right:50px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<? 	//while($row = mysql_fetch_assoc($qry)){ 
		foreach($list as $key => $row){
	?>
	<? $total['qty']+=$row['total_qty'];$total[$row['category_name']]['sales']+=$row['sales'];$total[$row['category_name']]['cost']+=$row['cost'];} ?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td colspan="2">Sales - BOOKS</td><td style="text-align:right;"><?=number_format($total['BOOKS']['sales'],2)?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">Sales - ID SLINGS</td><td style="text-align:right;border-bottom:1px solid black;"><?=number_format($total['ID SLINGS']['sales'],2)?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">Total Sales</td>
			<td>&nbsp;</td>
			<td style="text-align:right;"><?=number_format($sales = ($total['BOOKS']['sales']+$total['ID SLINGS']['sales']),2)?></td>
		</tr>
		
		<tr>
			<td colspan="2">Cost - BOOKS</td><td style="text-align:right;"><?=number_format($total['BOOKS']['cost'],2)?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">Cost - ID SLINGS</td><td style="text-align:right;border-bottom:1px solid black;"><?=number_format($total['ID SLINGS']['cost'],2)?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">Total Cost</td>
			<td>&nbsp;</td>
			<td style="text-align:right;"><?=number_format($cost = ($total['BOOKS']['cost']+$total['ID SLINGS']['cost']),2)?></td>
		</tr>
		<?php $gross = $sales-$cost; ?>
		<tr>
			<td>Gross Income</td>
			<td style="text-align:right;"><?=" [".number_format(($gross/$cost)*100,2)."%]"?></td>
			<td>&nbsp;</td>
			<td style="text-align:right;border-top:1px solid black;width:200px;"><?=number_format($gross,2)?></td>
		</tr>
		<tr>
			<td colspan="3">Less: Expenses</td>
		</tr>
		<?php $new_db = mysql_select_db("wrfa_issuance",$db->con); 
			if($new_db){
				$sql = "select exp_cat,sum(amount) as total_exps from tbl_voucher where 
					type='Expenses' and (status='CheckPrinted' or status='Hiden' or status='Done') and (date between '$begdate' and '$enddate') group by exp_cat";
				$qry = mysql_query($sql);
				while($row=mysql_fetch_assoc($qry)){
				$total_exps+=$row['total_exps'];
		?>
			<tr>
				<td></td>
				<td><?=$row['exp_cat']?></td>
				<td style="text-align:right;"><?=number_format($row['total_exps'],2)?></td>
			</tr>
		<?php
				}
		?>
			<tr>
				<td colspan="3">Total Expenses</td><td style="text-align:right;border-top:1px solid black;width:200px;"><?=number_format($total_exps,2)?></td>
			</tr>
		<?php
			}
		?>
	<tr>
		<td colspan="3" style="border:1px solid black;padding:10px;">Net <?= $gross>$total_exps?'Income':'Loss';?></td><td style="border:1px solid black;text-align:right;padding:10px;"><?=number_format($gross-$total_exps,2)?></td>
	</tr>
	</table>
	<div id="dialog"></div>
</body>
<script>
$(document).ready(function() {
	$('#beg_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});

</script>
</html>