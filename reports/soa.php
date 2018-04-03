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
			background-image: url('../images/emall_logo.jpg');
			background-size: 150px 130px;
			background-repeat: no-repeat;
			background-position: left center;
			
		}
		.logo_landtraders {
			background-image: url('../images/landtraders.jpg');
			background-size: 276px 120px;
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
$qry = $con->Nection()->query("select a.*,b.* from tbl_soa a left join tbl_customers b on a.cust_id=b.cust_id where a.id={$_REQUEST['refid']} limit 1");
// $sql = "select a.*,b.* from tbl_soa a left join tbl_customers b on a.cust_id=b.cust_id where a.id={$_REQUEST['refid']} limit 1";
// $qry=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
?>
<body style="margin:0 auto 0;width:900px;font-size:12px;">
		<div class="logo" style="text-align:center;width:100%;">
			<h1>Cebu Central Realty Corporation</h1>
			<span style="font-size:12px;">N. Bacalso cor. Leon Kilat Sts., Cebu City<br/>TIN : 005-255-946-000</span>
			<h2>STATEMENT OF ACCOUNT</h2>
		</div>
		<!--div class="logo_landtraders" style="text-align:center;width:100%;">
			<h1>Landtraders</h1>
			<span style="font-size:12px;">J Kyle Bldg, Gen Maxilom Ave Ext. Cor. J. De Veyra Ave, Cebu City<br/>TIN : 000-000-000-000</span>
			<h2>STATEMENT OF ACCOUNT</h2>
		</div-->
		<div style="clear:both;height:5px;"></div>
		<?php
		while($row = $qry->fetch_assoc()){
			echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
			echo "<tr>
					<td style='width:15%;'></td>
					<td style='width:50%;'></td>
					<td style='width:15%;'>Contract Start Date</td>
					<td style='text-align:left;width:20%;'>{$row['contract_start_date']}</td>
				</tr>";
			echo "<tr>
					<td></td>
					<td></td>
					<td>Contract End Date</td>
					<td style='text-align:left;'>{$row['contract_end_date']}</td>
				</tr>";
			echo "<tr>
					<td>Business Name:</td>
					<td style='text-align:left;'>{$row['customer_name']}</td>
					<td>SOA Number:</td>
					<td style='text-align:left;'>{$row['id']}</td>
				</tr>";
			echo "<tr>
					<td>Address:</td>
					<td style='text-align:left;'>{$row['customer_address']}</td>
					<td>Date Issued:</td>
					<td style='text-align:left;'>{$row['date']}</td>
				</tr>";
			echo "<tr>
					<td>Nature of Business:</td>
					<td style='text-align:left;'>{$row['nature_of_business']}</td>
					<td>Floor Area (sqm):</td>
					<td style='text-align:left;'>{$row['floor_area_sqm']}</td>
				</tr>";
			echo "<tr>
					<td>Unit Number:</td>
					<td style='text-align:left;'>{$row['mall_unit_number']}</td>
					<td>Fixed Rental/sqm:</td>
					<td style='text-align:left;'>{$row['fixed_rental_per_sqm']}</td>
				</tr>";
			echo "<tr>
					<td>TIN:</td>
					<td style='text-align:left;'>{$row['tin']}</td>
					<td>Due Date:</td>
					<td style='text-align:left;'>{$row['due_date']}</td>
				</tr>";
			echo '</table>';
			echo "<hr/>";

			$prev = $con->resultArray($con->Nection()->query("select * from tbl_soa_prev_bal where cust_id='{$row['cust_id']}' and year='{$row['year']}' and month='{$row['month']}' order by id asc"));
			echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
			(double)$prev_total=0;
			foreach($prev as $key => $val){
				if($key>=1){
					echo "<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;{$val['details']}</td>
						<td>&nbsp;</td>
						<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
					</tr>";
				}else{
					echo "<tr>
						<td>{$val['details']}</td>
						<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
						<td>&nbsp;</td>
					</tr>";
				}
				if($con->strposa($val['details'], array('Payment Received')) !== false){
					$prev_total = $prev_total - (double)$val['amount'];
				}else{
					$prev_total = $prev_total + (double)$val['amount'];
				}
			}
			echo "<tr>
					<td>Outstanding Balance</td>
					<td>&nbsp;</td>
					<td style='text-align:right;border-top:1px solid black;'>".number_format($prev_total,2)."</td>
				</tr>";
			echo '</table>';
			
			$cc = $con->resultArray($con->Nection()->query("select * from tbl_soa_current_charges where cust_id='{$row['cust_id']}' and year='{$row['year']}' and month='{$row['month']}' order by id asc"));
			echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
			(double)$cc_total=0;
			foreach($cc as $key => $val){
				echo "<tr>
						<td>{$val['details']}</td>
						<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
						<td>&nbsp;</td>
					</tr>";
				if($key>=2){
					if($con->strposa($val['details'], array('Less')) !== false){
						$cc_total = $cc_total - (double)$val['amount'];
					}else{
						$cc_total = $cc_total + (double)$val['amount'];
					}
				}
			}
			echo "<tr>
					<td style='font-weight:bold;'>Net Rental Due After Tax</td>
					<td style='border-top:1px solid black;'>&nbsp;</td>
					<td style='text-align:right;font-weight:bold;'>".number_format($cc_total,2)."</td>
				</tr>";
			echo '</table>';
			
			$rc = $con->resultArray($con->Nection()->query("select * from tbl_soa_reimbursable_charges where cust_id='{$row['cust_id']}' and year='{$row['year']}' and month='{$row['month']}' order by id asc"));
			echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
			(double)$rc_total=0;
			echo "<tr>
					<td style='text-decoration: underline;font-weight:bold;'>Reimbursable Charges</td>
					<td style='text-decoration:underline;'>Previous</td>
					<td style='text-decoration:underline;'>Present</td>
					<td style='text-decoration:underline;'>Reading/Area</td>
					<td style='text-decoration:underline;'>Rate</td>
					<td style='text-align:right;text-decoration:underline;'>Amount</td>
					<td></td>
				</tr>";
			foreach($rc as $key => $val){
				echo "<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;{$val['details']}</td>
						<td>{$val['previous']}</td>
						<td>{$val['present']}</td>
						<td>{$val['reading_area']}</td>
						<td>{$val['rate']}</td>
						<td style='text-align:right;'>".number_format($val['amount_due'],2)."</td>
						<td>&nbsp;</td>
					</tr>";
				$rc_total+=(double)$val['amount_due'];
			}
			echo "<tr>
					<td style='font-weight:bold;'>Total Reimbursable Charges</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style='text-align:right;font-weight:bold;'>".number_format($rc_total,2)."</td>
				</tr>";
			echo '</table>';
			$nvc = @$con->resultArray($con->Nection()->query("select * from tbl_soa_nonvat where cust_id='{$row['cust_id']}' and year='{$row['year']}' and month='{$row['month']}' order by id asc"));
			if($nvc){
				(double)$nvc_total=0;
				echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
				echo "<tr>
						<td style='text-decoration: underline;font-weight:bold;'>NON-VAT Charges</td>
						<td style='text-align:right;'>Amount</td>
						<td>&nbsp;</td>
					</tr>";
				foreach($nvc as $key => $val){
					echo "<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;{$val['details']}</td>
							<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
							<td>&nbsp;</td>
						</tr>";
					$nvc_total+=(double)$val['amount'];
				}
				echo "<tr>
						<td style='font-weight:bold;'>Total NON-VAT Charges</td>
						<td>&nbsp;</td>
						<td style='text-align:right;font-weight:bold;'>".number_format($nvc_total,2)."</td>
					</tr>";
				echo '</table>';
			}
			$oc = @$con->resultArray($con->Nection()->query("select * from tbl_soa_other_charges where cust_id='{$row['cust_id']}' and year='{$row['year']}' and month='{$row['month']}' order by id asc"));
			if($oc){
				echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">';
				(double)$oc_total=0;
				echo "<tr>
						<td style='text-decoration: underline;font-weight:bold;'>Other Charges</td>
						<td style='text-align:right;'>Amount</td>
						<td>&nbsp;</td>
					</tr>";
				foreach($oc as $key => $val){
					echo "<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;{$val['details']}</td>
							<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
							<td>&nbsp;</td>
						</tr>";
					$oc_total+=(double)$val['amount'];
				}
				echo "<tr>
						<td style='font-weight:bold;'>Total Other Charges</td>
						<td>&nbsp;</td>
						<td style='text-align:right;font-weight:bold;'>".number_format($oc_total,2)."</td>
					</tr>";
				echo '</table>';
			}
			echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td>Total Current Charges</td>
						<td style="width:150px;text-align:right;border-top:1px solid black;font-weight:bold;">'.number_format($tcc=$cc_total+$rc_total+$oc_total+$prev_total,2).'</td>
					</tr>
					<tr>
						<td>Total Amount Due</td>
						<td style="width:150px;text-align:right;border-top:1px solid black;">'.number_format($tcc,2).'</td>
					</tr>
					<tr>
						<td>Less: Advance Rental</td>
						<td style="width:150px;text-align:right;">'.number_format(0,2).'</td>
					</tr>
					<tr>
						<td style="">AMOUNT DUE</td>
						<td style="width:150px;text-align:right;border-top:1px solid black;">'.number_format($tcc,2).'</td>
					</tr>';
			$pdc = $con->resultArray($con->Nection()->query("select * from tbl_soa_pdclist where custid='{$row['cust_id']}' and monthrent='".$row['year']."-".$db->customeFormat($row['month'],2)."'"));
			$pdc_total=0;
			foreach($pdc as $key => $val){
			echo '<tr>
						<td style="">PDC - '.$val['bank']." ".$val['checknum']." ".$val['checkdate'].'</td>
						<td style="width:150px;text-align:right;border-top:1px solid black;">'.number_format($val['amount'],2).'</td>
					</tr>';
			$pdc_total+=$val['amount'];
			}
			echo '<tr>
						<td style="font-weight: bold;">NET AMOUNT DUE AFTER PDC</td>
						<td style="font-weight: bold;width:150px;text-align:right;border-top:1px solid black;border-bottom:double black;">'.number_format($tcc-$pdc_total,2).'</td>
					</tr>
				</table>';
			echo '<br/><br/><table class="tbl" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>Prepared By:</td>
					<td>Certified Correct By:</td>
					<td>Noted By:</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
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
				<tr>
					<td colspan="3">*Presentation of this statement is sufficient notice that account is due.</td>
				</tr>
				<tr>
					<td colspan="3">*Please make all payments in check payable to <b>CEBU CENTRAL REALTY CORPORATION.</b></td>
				</tr>
				<tr>
					<td colspan="3">*Payments made after the 25th day of the month are not reflected in this statement.</td>
				</tr>
				<tr>
					<td colspan="3"><b>*5% penalty</b> would be charged for all arrears/unpaid charges after the 7th day of the billing month.</td>
				</tr>
				<tr>
					<td colspan="3">*Please issue Certificate of Creditable Tax Withheld at Source <b>(BIR Form 2307)</b> regularly on a monthly basis. Unless said BIR Form is submitted to Cebu Central Corp., taxes shall not be recognized as credit and therefore, shall be collected from merchant.</td>
				</tr>
				</table>';
		}
		?>
</body>
<script>
$(document).ready(function() {
	
});


</script>
</html>