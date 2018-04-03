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
$db=new dbConnect();
$db->openDb();

if($_SESSION['default_db']=='rber_db'){
	$sql = "select x.*,y.total_undelivered from 
			(
			select *,
			COALESCE(sum(if(transtype='Payment' or transtype='Credit Memo',amount * -1,if(transtype!='Cash',(amount-(paid_amount+paid_wvat)),0))),0) as total_bal 
			from (
			select tbl_main.*,c.customer_name,c.customer_address 
			from (select a.*,b.date_delivered from tbl_customers_trans a 
			left join tbl_customers_trans_delivery b
			on a.cust_id=b.cust_id and a.receipt=b.receipt) as tbl_main
			left join tbl_customers c on tbl_main.cust_id=c.cust_id) as tbl group by cust_id
			) as x
			left join
			(
			select cust_id,amount,date_delivered,transtype,coalesce(sum(if(date_delivered is null,(amount-(paid_amount+paid_wvat)),0)),0) as total_undelivered from (select tbl_main.* 
			from (select a.*,b.date_delivered from tbl_customers_trans a 
			left join tbl_customers_trans_delivery b
			on a.cust_id=b.cust_id and a.receipt=b.receipt where transtype!='Cash' and transtype!='Payment' and transtype!='Adjustment' and transtype!='Credit Memo') as tbl_main) as tbl group by cust_id
			) as y on x.cust_id=y.cust_id
			order by x.customer_name asc";
	}else{
		$sql = "select a.*,b.bal total_bal from tbl_customers as a
		left join 
		(select cust_id,COALESCE(sum(if(transtype!='Cash' or transtype!='Adjustment',if(transtype='Payment' or transtype='Credit Memo',amount * -1,amount),0)),0) as bal from tbl_customers_trans) as b on a.cust_id=b.cust_id 
		";
	}
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2>Customer Balance</h2>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Customer Name</th>
				<th>Address</th>
				<th>Total Balance</th>
				<th>Undelivered</th>
				<th>Actual Balance</th>
			</tr>
		</thead>
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><a href="javascript:viewTrans('<?php echo $row['cust_id'] ?>','<?php echo $row['customer_name'] ?>')"><?php echo $row['customer_name'] ?></a></td>
				<td><?= $row['customer_address']?></td>
				<td style="text-align:right;"><?= number_format($row['total_bal'],2)?></td>
				<td style="text-align:right;color:red;"><?= number_format($row['total_undelivered'],2) ?></td>
				<td style="text-align:right;"><?= number_format($row['total_bal']-$row['total_undelivered'],2) ?></td>
			</tr>
			<? $total['total_bal']+=$row['total_bal'];$total['total_undelivered']+=$row['total_undelivered'];} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">Sub Total</th>
				<th><?=number_format($total['total_bal'],2)?></th>
				<th><?=number_format($total['total_undelivered'],2)?></th>
				<th><?=number_format($total['total_bal']-$total['total_undelivered'],2)?></th>
			</tr>
		</tfoot>
	</table>
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