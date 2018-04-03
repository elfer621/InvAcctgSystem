<style type="text/css">
table.tbl {
	border-width: 0px;
	border-spacing: 0px;
	border-style: none;
	border-collapse: collapse;
	font-family:Calibri,Arial, Verdana, Geneva, Helvetica, Sans-Serif;
}
table.tbl th {
	border-width: 1px;
	border-style: none;
	border-color: gray;
	height:20px;
	text-align:center;
	font-size:15px;
}
table.tbl td {
	font-size:15px;
	border-width: 1px;
	border-style: none;
	border-color: gray;
	background-color: white;
	height:20px;
}
.lbl{
	float:left;margin-right:10px;width:120px;
}
</style>
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$db->openDb();
?>
<div style="width:300px;font-family:Calibri,Arial, Verdana, Geneva, Helvetica, Sans-Serif;">
	<div style="text-align:center;width:100%;">
	<?php echo $receipt_header; ?>
	</div>
	<h3 style="text-align:left;"><?=$_REQUEST['all']?"Z-READING":"X-READING"?></h3>
	<h3 style="text-align:center;"><?=$_REQUEST['all']?"SERVER READING":"CASHIER ACCOUNTABILITY"?></h3>
	<?php
	
	if($_REQUEST['all']){
		$all = $db->resultArray("*","tbl_reading","where reading_num='".$_REQUEST['readingnum']."'");
	}else{
		$all[0]['reading_num'] = $_REQUEST['readingnum'];
		$all[0]['counter'] = $_REQUEST['counter_num']?$_REQUEST['counter_num']:$_SESSION['counter_num'];
	}
	
	$grand_total=0;$grand_vat=0;
	foreach($all as $k=>$v){
		$reading = $v['reading_num'];
		$counter = $v['counter'];
		$info=$db->getWHERE("*","tbl_reading","where reading_num='$reading' and counter='$counter'");
		$trans = $db->getWHERE("max(receipt_id) mxid,min(receipt_id) minid,sum(amount-vat) as total,sum(vat) as total_vat,min(`timestamp`) as start_trans,max(`timestamp`) as end_trans",
			"tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."'");
		//print_r($trans);
		?>
		<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
				<tr>
					<td>Station:</td>
					<td style="text-align:right;"><?=strtoupper($_SESSION['connect'])."-ST0".$counter?></td>
				</tr>
				<tr>
					<td>TransStarted:</td>
					<td style="text-align:right;"><?=date('Y-m-d',strtotime($trans['start_trans']))."&nbsp;&nbsp;&nbsp;&nbsp;".date('H:m:s',strtotime($info['start_time']))?></td>
				</tr>
				<tr>
					<td>TransEnded:</td>
					<td style="text-align:right;"><?=date('Y-m-d',strtotime($trans['end_trans']))."&nbsp;&nbsp;&nbsp;&nbsp;".date('H:m:s',strtotime($info['end_time']))?></td>
				</tr>
				<tr>
					<td>Start OR.#</td>
					<td style="text-align:right;"><?=$db->customeFormat($trans['minid'],9)?></td>
				</tr>
				<tr>
					<td>End OR.#</td>
					<td style="text-align:right;"><?=$db->customeFormat($trans['mxid'],9)?></td>
				</tr>
				<tr>
					<td>Cashier Name</td>
					<td style="text-align:right;"><?=$info['cashier_name']?></td>
				</tr>
		</table>
		<hr/>
		<?php
		//$cashnum=0;$vouchernum=0;$cash=0;$voucher=0;$voucher_bal=0;$refund=0;$refundnum=0;$cash_vat=0;$voucher_vat=0;
		$cash=0;
		$totals = $db->resultArray("cat.category_name,sum(a.amount) as total_sales,a.payment,count(*) num,sum(b.amount) total_voucher,sum(a.vat) vat_sales",
		"tbl_sales_receipt_$counter a left join (select * from tbl_voucherpayment_details where reading='$reading' and counter='$counter') b on a.receipt_id=b.receipt 
		left join tbl_category cat on a.category_id=cat.category_id",
		"where a.reading='".$reading."' and a.counter_num='".$counter."' and payment='CASH' group by a.category_id");
		
		$voidCash = $db->getWHERE("sum(amount) as total,sum(vat) as total_vat,count(*) num,group_concat(receipt_id) as ids,payment","tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."' and type='VOID' AND payment='CASH'");
		$voidVoucher = $db->getWHERE("sum(amount) as total,sum(vat) as total_vat,count(*) num,group_concat(receipt_id) as ids,payment","tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."' and type='VOID' AND payment='VOUCHER'");
		
		$xrefund = $db->getWHERE("sum(amount) as total,count(*) num","tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."' and payment='REFUND'");
		$xvoucher = $db->getWHERE("sum(amount) as total,count(*) num","tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."' and payment='VOUCHER' and type!='VOID'");
		$splitpayment = $db->resultArray("sum(payment_amt) as total,count(*) num,payment_type","tbl_sales_splitpayment","where counter='".$counter."' and reading='".$reading."' ".($void['ids']?"and receipt_id not in ({$void['ids']})":"")." group by payment_type order by payment_type asc");
		//print_r($void);
		echo '<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >';
		echo '<tr>
					<td colspan="3">CASH TRANSACTION</td>
					<td style="text-align:right;"></td>
				</tr>';
		foreach($totals as $x => $y){
			if($y['category_name']=="BOOKS"){
				echo '<tr>
						<td>&nbsp;&nbsp;</td>
						<td>'.$y['category_name'].'</td>
						<td style="text-align:right;">'.number_format($y['total_sales']+$splitpayment[0]['total'],2).'</td>
						<td>&nbsp;</td>
					</tr>';
			}else{
				echo '<tr>
						<td>&nbsp;&nbsp;</td>
						<td>'.$y['category_name'].'</td>
						<td style="text-align:right;">'.number_format($y['total_sales'],2).'</td>
						<td>&nbsp;</td>
					</tr>';
			}
			$cash+=$y['total_sales'];
		}
		echo '<tr>
					<td>&nbsp;&nbsp;</td>
					<td>REFUND '."[Tx: {$xrefund['num']}]".'</td>
					<td style="text-align:right;">('.number_format($xrefund['total']*-1,2).')</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td>VOID '."[Tx: ".($voidCash['num'])."]".'</td>
					<td style="text-align:right;">('.number_format($voidCash['total'],2).')</td>
				</tr>
				<tr>
					<td colspan="2">TOTAL CASH</td>
					<td style="text-align:right;border-top:1px solid #000;">&nbsp;</td>
					<td style="text-align:right;">'.number_format($total_cash=($cash+$splitpayment[0]['total'])-(($xrefund['total']*-1)+$voidCash['total']),2).'</td>
				</tr>
				
				<tr>
					<td colspan="2">VOUCHER TRANSACTION </td>
					<td style="text-align:right;">'.number_format(($xvoucher['total']+$splitpayment[1]['total'])+$voidVoucher['total'],2).'</td>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td>VOID '."[Tx: ".($voidVoucher['num'])."]".'</td>
					<td style="text-align:right;">('.number_format($voidVoucher['total'],2).')</td>
				</tr>
				<tr>
					<td colspan="2">TOTAL VOUCHER '."[Tx: {$xvoucher['num']}]".'</td>
					<td style="text-align:right;border-top:1px solid #000;">&nbsp;</td>
					<td style="text-align:right;">'.number_format($total_voucher=($xvoucher['total']+$splitpayment[1]['total']),2).'</td>
				</tr>';
		echo '<tr style="border-top:1px solid #000;border-bottom:1px solid #000;padding:5px;">
					<td colspan="3">TOTAL SALES '.'ST0'.$counter.'</td>
					<td style="text-align:right;">'.number_format($total_cash+$total_voucher,2).'</td>
				</tr>';
		echo '</table>';
		$grand_total+=($trans['total']-($voidCash['total']-$voidCash['total_vat']));
		$grand_vat+=($trans['total_vat']-$voidCash['total_vat']);
	} ?>
	
	<?php if($_REQUEST['all']){ ?>
		<div style="width:100%;">
			GRAND TOTAL SALES: <span style="float:right;"><?=number_format($grand_total+$grand_vat,2)?></span>
		</div>
		<hr/>
	<?php } ?>
	<div style="clear:both;height:10px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
			<tr>
				<td>VAT-EXEMPT SALES</td>
				<td><?=number_format($grand_total,2)?></td>
			</tr>
			<tr>
				<td>VAT SALES</td>
				<td><?=number_format($grand_vat-($grand_vat/9.333),2)?></td>
			</tr>
			<tr>
				<td>VAT AMOUNT</td>
				<td><?=number_format(($grand_vat)/9.333,2)?></td>
			</tr>
			
	</table>
	<div style="clear:both;height:30px;"></div>
	<?
	if($_REQUEST['all']){
		$qry=mysql_query("select * from tbl_cashdetails where reading='".$db->getServerReadingnum()."'");
	}else{
		$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$counter."' and reading='".$reading."'");
	}
	?>
	<?php if(!$_REQUEST['all']){ ?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th colspan="3" style="text-align:center;">CASH COUNT</th>
		</tr>
		<tr>
			<th>Denomination</th>
			<th>Count</th>
			<th>Total</th>
		</tr>
	<?$cdtotal=0;while($row=mysql_fetch_assoc($qry)){?>
		<tr>
			<td align="center"><?=$row['money']?></td>
			<td align="center"><?=$row['count']?></td>
			<td align="center"><?=number_format($row['total'],2)?></td>
		</tr>
	<?$cdtotal+=$row['total'];}?>
		<tr>
			<td colspan="2" align="left">Total Cash</td>
			<td align="right" style="border-top:1px solid black;"><?=number_format($cdtotal,2)?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Voucher</td>
			<td align="right" style="border-bottom:1px solid black;"><?=number_format($total_voucher,2)?></td>
		</tr>
		<?php $so = $total_cash-$cdtotal;?>
		<tr>
			<td colspan="2" align="left"><?=$so>0?"Short":"Over"?></td>
			<td align="right" style="border-bottom:1px solid black;"><?=number_format($so,2)?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Total</td>
			<td align="right" style="border-bottom:1px solid black;"><?=number_format($cdtotal+$total_voucher+$so,2)?></td>
		</tr>
	</table>
	<div style="clear:both;height:10px;"></div>
	<?php } ?>
	<div id="prodsold"></div>
	<?php
	if(!$_REQUEST['all']){ //journal entry...
		$info_r = $db->getWHERE("*","tbl_reading","where reading_num='".$reading."' and counter='".$counter."'");
		$glref = isset($info_r['glref'])&&$info_r['glref']!=0?$info_r['glref']:$con->getNextJournalID();
		$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`) values 
			('".$glref."','{$_SESSION['connect']}','".$info_r['end_date']."','General Ledger', 
			'".strtoupper($_SESSION['connect']).":TO RECORD SALES COUNTER $counter','".($total_cash+$total_voucher)."', 
			'".$_SESSION['xid']."','ForApproval') 
			on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
		switch($_SESSION['connect']){
			case'uclm':
				$entry[]=array('code'=>'1014','desc'=>'CASH - UC LM','dr_amt'=>$total_cash+$total_voucher,'cr_amt'=>'','center'=>"{$_SESSION['connect']}");
			break;
			case'ucmambaling':
				$entry[]=array('code'=>'1013','desc'=>'CASH - UC METC','dr_amt'=>$total_cash+$total_voucher,'cr_amt'=>'','center'=>"{$_SESSION['connect']}");
			break;
			case'ucmain':
				$entry[]=array('code'=>'1012','desc'=>'CASH - UC MAIN','dr_amt'=>$total_cash+$total_voucher,'cr_amt'=>'','center'=>"{$_SESSION['connect']}");
			break;
			case'ucbanilad':
				$entry[]=array('code'=>'1011','desc'=>'CASH - UC BANILAD','dr_amt'=>$total_cash+$total_voucher,'cr_amt'=>'','center'=>"{$_SESSION['connect']}");
			break;
		}
		$entry[]=array('code'=>'4000','desc'=>'SALES','dr_amt'=>'','cr_amt'=>$total_cash+$total_voucher,'center'=>"{$_SESSION['connect']}");
		//Journal Entry for Inventory
		$entry[]=array('code'=>'5000','desc'=>'COST OF SALES - BOOKS','dr_amt'=>$total_cash+$total_voucher,'cr_amt'=>'','center'=>"{$_SESSION['connect']}");
		$entry[]=array('code'=>'1400','desc'=>'INVENTORY - BOOKS','dr_amt'=>'','cr_amt'=>$total_cash+$total_voucher,'center'=>"{$_SESSION['connect']}");
		//Journal Entry for Inventory
		$glid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry);
		$update = mysql_query("update tbl_reading set glref='".$glref."' where reading_num='".$reading."' and counter='".$counter."'");
	}
	?>
</div>
<script>
onload=function(){
	var print_productsold = '<?= $print_productsold ?>';
	if(print_productsold==true){
		productSold();
	}
}
function productSold(){
	var reading = '<?= $reading ?>';
	var counter = '<?= $counter ?>';
	$.ajax({
		url: './sales_reports_sm.php?rep=perreading&reading='+reading+'&counter='+counter,
		type:"POST",
		success:function(data){
			$("#prodsold").html(data);
		}
	});
}
</script>