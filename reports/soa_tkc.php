<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
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
			background-image: url('../images/tkc.png');
			background-size: 90px 60px;
			background-repeat: no-repeat;
			background-position: left top;
			text-align:left;
			padding-top:10px;
			width:100%;
		}
		.logo h1{
			position:relative;
			left: 110px;
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
	from tbl_customers_trans where ".($_REQUEST['acctid']?"cust_id='{$_REQUEST['acctid']}' and":"")." (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment')
) as myTbl 
left join tbl_customers info on myTbl.cust_id=info.cust_id 
group by  myTbl.cust_id";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<div class="logo">
		<h1>TKC</h1>
		<span style="font-size:12px;">17 Bulacao Pardo, Cebu City 6000<br/>
		Tel. Nos.: 505-01111,505-0222,505-0333 Fax No.: 505-0666<br/>
		TIN : 005-255-269-000</span>
	</div>
	<h2 align="center">STATEMENT OF ACCOUNT</h2>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
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
						from tbl_customers_trans where cust_id='{$row['cust_id']}' and (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment')";
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
	
	<?php
	echo '<br/><br/><table  cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td>Prepared By:</td>
					<td>Certified Correct By:</td>
					<td>Received By:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>____________________</td>
					<td>____________________</td>
					<td>____________________</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				</table>';
	?>
	<div id="dialog"></div>
</body>
<script>
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