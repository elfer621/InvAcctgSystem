<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
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
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl tbody td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
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
	body,td {
			font-size:15px !important;
		}
</style>
<style type="text/css" media="print" >
	thead {display: table-header-group !important;}
</style>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);

session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$db->openDb();
$output = preg_split( "/ (and) /", $_REQUEST['where'] );
$flag=false;
foreach($output as $key => $val){
	if($db->strpos_arr($val,array("Campus")) == true){
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else if($db->strpos_arr($val,array("counter_num")) == true){
		// $counter =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
		// if($flag)$where.=" and ";
		// $where.= "$val";
		// $flag=true;
		$counter = preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else if($db->strpos_arr($val,array("isVoid")) == true){
		$VOID = preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else if($db->strpos_arr($val,array("instructor_code")) == true){
		$noinscode = preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
if(trim($VOID[1])=="Y"){
		$sql="select receipt_id receipt,reading,counter_num counter,date,type,payment,studentname,(amount - vat) book_sales, vat idsling_sales,amount total_sales,(select variable_values from settings where variable_name='session_connect') campus 
		from view_receipt ".($where?"where $where":"")." order by counter_num asc,receipt_id desc";
}else{
	//$filter = "where receipt in (select receipt_id from view_receipt ".($where?"where $where":"").")";
	// $sql = "SELECT 
				// receipt,counter_num counter,reading,date,studentname,payment,type,amount,orderslip,btimestamp,course,yr,xstudentid,  
				// voucher_amt,
				// (sum(if(category_id = 1,qty*selling,0))-voucher_amt) cash_amt,
				// sum(if(category_id = 1,qty*selling,0)) book_sales,
				// sum(if(category_id = 1,qty*cost,0)) book_cost,
				// sum(if(category_id = 1,qty*vat,0)) book_vat,
				// sum(if(category_id = 2,qty*selling,0)) idsling_sales,
				// sum(if(category_id = 2,qty*cost,0)) idsling_cost,
				// sum(if(category_id = 2,qty*vat,0)) idsling_vat,
				// sum(qty*selling) total_sales,
				// sum(qty*cost) total_cost,
				// sum(qty*vat) total_vat,
				// (select variable_values from settings where variable_name='session_connect') campus 
			// from (select a.*,b.date,b.studentid xstudentid,b.studentname,b.course,b.yr,b.payment,b.type,b.counter_num,b.amount,b.orderslip,b.timestamp btimestamp,
				// COALESCE(c.amount,0) voucher_amt 
				// from (select * from tbl_sales_items $filter) a 
				// left join view_receipt b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading 
				// left join tbl_voucherdeduction_details c on a.receipt=c.receipt and a.counter=c.counter and a.reading=c.reading 
				// ) tbl ".($where?"where $where":"")." group by receipt desc";
	if($counter){
		$filter = "where receipt in (select receipt_id from tbl_sales_receipt_".trim($counter[1])." ".($where?"where $where":"").")";
		$sql = "SELECT 
				receipt,counter_num counter,reading,date,studentname,payment,type,amount,orderslip,btimestamp,course,yr,xstudentid,  
				voucher_amt,
				sum(if(payment = 'CASH',qty*selling,0)) cash_amt,
				sum(if(payment = 'CHARGE',qty*selling,0)) charge_amt,
				sum(qty*selling) total_sales,
				sum(qty*cost) total_cost,
				sum(qty*vat) total_vat,
				(select variable_values from settings where variable_name='session_connect') campus 
			from (select a.*,b.date,b.studentid xstudentid,b.studentname,b.course,b.yr,b.payment,b.type,b.counter_num,b.amount,b.orderslip,b.timestamp btimestamp,
				COALESCE(c.amount,0) voucher_amt 
				from (select * from tbl_sales_items $filter) a 
				left join tbl_sales_receipt_".trim($counter[1])." b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading 
				left join tbl_voucherdeduction_details c on a.receipt=c.receipt and a.counter=c.counter and a.reading=c.reading 
				) tbl ".($where?"where $where":"")." group by receipt desc";
	}else{
		$flag=false;
		$settings = $db->getWHERE("*","settings","where variable_name='howmanycounter'");
		for($x=1;$x<=$settings['variable_values'];$x++){
			if(trim($noinscode[1])=="Y"){
				$filter = "where (instructor_code='' or instructor_code is null) and receipt in (select receipt_id from tbl_sales_receipt_".trim($x)." ".($where?"where $where":"").")";
			}else{
				$filter = "where receipt in (select receipt_id from tbl_sales_receipt_".trim($x)." ".($where?"where $where":"").")";
			}
			
			$q = "SELECT 
					receipt,counter_num counter,reading,date,studentname,payment,type,amount,orderslip,btimestamp,course,yr,xstudentid,  
					voucher_amt,
					sum(if(payment = 'CASH',qty*selling,0)) cash_amt,
					sum(if(payment = 'CHARGE',qty*selling,0)) charge_amt,
					sum(qty*selling) total_sales,
					sum(qty*cost) total_cost,
					sum(qty*vat) total_vat,
					(select variable_values from settings where variable_name='session_connect') campus 
				from (select a.*,b.date,b.studentid xstudentid,b.studentname,b.course,b.yr,b.payment,b.type,b.counter_num,b.amount,b.orderslip,b.timestamp btimestamp,
					COALESCE(c.amount,0) voucher_amt 
					from (select * from tbl_sales_items $filter) a 
					left join tbl_sales_receipt_".trim($x)." b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading 
					left join tbl_voucherdeduction_details c on a.receipt=c.receipt and a.counter=c.counter and a.reading=c.reading 
					) tbl ".($where?"where $where":"")." group by receipt desc";
			if($flag)$sql.=" union ";
			$sql.=$q;
			$flag=true;
		}
	}
	
	// echo $sql;
	// exit;
}
$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	}else{
		$arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			}
		}
	}
}
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}
?>
<div class="print" style="width:950px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="print tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="13"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Sales Journal Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="13">
					<?php
						foreach($output as $key => $val){
							echo $val."<br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<th rowspan="2">OR #</th>
				<th rowspan="2">ORDER SLIP</th>
				<th rowspan="2">READING</th>
				<th rowspan="2">STATION</th>
				<th rowspan="2">DATE</th>
				<th rowspan="2">TIME</th>
				<th rowspan="2">TYPE</th>
				<th rowspan="2">PAYMENT</th>
				<th rowspan="2">CUSTOMER</th>
				<th colspan="2">PAYMENT</th>
				<th rowspan="2">TOTAL SALES</th>
			</tr>
			<tr>
				<th>Charge</th>
				<th>Cash</th>
			</tr>
		</thead>
		<tbody>
			<? 	foreach($list as $key => $row){ ?>
			<tr style="<?=$row['type']=="VOID"?"color:red;":""?>">
				<td><a href="javascript:viewReceipt(<?=$row['receipt']?>,<?=$row['reading']?>,<?=$row['counter']?>);" class="receipt"><?= $db->customeFormat($row['receipt'],7)?></a></td>
				<td><?= $db->customeFormat($row['orderslip'],7)?></td>
				<td style="text-align:center;"><?= $row['reading']?></td>
				<td style="text-align:center;"><?= strtoupper($row['campus'])." ST-".$row['counter']?></td>
				<td style="text-align:center;font-size:10px;"><?= date('Y-m-d',strtotime($row['date']))?></td>
				<td style="text-align:center;font-size:10px;"><?= date('h:i a',strtotime($row['btimestamp']))?></td>
				<td style="text-align:center;"><?= $row['type']?></td>
				<td style="text-align:center;"><?= $row['payment']?></td>
				<td id="seq_<?=$row['receipt']?>_<?=$row['reading']?>_<?=$row['counter']?>" style="text-align:center;"><?= $row['studentname']?></td>

				<td style="text-align:right;"><?= $row['charge_amt']==0?"":number_format($row['charge_amt'],2) ?></td>
				<td style="text-align:right;"><?= $row['cash_amt']==0?"":number_format($row['cash_amt'],2) ?></td>
				
				<td style="text-align:right;<?=($row['amount']!=$row['total_sales']?"color:red;":"")?>"><?= number_format($row['total_sales'],2) ?></td>
			</tr>
			<? 	
				$total['charge_amt'] += $row['charge_amt'];
				$total['cash_amt'] += $row['cash_amt'];
				$total['sales']+=$row['total_sales'];
				$total['vat']+=$row['total_vat'];
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="9">Sub Total</th>
				<th ><?=number_format($total['charge_amt'],2)?></th>
				<th ><?=number_format($total['cash_amt'],2)?></th>
				<th ><?=number_format($total['sales'],2)?></th>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
<script>
function viewReceipt(num,readingnum,counter) {
	//$("#seq_"+num+"_"+readingnum+"_"+counter).prepend(" <span style='color:red;'>(EDITED)</span> ");
	var page = './receipt_forlab.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true&computecommission=true';
	var win=window.open(page,'_blank');
	win.focus();
	// if (window.showModalDialog) {
		// window.showModalDialog('./receipt_forcommission.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt","dialogWidth:700px;dialogHeight:500px");
	// } else {
		// window.open('./receipt_forcommission.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt",'height=500,width=700,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	// }
}
</script>
