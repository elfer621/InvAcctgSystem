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
require_once"../class/dbConnection.php";
require_once"../settings.php";
$db=new dbConnect();
$db->openDb();
$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
if($_POST){
		$begdate = $_REQUEST['beg_date'];
		$enddate = $_REQUEST['end_date'];
}
$sql="select a.*,b.*,c.* from tbl_sales_receipt_1 a 
	left join (select receipt,customer_name from tbl_customers_trans a left join 
	tbl_customers b on a.cust_id=b.cust_id) b on a.receipt_id=b.receipt
	left join tbl_sales_moreinfo c on a.receipt_id=c.sales_refid
	where (a.date between '$begdate' and '$enddate') and a.type='SALES' order by c.ob asc";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
$agent = $db->resultArray("*","req_agent","");
foreach($agent as $key => $val){
	$comm[$val['agent_name']] = 0;
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2>Commission Report</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;margin-left:30px;">End Date</div>
		<input style="float:left;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
		<input type="submit" value="Search" name="search_date" style="float:left;margin-left:30px;"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" style="margin-left:20px;">
		<tr>
			<td>Date</td>
			<td>TRA/DR</td>
			<td>OB</td>
			<td>Customer Name</td>
			
			<td>Qty</td>
			<td>SKU</td>
			<td>Product Name</td>
			<td>Selling Price</td>
			<td>Cost Price</td>
			<td>Gain</td>
			<td>Comm</td>
			<td>Agent</td>
			<td>Cashier</td>
		</tr>
		<?php while($row=mysql_fetch_assoc($qry)){ ?>
			<?php $items = $db->resultArray("*"," tbl_sales_items","where receipt='".$row['receipt_id']."'"); ?>
			<?php $num = count($items)+1; ?>
			<tr>
				<td rowspan="<?=$num?>"><?=$row['date']?></td>
				<td rowspan="<?=$num?>"><?=$row['receipt_id']?></td>
				<td rowspan="<?=$num?>"><?=$row['ob']?></td>
				<td rowspan="<?=$num?>" style="text-align:left;"><?=$row['customer_name']?></td>
			</tr>
				<?php foreach($items as $key => $val){ ?>
				<tr>
					<td><?=$val['qty']." ".$val['unit']?></td>
					<td><a href="javascript:viewTrans('<?php echo $val['skuid'] ?>','<?php echo $val['item_desc'] ?>')"><?=$val['skuid']?></a></td>
					<td style="text-align:left;"><?=$val['item_desc']?></td>
					<td><?=number_format($val['selling'],2)?></td>
					<td><input onchange="computeGain('<?=$val['item_desc']?>',<?=$val['id']?>,<?=$val['selling']?>,this.value,<?=$val['qty']?>,'<?=$val['barcode']?>')" id="cost_<?=$val['id']?>" name="cost_<?=$val['id']?>" style="width:70px;" type="text" value="<?=$val['cost']?>"/></td>
					<td id="gain_<?=$val['id']?>" style="text-align:right;"><?=$val['gain']?></td>
					<td id="comm_<?=$val['id']?>" style="text-align:right;"><?=$db->getCommission(((double)$val['selling'] - (double)$val['cost']),$val['item_desc'],$val['qty'])?></td>
					<td><?=$row['agent']?></td>
					<td><?=$row['cashier']?></td>
				</tr>
				<?php 
					$comm[$row['agent']] += $db->getCommission(((double)$val['selling'] - (double)$val['cost']),$val['item_desc'],$val['qty']);
					} 
					?>
		<?php } ?>
	</table>
	<div style="clear:both;height:5px;"></div>
	<?php if($view==2){ ?>
	<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" style="margin-left:20px;">
		<?php 
			foreach($agent as $key => $val){
				$com_name .= '<td>'.$val['agent_name'].'</td>';
				$com_val .= '<td>'.number_format($comm[$val['agent_name']],2).'</td>';
			}
		echo "<tr>$com_name</tr><tr>$com_val</tr>";
		?>
	</table>
	<?php } ?>
	<div id="dialog"></div>
	<div style="clear:both;height:25px;"></div>
</body>
<script>
function viewTrans(sku_id,prod_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:prod_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=prodtrans&sku_id='+sku_id,async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}
function computeGain(item,refid,selling,cost,qty,barcode){
	var selling = new Number(selling);
	var cost = new Number(cost);
	var gain = selling - cost;
	var qty = new Number(qty);
	$("#gain_"+refid).html(gain);
	$.ajax({
		url: '../content/pos_ajax.php?execute=computeCommission&begdate=<?=$begdate?>&enddate=<?=$enddate?>',
		data:{cost:cost,gain:gain,refid:refid,item:item,qty:qty,barcode:barcode},
		type:"POST",
		success:function(data){
			res = data.split(",");
			if(res[0]=="success"){
				$("#comm_"+refid).html(res[1]);
				//window.location=document.URL;
			}else{
				alert(res[0]);
			}
		}
	});
}
$(document).ready(function() {
	$('#beg_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});
/*function updateDateDelivered(cust_id,receipt){
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
}*/

</script>
</html>