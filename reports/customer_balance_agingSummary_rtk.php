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
			border-style: solid;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px 0px 1px 0px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
			font-size:12px;
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			font-size:12px;
			padding:5px;
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
		@page  
		{ 
			size: auto;   /* auto is the initial value */ 

			/* this affects the margin in the printer settings 
			margin: 15mm 15mm 15mm 15mm;  */ 
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
$_REQUEST['begdate'] = $_REQUEST['begdate']?$_REQUEST['begdate']:date('2015-m-01');
$_REQUEST['enddate'] = $_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d');
$date = (!empty($_REQUEST['begdate']) && !empty($_REQUEST['enddate'])?"and (`date` between '{$_REQUEST['begdate']}' and '{$_REQUEST['enddate']}')":"");
$sales_rep = !empty($_REQUEST['sales_rep'])?"where info.agent_id='{$_REQUEST['sales_rep']}'":"";
$rep = $con->resultArray($con->Nection()->query("select * from req_agent"));
?>
<body style="margin:0 auto 0;width:1000px;font-size:12px;">
	<div class="logo">
		<h1>Marketing, Inc.</h1>
		<span style="font-size:12px;">1-62 A. Flordeliz St. Jude Acres, Bulacao, Cebu City<br/>
		Tel. Nos.: 273-7441/273-7442, Fax: 272-0061 (Cebu)<br/>
		Tel. Nos.: 221-8423 (Davao)<br/>
		TIN : 000-552-122-000</span>
	</div>
	<h2>Customer Balance Aging Reports</h2>
	<div style="clear:both;height:20px;"></div>
	<form method="post" name="frmFilter">
		<fieldset>
			<legend>Filter</legend>
			<div style="float:left;width:50px;">Beg Date</div>
			<input type="text" name="begdate" id="begdate" value="<?=$_REQUEST['begdate']?$_REQUEST['begdate']:date('2015-m-01')?>" style="float:left;width:150px;margin-right:20px;"/>
			<div style="float:left;width:50px;">End Date</div>
			<input type="text" name="enddate" id="enddate" value="<?=$_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d')?>" style="float:left;width:150px;margin-right:20px;"/>
			<div style="float:left;width:50px;">Sales Rep</div>
			<select name="sales_rep" style="float:left;width:150px;">
			<option value="">Select Rep</option>
			<?php foreach($rep as $x=>$r){
				echo "<option value='".$r['id']."'>".$r['agent_name']."</option>";
			} ?>
			</select>
			<input type="submit" value="Execute" style="float:right;width:150px;"/>
		</fieldset>
	</form>
	<div style="clear:both;height:20px;"></div>
	<?
	$sql="select
		rep.id,
		group_concat(info.cust_id) cust_ids,
		rep.agent_name,
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
				when datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) < 30 then amount
				else 0
			end as Current,
			case
				When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) = 30 then amount 
				Else 0
			End as Balance30,
			case
				When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 30 
				and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 60 then amount
				Else 0
			End as Balance60,
			case
				When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 61 
				and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 90 then amount
				Else 0
			End as Balance90,
			case
				When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 90 
				 then amount
				Else 0
			End as Balance120
		from tbl_customers_trans where (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment' OR transtype='Credit Memo') $date
	) as myTbl 
	left join tbl_customers info on myTbl.cust_id=info.cust_id 
	left join req_agent rep on info.agent_id = rep.id 
	$sales_rep 
	group by info.agent_id";
	$q = mysql_query($sql);
	echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >';
	while($r = mysql_fetch_assoc($q)){
		echo '<tr><td>'.$r['id'].'</td><td colspan="7"><b>'.$r['agent_name'].'</b></td></tr>';
		?>
		<tr>
			<th rowspan="2" colspan="2">Sub Total</th>
			<th>120 days</th>
			<th>90 days</th>
			<th>60 days</th>
			<th>30 days</th>
			<th>Current</th>
			<th>Total Due</th>
		</tr>
		<tr>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Balance120'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Balance90'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Balance60'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Balance30'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Current'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($r['Current']+$r['Balance30']+$r['Balance60']+$r['Balance90']+$r['Balance120'],2)?></td>
		</tr>
		<tr><td colspan="8">
		<?
		$sql="select
			myTbl.cust_id,
			info.customer_name,
			rep.agent_name,
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
					when datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) < 30 then amount
					else 0
				end as Current,
				case
					When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) = 30 then amount 
					Else 0
				End as Balance30,
				case
					When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 30 
					and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 60 then amount
					Else 0
				End as Balance60,
				case
					When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 61 
					and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 90 then amount
					Else 0
				End as Balance90,
				case
					When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 90 
					 then amount
					Else 0
				End as Balance120
			from tbl_customers_trans where cust_id in ({$r['cust_ids']}) and (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment' OR transtype='Credit Memo') $date
		) as myTbl 
		left join tbl_customers info on myTbl.cust_id=info.cust_id 
		left join req_agent rep on info.agent_id = rep.id 
		group by info.agent_id,myTbl.cust_id";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
	?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
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
			<tr>
				<td><a href="javascript:viewTrans('<?php echo $row['cust_id'] ?>','<?php echo $row['customer_name'] ?>')"><?php echo $row['cust_id'] ?></a></td>
				<td colspan="7"><b><?= $row['customer_name']?></b> (<?=$row['agent_name']?>)</td>
			</tr>
			<?php $s = "select
							cust_id,
							date_format(`date`,'%Y-%m-%d') xdate,
							details,
							case
								when datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) < 30 then amount
								else 0
							end as Current,
							case
								When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) = 30 then amount 
								Else 0
							End as Balance30,
							case
								When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 30 
								and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 60 then amount
								Else 0
							End as Balance60,
							case
								When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 61 
								and datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) <= 90 then amount
								Else 0
							End as Balance90,
							case
								When datediff ('{$_REQUEST['enddate']}', date_format(`date`,'%Y-%m-%d')) > 90 
								 then amount
								Else 0
							End as Balance120
						from tbl_customers_trans where cust_id='{$row['cust_id']}' and (or_ref = '' or or_ref is null) and (transtype='sales_invoice' or transtype='Adjustment' OR transtype='Credit Memo') $date";
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
			<?php 
				$grandTotal['Current']+=$row['Current'];
				$grandTotal['Balance30']+=$row['Balance30'];
				$grandTotal['Balance60']+=$row['Balance60'];
				$grandTotal['Balance90']+=$row['Balance90'];
				$grandTotal['Balance120']+=$row['Balance120'];
				$grandTotal['subtotal']+=$total;
			} ?>
		</tbody>
		
		
	</table>
	</td></tr>
	<tr><td style="border:none;">&nbsp;</td></tr>
	<? } ?>
	<thead>
		<tr>
			<td colspan="2" style="text-align:center;font-weight:bold;">Grand Total</td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['Balance120'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['Balance90'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['Balance60'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['Balance30'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['Current'],2)?></td>
			<td style="text-align:right;font-weight:bold;"><?= number_format($grandTotal['subtotal'],2)?></td>
		</tr>
		<tr><td style="border:none;">&nbsp;</td></tr>
	</thead>
	</table>
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