<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
<!--<link rel="stylesheet" href="../css/tblstyle.css" type="text/css" />-->
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: solid;
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
			border-style: solid;
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
		.logo {
			background-image: url('../images/rtklogoNew.png');
			background-size: 170px 60px;
			background-repeat: no-repeat;
			background-position: left top;
			text-align:left;
			padding-top:10px;
			width:100%;
		}
		.logo h1{
			position:relative;
			left: 180px;
		}
		@media print {
			* {-webkit-print-color-adjust:exact;}
		}
	</style>
</head>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
mysql_query("SET SESSION group_concat_max_len = 10000000");
$date = (!empty($_REQUEST['begdate']) && !empty($_REQUEST['enddate'])?"and (`date` between '{$_REQUEST['begdate']}' and '{$_REQUEST['enddate']}')":"");
$sql="select
	myTbl.cust_id,
	info.customer_name,
	sum(Current) as Current,
	sum(Balance30) as Balance30,
	sum(Balance60) as Balance60,
	sum(Balance90) as Balance90,
    sum(Balance120) as Balance120
from (
	select
		cust_id,
		date_format(`date`,'%Y-%m-%d') xdate,
		details,
		case
			when datediff (curdate(), date_format(`date`,'%Y-%m-%d')) < 30 then amount
			else 0
		end as Current,
		case
			When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) = 30 then amount 
			Else 0
		End as Balance30,
		case
			When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 30 
			and datediff (curdate(), date_format(`date`,'%Y-%m-%d')) <= 60 then amount
			Else 0
		End as Balance60,
		case
			When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 61 
			and datediff (curdate(), date_format(`date`,'%Y-%m-%d')) <= 90 then amount
			Else 0
		End as Balance90,
		case
			When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 90 
			 then amount
			Else 0
		End as Balance120
	from tbl_customers_trans where ".($_REQUEST['acctid']?"cust_id='{$_REQUEST['acctid']}' and":"")." (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment' or transtype='Credit Memo') $date 
) as myTbl 
left join tbl_customers info on myTbl.cust_id=info.cust_id 
group by  myTbl.cust_id";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<div class="logo">
		<h1>Marketing, Inc.</h1>
		<span style="font-size:12px;">1-62 A. Flordeliz St. Jude Acres, Bulacao, Cebu City<br/>
		Tel. Nos.: 273-7441/273-7442, Fax: 272-0061 (Cebu)<br/>
		Tel. Nos.: 221-8423 (Davao)<br/>
		TIN : 000-552-122-000</span>
	</div>
	<h2 align="center">STATEMENT OF ACCOUNT</h2>
	<div style="clear:both;height:20px;"></div>
	<form method="post" name="frmFilter">
		<fieldset>
			<legend>Filter</legend>
			<div style="float:left;width:50px;">Beg Date</div>
			<input type="text" name="begdate" id="begdate" value="<?=$_REQUEST['begdate']?>" style="float:left;width:150px;margin-right:20px;"/>
			<div style="float:left;width:50px;">End Date</div>
			<input type="text" name="enddate" id="enddate" value="<?=$_REQUEST['enddate']?>" style="float:left;width:150px;margin-right:20px;"/>
			<input type="submit" value="Execute" style="float:right;width:150px;"/>
		</fieldset>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%" >
		<thead>
			<tr>
				<th>Acct #</th>
				<th>Customer Name</th>
				<th>120 days</th>
				<th>90 days</th>
				<th>60 days</th>
				<th>30 days</th>
				<th>Current</th>
				<th>Total Due</th>
			</tr>
		</thead>
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><a href="javascript:viewTrans('<?php echo $row['cust_id'] ?>','<?php echo $row['customer_name'] ?>')"><?php echo $row['cust_id'] ?></a></td>
				<td colspan="7"><b><?= $row['customer_name']?></b></td>
			</tr>
			<?php $s = "select
							cust_id,
							date_format(`date`,'%Y-%m-%d') xdate,
							details,
							case
								when datediff (curdate(), date_format(`date`,'%Y-%m-%d')) < 30 then amount
								else 0
							end as Current,
							case
								When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) = 30 then amount 
								Else 0
							End as Balance30,
							case
								When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 30 
								and datediff (curdate(), date_format(`date`,'%Y-%m-%d')) <= 60 then amount
								Else 0
							End as Balance60,
							case
								When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 61 
								and datediff (curdate(), date_format(`date`,'%Y-%m-%d')) <= 90 then amount
								Else 0
							End as Balance90,
							case
								When datediff (curdate(), date_format(`date`,'%Y-%m-%d')) > 90 
								 then amount
								Else 0
							End as Balance120
						from tbl_customers_trans where cust_id='{$row['cust_id']}' and (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment' or transtype='Credit Memo') $date ";
				$list = $con->resultArray($con->Nection()->query($s));
				foreach($list as $k => $val){
					?>
					<tr>
						<td style="text-align:center;"><?=$val['xdate']?></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$val['details']?></td>
						<td style="text-align:right;"><?=$val['Balance120']!=0?number_format($val['Balance120'],2):''?></td>
						<td style="text-align:right;"><?=$val['Balance90']!=0?number_format($val['Balance90'],2):''?></td>
						<td style="text-align:right;"><?=$val['Balance60']!=0?number_format($val['Balance60'],2):''?></td>
						<td style="text-align:right;"><?=$val['Balance30']!=0?number_format($val['Balance30'],2):''?></td>
						<td style="text-align:right;"><?=$val['Current']!=0?number_format($val['Current'],2):''?></td>
						<td style="text-align:right;"><?= number_format($val['Current']+$val['Balance30']+$val['Balance60']+$val['Balance90']+$val['Balance120'],2)?></td>
					</tr>
					<?php }  ?>
			<tr>
				<td colspan="2" style="text-align:center;font-weight:bold;">Sub Total</td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Balance120'],2)?></td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Balance90'],2)?></td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Balance60'],2)?></td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Balance30'],2)?></td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Current'],2)?></td>
				<td style="text-align:right;font-weight:bold;"><?= number_format($row['Current']+$row['Balance30']+$row['Balance60']+$row['Balance90']+$row['Balance120'],2)?></td>
			</tr>
			<tr>
				<?php $total = $row['Current']+$row['Balance30']+$row['Balance60']+$row['Balance90']+$row['Balance120']; ?>
				<td colspan="2" style="text-align:center;font-weight:bold;">PERCENTAGE</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format(($row['Balance120']/$total) *100,2)?>%</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format(($row['Balance90']/$total) *100,2)?>%</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format(($row['Balance60']/$total) *100,2)?>%</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format(($row['Balance30']/$total) *100,2)?>%</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format(($row['Current']/$total) *100,2)?>%</td>
				<td style="text-align:center;font-weight:bold;"><?= number_format((($row['Current']+$row['Balance30']+$row['Balance60']+$row['Balance90']+$row['Balance120'])/$total) *100,2)?>%</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div id="dialog"></div>
</body>
<script>
$(document).ready(function(){
	$('#begdate,#enddate').datepicker({
		inline: true,
		changeMonth: true,
        changeYear: true,
		dateFormat:"yy-mm-dd"
	});
});
function viewTrans(id,cust_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:cust_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=custtransdetails&acctid='+id,async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}

</script>
</html>