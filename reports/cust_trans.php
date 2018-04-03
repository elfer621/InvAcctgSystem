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
$db=new dbConnect();
$db->openDb();
if($_POST){
	if(!$_POST['search_date']){
		$sql="insert into tbl_customers_trans_delivery set 
			date_delivered='{$_REQUEST['date_delivered']}',
			receipt='{$_REQUEST['receipt']}',
			cust_id='{$_REQUEST['cust_id']}'";
		$qry=mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
	}
}
switch($_REQUEST['rep_type']){
	
	case 'undelivered_receipt':
	$sql = "select tbl_main.*,c.date_delivered from 
		(select a.*,b.customer_name,date_format(a.date,'%Y-%m-%d') as xdate from tbl_customers_trans a 
		left join tbl_customers b on a.cust_id=b.cust_id where a.transtype!='Payment' and a.transtype!='Adjustment' and a.transtype!='Credit Memo') as tbl_main 
		left join tbl_customers_trans_delivery c on tbl_main.cust_id=c.cust_id and tbl_main.receipt=c.receipt where c.date_delivered is null";
	$title="Undelivered Receipt";
	break;
	default:
	$xdate = $_REQUEST['my_date']?"'".$_REQUEST['my_date']."'":'current_date()';
	$sql = "select *,date_format(a.date,'%Y-%m-%d') as xdate from tbl_customers_trans a
		left join tbl_customers b on a.cust_id=b.cust_id
		where date_format(a.date,'%Y-%m-%d')=$xdate and (a.transtype='Payment' or a.transtype='Credit Memo')";
	$title="Payment/Credit Memo";
	break;
}
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:1200px;font-size:11px;">
	<h2>Customer <?=$title?> Report</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Date</div>
		<input style="float:left;" type="text" id="my_date" name="my_date" value="<?=$_REQUEST['my_date']?$_REQUEST['my_date']:date('Y-m-d')?>"/>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" style="margin-left:20px;">
		<tr>
			<th>Date</th>
			<th>Status</th>
			<th>Customer Name</th>
			<th>Details</th>
			<th>Amount</th>
			<th>Balance</th>
			<?php if($_REQUEST['rep_type']=="undelivered_receipt"){ ?>
			<th>Date Delivered</th>
			<th>Menu</th>
			<?php } ?>
		</tr>
		<?php while($row=mysql_fetch_assoc($qry)){ 
			$qry_info = mysql_query("select x.*,y.total_undelivered from 
				(
				select *,
				COALESCE(sum(if(transtype='Cash' or transtype='Payment' or transtype='Credit Memo',amount * -1,amount)),0) as total_bal 
				from (
				select tbl_main.*,c.customer_name 
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt) as tbl_main
				left join tbl_customers c on tbl_main.cust_id=c.cust_id) as tbl group by cust_id
				) as x
				left join
				(
				select cust_id,amount,date_delivered,transtype,coalesce(sum(if(date_delivered is null,amount,0)),0) as total_undelivered from (select tbl_main.* 
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt where transtype!='Cash' and transtype!='Payment' and transtype!='Adjustment' and transtype!='Credit Memo') as tbl_main) as tbl group by cust_id
				) as y on x.cust_id=y.cust_id where x.cust_id='{$row['cust_id']}'
				");
			$info = mysql_fetch_assoc($qry_info);
		?>
			<tr>
				<td><?=$row['xdate']?></td>
				<td><?=$row['transtype']?></td>
				<td><?=$row['customer_name']?></td>
				<td><?=$row['details']?></td>
				<td><?=number_format($row['amount'],2)?></td>
				<td style="color:blue;"><?=number_format($info['total_bal']-$info['total_undelivered'],2)?></td>
				<?php if($_REQUEST['rep_type']=="undelivered_receipt"){ ?>
				<td><?=$row['date_delivered']?></td>
				<td><a href="javascript:updateDateDelivered(<?=$row['cust_id']?>,<?=$row['receipt']?>)">Update</a></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</table>
	<div id="dialog"></div>
</body>
<script>
$(document).ready(function() {
	$('#my_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});
function updateDateDelivered(cust_id,receipt){
	$('#dialog').dialog({
		autoOpen: false,
		width: 400,
		height: 100,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:'Update Undelivered Receipt'
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=updateDateDelivered&cust_id='+cust_id+'&receipt='+receipt,async:false});
	$('#dialog').html(htmlobj.responseText);
	$('#dialog').dialog('open');
}

</script>
</html>