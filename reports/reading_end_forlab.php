<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
$settings = $db->getWHERE("*","settings","where variable_name='receipt_header'");
?>
<div style="width:300px;font-family:Calibri,Arial, Verdana, Geneva, Helvetica, Sans-Serif;">
	<div style="text-align:center;width:100%;">
	<?php echo str_replace(array("|permit|","|serial|","|machine|"),array($permit,$serial,$machine),$settings['variable_values']); ?>
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
	
	//$grand_total=0;$grand_vat=0;$grand_nonvat=0;
	foreach($all as $k=>$v){
		$total_cash=0;$total_voucher=0;
		$reading = $v['reading_num'];
		$counter = $v['counter'];
		$info=$db->getWHERE("*","tbl_reading","where reading_num='$reading' and counter='$counter'");
		$persup=$db->resultArray("b.supplier_id,sum(qty*selling) as total_selling,sum(qty*cost) as total_cost","tbl_sales_items a left join tbl_product_name b on a.skuid=b.sku_id","where a.reading='$reading' and a.counter='$counter' group by b.supplier_id");
		$sql = "SELECT 
				max(receipt) mxid,min(receipt) minid,counter,reading,date,type,max(`timestamp`) as end_trans,
				0 void_amount,
				sum(voucher_worth) voucher_worth,
				sum(voucher_amt) voucher_amount,
				sum(voucher_worth) - sum(voucher_amt) voucher_bal,
				
				sum(if(payment='CASH',total_sales,0)) cash_amt,
				sum(if(payment!='CASH',total_sales,0)) charge_amt,
				
				
				sum(total_sales) total_sales,
				sum(total_vat) total_vat,
				(select variable_values from settings where variable_name='session_connect') campus 
			from (select a.*,b.date,b.studentname,b.payment,b.type,
				COALESCE(if(c.amount>d.amount,d.amount,c.amount),0) voucher_amt,
				COALESCE(d.amount,0) voucher_worth
				from (select *,
					sum(qty*selling) total_sales,
					sum(qty*cost) total_cost,
					sum(qty*vat) total_vat 
				from tbl_sales_items where counter='".$counter."' and reading='".$reading."' group by receipt) a 
				left join tbl_sales_receipt_$counter b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading
				left join tbl_voucherdeduction_details c on a.receipt=c.receipt and a.counter=c.counter and a.reading=c.reading 
				left join tbl_voucherpayment_details d on a.receipt=d.receipt and a.counter=d.counter and a.reading=d.reading 
			) tbl 
			group by counter,reading desc
			";
		//echo $sql;
		$con->getBranch2(trim($_SESSION['connect']));
		$res=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
		//generating voids
		$sql="select payment,COALESCE(sum(amount),0) amt from tbl_sales_receipt_$counter where counter_num='".$counter."' and reading='".$reading."' and type='VOID' and payment!='SPLITPAYMENT' group by payment
				union 
			select payment_type,COALESCE(sum(payment_amt),0) amt from tbl_sales_splitpayment where receipt_id in (select receipt_id from tbl_sales_receipt_$counter where counter_num='".$counter."' and reading='".$reading."' and type='VOID' and payment='SPLITPAYMENT') and counter='".$counter."' and reading='".$reading."' group by payment_type";
		$res['voids']=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
		foreach($res['voids'] as $key => $val){
			$res['voids'][strtoupper($val['payment'])]+=$val['amt'];
		}
		// echo "<pre>";
		// print_r($res);
		// echo "</pre>";
		?>
		<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
				<tr>
					<td>Station:</td>
					<td style="text-align:right;"><?=strtoupper($_SESSION['connect'])."-ST0".$counter?></td>
				</tr>
				<tr>
					<td>TransStarted:</td>
					<td style="text-align:right;"><?=date('Y-m-d',strtotime($info['start_date']))."&nbsp;&nbsp;&nbsp;&nbsp;".date('h:i:s A',strtotime($info['start_time']))?></td>
				</tr>
				<tr>
					<td>TransEnded:</td>
					<td style="text-align:right;"><?=date('Y-m-d',strtotime($res[0]['end_trans']))."&nbsp;&nbsp;&nbsp;&nbsp;".($info['end_time']=="00:00:00"?date('h:i:s A'):date('h:i:s A',strtotime($info['end_time'])))?></td>
				</tr>
				<tr>
					<td>Start OR.#</td>
					<td style="text-align:right;"><?=$db->customeFormat($res[0]['minid'],9)?></td>
				</tr>
				<tr>
					<td>End OR.#</td>
					<td style="text-align:right;"><?=$db->customeFormat($res[0]['mxid'],9)?></td>
				</tr>
				<tr>
					<td>Cashier Name</td>
					<td style="text-align:right;"><?=$info['cashier_name']?></td>
				</tr>
		</table>
		<hr/>
		<?php
		//$xrefund = $db->getWHERE("sum(amount) as total,count(*) num","tbl_sales_receipt_$counter","where counter_num='".$counter."' and reading='".$reading."' and payment='REFUND'");
		echo '<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
			<tr>
					<td colspan="3">TRANSACTION</td>
					<td style="text-align:right;"></td>
				</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td>CASH</td>
				<td style="text-align:right;">'.number_format($res[0]['cash_amt'],2).'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td>CHARGE</td>
				<td style="text-align:right;">'.number_format($res[0]['charge_amt'],2).'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td>REFUND </td>
				<td style="text-align:right;">('.number_format(0,2).')</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td>VOID </td>
				<td style="text-align:right;">('.number_format($res['voids']['CASH'],2).')</td>
			</tr>
			<tr>
				<td colspan="2">TOTAL</td>
				<td style="text-align:right;border-top:1px solid #000;">&nbsp;</td>
				<td style="text-align:right;">'.number_format($res[0]['total_sales']-$res[0]['voucher_amount'],2).'</td>
			</tr>
			
			<tr>
				<td colspan="2">VOUCHER TRANSACTION </td>
				<td style="text-align:right;">'.number_format($res[0]['voucher_amount']+$res['voids']['VOUCHER'],2).'</td>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">VOUCHER BAL </td>
				<td style="text-align:right;">'.number_format($res[0]['voucher_bal'],2).'</td>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td>VOID </td>
				<td style="text-align:right;">('.number_format($res['voids']['VOUCHER'],2).')</td>
			</tr>
			<tr>
				<td colspan="2">TOTAL VOUCHER </td>
				<td style="text-align:right;border-top:1px solid #000;">&nbsp;</td>
				<td style="text-align:right;">'.number_format($res[0]['voucher_amount'],2).'</td>
			</tr>
			<tr style="border-top:1px solid #000;border-bottom:1px solid #000;padding:5px;">
				<td colspan="3">TOTAL SALES '.'ST0'.$counter.'</td>
				<td style="text-align:right;">'.number_format($res[0]['total_sales'],2).'</td>
			</tr>
		</table>';
		$grand_total+=($res[0]['total_sales']);
		$grand_vatexempt+=($res[0]['book_sales']);
		$grand_vatsales+=($res[0]['total_vat']);
		$grand_vat+=$res[0]['total_vat'];
		$total_voucher+=$res[0]['voucher_amount'];
		$total_cash+=($res[0]['cash_amt']);
		$total_per_cat['BOOKS']['sales']+=($res[0]['book_sales']);
		$total_per_cat['BOOKS']['cost']+=$res[0]['book_cost'];
		$total_per_cat['ID SLINGS']['sales']+=$res[0]['idsling_sales'];
		$total_per_cat['ID SLINGS']['cost']+=$res[0]['idsling_cost'];
	}
	// echo "<pre>";
	// print_r($total_per_cat);
	// echo "</pre>";
	?>
	
	<?php if($_REQUEST['all']){ ?>
		<div style="width:100%;font-weight:bold;">
			GRAND TOTAL SALES: <span style="float:right;"><?=number_format($grand_total,2)?></span>
		</div>
		<hr/>
	<?php } ?>
	<div style="clear:both;height:10px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%" >
			<tr>
				<td>VAT-EXEMPT SALES</td>
				<td><?=number_format($grand_vatexempt,2)?></td>
			</tr>
			<tr>
				<td>VAT SALES</td>
				<td><?=number_format($grand_vatsales,2)?></td>
			</tr>
			<tr>
				<td>VAT AMOUNT</td>
				<td><?=number_format($grand_vat,2)?></td>
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
	if(!$_REQUEST['all'] && date('Y-m-d',strtotime($info['start_date']))>='2017-03-01'){ //journal entry...
		foreach($persup as $x=>$y){
			if($y['supplier_id']==22){
				$martha['cost']+=$y['total_cost'];
				$martha['selling']+=$y['total_selling'];
			}else{
				$others['cost']+=$y['total_cost'];
				$others['selling']+=$y['total_selling'];
			}
		}
		
		$info_r = $db->getWHERE("*","tbl_reading","where reading_num='".$reading."' and counter='".$counter."'");
		$glref = isset($info_r['glref'])&&$info_r['glref']!=0?$info_r['glref']:$con->getNextJournalID('SJ');
		$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`) values 
			('".$glref."','{$_SESSION['connect']}','".$info_r['start_date']."','SJ', 
			'".strtoupper($_SESSION['connect']).":TO RECORD SALES COUNTER $counter READING: $reading','".($total_cash+$total_voucher)."', 
			'".$_SESSION['xid']."','ForApproval') 
			on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
		switch($_SESSION['connect']){
			case'uclm':
				$entry[]=array('account_code'=>'1014','account_desc'=>'CASH - UC LM','dr'=>($total_cash),'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
			break;
			case'ucmambaling':
				$entry[]=array('account_code'=>'1013','account_desc'=>'CASH - UC METC','dr'=>($total_cash),'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
			break;
			case'ucmain':
				$entry[]=array('account_code'=>'1012','account_desc'=>'CASH - UC MAIN','dr'=>($total_cash),'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
			break;
			case'ucbanilad':
				$entry[]=array('account_code'=>'1011','account_desc'=>'CASH - UC BANILAD','dr'=>($total_cash),'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
			break;
		}
		if($total_voucher!=0){
			$entry[]=array('account_code'=>'7502','account_desc'=>'ACCOUNTS RECEIVABLE-TRADE','dr'=>$total_voucher,'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		}
		$entry[]=array('account_code'=>'4000','account_desc'=>'SALES - BOOKS','dr'=>'','cr'=>$total_per_cat['BOOKS']['sales'],'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		$entry[]=array('account_code'=>'4001','account_desc'=>'SALES - ID','dr'=>'','cr'=>($grand_vatsales),'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		$entry[]=array('account_code'=>'2026','account_desc'=>'OUTPUT VAT','dr'=>'','cr'=>($grand_vat),'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		//Journal Entry for Inventory
		$entry[]=array('account_code'=>'5000','account_desc'=>'COST OF SALES - BOOKS','dr'=>$total_per_cat['BOOKS']['cost'],'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		$entry[]=array('account_code'=>'5009','account_desc'=>'COST OF SALES - ID','dr'=>$total_per_cat['ID SLINGS']['cost'],'cr'=>'','center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		
		$entry[]=array('account_code'=>'1402','account_desc'=>'INVENTORY REGULAR','dr'=>'','cr'=>$martha['cost'],'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		$entry[]=array('account_code'=>'1400','account_desc'=>'INVENTORY CONSIGNMENT - BOOKS','dr'=>'','cr'=>$others['cost'],'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		
		$entry[]=array('account_code'=>'1401','account_desc'=>'INVENTORY - ID','dr'=>'','cr'=>$total_per_cat['ID SLINGS']['cost'],'center'=>"{$_SESSION['connect']}",'type'=>'SJ');
		//Journal Entry for Inventory
		// echo "<pre>";
		// print_r($entry);
		// echo "</pre>";
		$glid=$con->insertSJDiffApproach($glref,$sql,$info_r['start_date'],$entry);
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