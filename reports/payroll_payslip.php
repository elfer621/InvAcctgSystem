<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<body>
<style type="text/css">

table.tbl,table.tbl2 {
		border-width: 0px;
		border-spacing: 0px;
		border-style: solid;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		font-size:15px;
		
	}
	table.tbl th,table.tbl2 th {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		height:20px;
		text-align:center;
		font-size:15px;
		padding:0 3px 0 3px;
	}
	table.tbl td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:15px;
		padding:0 3px 0 3px;
		text-align:right;
	}
	table.tbl2 td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:15px;
		font-size:15px;
		padding:0 3px 0 3px;
		text-align:right;
	}
	.lbl{
		font-size:17px;
	}
@page {
	margin: 0 20px 0 20px;
	font-size:17px !important;
}
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
	.per_slip{
		height:700px;
		border-bottom:1px solid gray;
		padding-top:80px;
		font-size:17px!important;
	}
	.brk {
		page-break-after:always;
	}
}
</style>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$db->openDb();
$header = $db->getWHERE("*","tbl_payroll_header","where id='{$_REQUEST['refid']}'");
$empid=$_REQUEST['empids']?"and empid in ({$_REQUEST['empids']})":"";
$entries = $db->resultArray("*","tbl_payroll_entry","where refid='{$_REQUEST['refid']}' $empid order by name asc");
?>
<div style="width:1100px;font-size:15px;">
	<?php 
	$i=1;
	foreach($entries as $key => $val){
		if($i==3)$i=1;
		$emp = $db->getWHERE("*","tbl_employee","where id='{$val['empid']}'");
		$dtr = $db->resultArray("*","tbl_payroll_dtr","where empid='{$val['empid']}' and (date between '{$header['begdate']}' and '{$header['enddate']}')");
		$advances = $db->resultArray("*,sum(amount) as total","tbl_payroll_advances","where empid='{$val['empid']}' group by details");
		$deduction = $db->resultArray("*,sum(amount) as total","tbl_payroll_deduction","where empid='{$val['empid']}' and payrollid='{$_REQUEST['refid']}' group by details");
		foreach($advances as $k => $v){
			$adv[$v['details']]=$v['total'];
		}
	?>
	<div class="per_slip">
		<h3><?=$db->stockin_header;?><br/><span style="font-size:15px;">Pay Slip</span></h3>
		<div style="float:left;width:60%;">
			<div style="width:150px;float:left;margin-right:10px;">Emp Name:</div>
			<div style="float:left;width:350px;text-align:right;font-size:20px;"><?="[".$val['empid']."] ".$val['name']?></div>
			<div style="clear:both;height:5px;"></div>
			<div style="width:150px;float:left;margin-right:10px;">Daily Rate:</div>
			<div style="float:left;width:350px;text-align:right;font-size:20px;"><?=number_format($val['dailyrate'],2)?></div>
			<div style="clear:both;height:5px;"></div>
			<div style="width:150px;float:left;margin-right:10px;">TIN:</div>
			<div style="float:left;width:350px;text-align:right;font-size:20px;"><?=$emp['tin_number']?></div>
			<div style="clear:both;height:5px;"></div>
			<div style="width:150px;float:left;margin-right:10px;">SSS #:</div>
			<div style="float:left;width:350px;text-align:right;font-size:20px;"><?=$emp['sss_number']?></div>
			<div style="clear:both;height:5px;"></div>
		</div>
		<fieldset style="float:right;width:30%;">
			<legend>Payrol Period</legend>
			<?=$header['begdate']." - ".$header['enddate']?>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<div style="width:45%;float:left;">
			<fieldset style="width:100%;float:left;">
				<legend>DTR</legend>
				<table id="mytbl" class="tbl2" style="width:100%;">
					<tr>
						<td>Date</td>
						<td>Type</td>
						<td>Status</td>
						<td>RegHrs</td>
						<td>Reg Pay</td>
						<td>OT Hours</td>
						<td>OT Amount</td>
						<td>Add</td>
						<td>Total</td>
					</tr>
					<?php 
					$total['reghours']=0;
					$total['regtotal']=0;
					$total['othours']=0;
					$total['otamt']=0;
					$total['subtotal']=0;
					foreach($dtr as $k => $v){ ?>
						<tr>
							<td style="font-size:12px;"><?=date('M d',strtotime($v['date']))?></td>
							<td style="font-size:10px;"><?=$v['type']?></td>
							<td style="font-size:10px;"><?=$v['status']?></td>
							<td><?=$v['reghours']?></td>
							<td><?=number_format($v['regtotal'],2)?></td>
							<td><?=$v['othours']?></td>
							<td><?=number_format($v['otamt'],2)?></td>
							<td><?=number_format($v['add'],2)//" <span style='font-size:10px;'>".$v['add_remarks']."</span>"?></td>
							<td><?=number_format($v['subtotal'],2)?></td>
						</tr>
					<?php 
						$total['reghours']+=$v['reghours'];
						$total['regtotal']+=$v['regtotal'];
						$total['othours']+=$v['othours'];
						$total['otamt']+=$v['otamt'];
						$total['add']+=$v['add'];
						$total['subtotal']+=$v['subtotal'];
					} ?>
					<tr>
						<td colspan="3">Total</td>
						<td><?=number_format($total['reghours'],2)?></td>
						<td><?=number_format($total['regtotal'],2)?></td>
						<td><?=number_format($total['othours'],2)?></td>
						<td><?=number_format($total['otamt'],2)?></td>
						<td><?=number_format($total['add'],2)?></td>
						<td><?=number_format($total['subtotal'],2)?></td>
					</tr>
				</table>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<?php if($val['adjustment']!=0){ ?>
			<fieldset style="width:100%;float:left;">
				<legend>Adjustment</legend>
				<?php 
					$adj = $db->resultArray("*","tbl_payroll_adjustment","where empid='{$val['empid']}' and payrollid='{$header['id']}'");
					foreach($adj as $a=>$b){
						echo "<div style='float:left;width:45%;margin-right:10px;'>".$b['details'].($b['rem']?"<span style='font-size:10px;'> (".$b['rem'].") </span>":"")." -> <span style='float:right;'>".number_format($b['amount'],2)."</span></div>";
					}
				?>
			</fieldset>
			<?php } ?>
			<?php if($val['others']!=0){ ?>
			<fieldset style="width:100%;float:left;">
				<legend>Others Deduction Details</legend>
				<?php 
					$others = $db->resultArray("*","tbl_payroll_deduction","where empid='{$val['empid']}' and payrollid='{$header['id']}'");
					foreach($others as $a=>$b){
						echo "<div style='float:left;width:45%;margin-right:10px;'>".$b['details'].($b['rem']?"<span style='font-size:10px;'> (".$b['rem'].") </span>":"")." -> <span style='float:right;'>".number_format($b['amount'],2)."</span></div>";
					}
				?>
			</fieldset>
			<?php } ?>
		</div>
		<div style="width:43%;float:right;">
			<fieldset style="width:100%;float:right;">
				<legend>Summary</legend>
				<div style="float:left;margin-right:15px;">
					<div style="width:100px;float:left;margin-right:10px;">Reg Pay:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=number_format($val['regtime'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">OT Hours:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=$val['othours']?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">OT Amount:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=number_format($val['otamt'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Add:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=number_format($val['added_amt'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Gross Total:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;border-top:1px solid gray;"><?=number_format($val['gross_total'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Adjustment:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=number_format($val['adjustment'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Sub Total:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;border-top:1px solid gray;"><?=number_format($val['gross_total']+$val['adjustment'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Less Deduction:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;"><?=number_format($val['lesstotal'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:100px;float:left;margin-right:10px;">Net Salary:</div>
					<div class="lbl" style="float:left;width:100px;text-align:right;border-top:1px solid gray;"><b><?=number_format($val['nettotal'],2)?></b></div>
				</div>
				<div style="float:right;">
					<div style="width:80px;float:left;margin-right:10px;">Deduction:</div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:80px;float:left;margin-right:10px;">SSS:</div>
					<div class="lbl" style="float:left;width:80px;text-align:right;"><?=number_format($val['sss'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:80px;float:left;margin-right:10px;">PhilHealth:</div>
					<div class="lbl" style="float:left;width:80px;text-align:right;"><?=number_format($val['philhealth'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:80px;float:left;margin-right:10px;">Pag-Ibig:</div>
					<div class="lbl" style="float:left;width:80px;text-align:right;"><?=number_format($val['pagibig'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:80px;float:left;margin-right:10px;">Others:</div>
					<div class="lbl" style="float:left;width:80px;text-align:right;"><?=number_format($val['others'],2)?></div>
					<div style="clear:both;height:3px;"></div>
					<div style="width:80px;float:left;margin-right:10px;">Total:</div>
					<div class="lbl" style="float:left;width:80px;text-align:right;border-top:1px solid gray;"><?=number_format($val['lesstotal'],2)?></div>
				</div>
			</fieldset>
			
			<?php /*if($deduction){ ?>
			<fieldset style="width:100%;float:right;">
				<legend>Advances Balance</legend>
				<table class="tbl" style='width:100%;'>
					<thead>
						<tr>
							<td>Remarks</td>
							<td style='width:100px;'>Amount</td>
							<td style='width:100px;'>Deduction</td>
							<td style='width:100px;'>Balance</td>
						</tr>
					</thead>
					<tbody>
					<?php foreach($deduction as $key => $x){?>
						<tr>
							<td style="font-size:12px;"><?=$x['details']?></td>
							<td><?=number_format($adv[trim($x['details'])],2)?></td>
							<td><?=number_format($x['total'],2)?></td>
							<td><?=$adv[trim($x['details'])]==0?'':number_format($adv[trim($x['details'])]-$x['total'],2)?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</fieldset>
			<?php }*/ ?>
		</div>
		<div style="float:right;margin-right:10px;width:100%;text-align:left;font-size:16px!important;">
			<p>I hereby acknowledge to have received the sum of P <?=number_format($val['nettotal'],2)?> as 
			full payment for my service for the period covered <?=$header['begdate']." to ".$header['enddate']?>.</p>
			<div style="clear:both;height:10px;"></div>
			<p>__________________________________</p>
			<div style="clear:both;"></div>
			<div style="font-size:8px;width:200px;text-align:center;margin-top:-10px;">SIGNATURE OVER PRINTED NAME</div>
		</div>
	</div>
	<div style="clear:both;height:5px;"></div>
	<?php if($i==2){echo "<div class='brk'></div>";}
	$i++;} ?>
</div>
</body>
</html>