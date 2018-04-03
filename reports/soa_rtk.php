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
		.logo {
			background-image: url('../images/rtklogo.png');
			background-size: 150px 70px;
			background-repeat: no-repeat;
			background-position: left center;
			
		}
		@media print {
			* {-webkit-print-color-adjust:exact;}
		}
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();

$sql = "select a.*,b.date_delivered from tbl_customers_trans a
		left join tbl_customers_trans_delivery b on a.receipt=b.receipt and a.cust_id=b.cust_id 
		where a.cust_id='{$_REQUEST['acctid']}' order by a.date desc";
		
$qry = $con->Nection()->query($sql);
		
?>
<body style="margin:0 auto 0;width:900px;font-size:12px;">
		<div class="logo" style="text-align:center;width:100%;">
			<h1>Cebu RTK Marketing, Inc.</h1>
			<span style="font-size:12px;">1-62 A. Flordeliz St. Jude Acres, Bulacao, Cebu City<br/>
			Tel. Nos.: 273-7441/273-7442, Fax: 272-0061 (Cebu)<br/>
			Tel. Nos.: 221-8423 (Davao)<br/>
			TIN : 000-552-122-000</span>
			<h2>STATEMENT OF ACCOUNT</h2>
		</div>
		
		<div style="clear:both;height:5px;"></div>
		<?php
		echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%" border="1" style="font-size:12px;">';
		echo '<tr>
				<th>ReceiptDate</th>
				<th>Details</th>
				<th>Amount</th>
				<th>DateDelivered</th>
				'.($_SESSION['settings']['system_name']=='Rber System'?'
				<th>Paid Date</th>
				<th>Payment Details</th>
				<th>Paid Amount</th>
				<th>W/Vat</th>
				<th>Menu</th>':'').'
			</tr>';
		while($row = $qry->fetch_assoc()){
			
			if($row['transtype']=='Payment' or $row['transtype']=='Credit Memo'){
				$color = 'style="color:red;"';
			}else if($row['date_delivered']=='' and $row['transtype']!="Adjustment"){
				$color = 'style="color:green;"';
			}else{
				$color = '';
			}
			$delbtn = $_SESSION['restrictionid']==1?'<img src="./images/del.png" title="Delete Records" onclick="delCustRec('.$row['id'].')"/>':'';
			$paidbtn = '<img src="./images/cashdetails.png" title="Paid" style="width:20px;height:20px;float:left;" onclick="setPaid('.$row['id'].')"/>';
			echo '<tr>
					<td '.$color.'>'.($row['date']=="0000-00-00 00:00:00"?"":date('Y-m-d',strtotime($row['date']))).'</td>
					<td '.$color.'>'.$row['details'].'</td>
					<td '.$color.' align="right">'.number_format($row['amount'],2).'</td>
					<td>'.$row['date_delivered'].'</td>'.($_SESSION['settings']['system_name']=='Rber System'?
					'<td>'.($row['paid_date']=='0000-00-00'?'':$row['paid_date']).'</td>
					<td>'.$row['paid_details'].'</td>
					<td align="right">'.number_format($row['paid_amount'],2).'</td>
					<td align="right">'.number_format($row['paid_wvat'],2).'</td>
					<td>'.$paidbtn.'</td>':'').'
				</tr>';
		}
		echo '</table>';
		?>
</body>
<script>
$(document).ready(function() {
	
});


</script>
</html>