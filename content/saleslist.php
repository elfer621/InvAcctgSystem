<?php
$tbl = $_REQUEST['type']?$_REQUEST['type']:"tbl";
$terminal = $_REQUEST['terminalnum']?$_REQUEST['terminalnum']:1;
$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
/*if($tbl=="old"){
$sql = "select sum(amount) as amount,reading as reading_num,counter_num as counter,date as start_date,date as end_date
	from old_sales_receipt_1 group by reading,counter_num";
}else{
$sql="select * from tbl_reading where end_date !='0000-00-00' order by start_date desc";
}*/
$sql = "select sum(amount) as amount,reading as reading_num,counter_num as counter,date as start_date,date as end_date
	from tbl_sales_receipt_$terminal where date between '$begdate' and '$enddate' group by reading,counter_num order by reading desc,date desc";
$qry = mysql_query($sql);
echo mysql_error();
//$sql_current = "select reading_num,counter,start_date from tbl_reading where end_date='0000-00-00'";
//$qry_current = mysql_query($sql_current);

?>
<?php /*
<fieldset>
	<legend>Current Sales:</legend>
	<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th style="border:none;">Start Date</th>
			<th style="border:none;">Reading Num</th>
			<th style="border:none;">Counter</th>
			<th style="border:none;">Amount</th>
		</tr>
		<? while($row = mysql_fetch_assoc($qry_current)){ 
			//$info = $db->getWHERE("sum(amount) as total_sales","tbl_sales_receipt_{$_SESSION['counter_num']}","where counter_num='".$row['counter']."' and reading='".$row['reading_num']."'");
		?>
			<tr>
				<td class="data_lightgray"><?= $row['start_date']?></td>
				<td class="data_lightgray"><?= $row['reading_num']?></td>
				<td class="data_lightgray"><?= $row['counter']?></td>
				<td class="data_lightgray" style="text-align:right;"><?= number_format($info['total_sales'],2) ?></td>
			</tr>
		<? } ?>
	</table>
</fieldset>
*/?>
<div style="clear:both;height:10px;"></div>
<form name="frm_cust" method="post">
	<div style="float:left;margin-right:30px;">Beg Date</div>
	<input style="float:left;margin-right:50px;width:100px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
	<div style="float:left;margin-right:30px;">End Date</div>
	<input style="float:left;width:100px;margin-right:30px;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
	<div style="float:left;margin-right:30px;">Terminal #</div>
	<input style="float:left;width:80px;margin-right:30px;" type="text" id="terminalnum" name="terminalnum" value="<?=$_REQUEST['terminalnum']?$_REQUEST['terminalnum']:1?>"/>
	<input type="submit" value="Search" name="search_date"/>
</form>
<div style="clear:both;height:10px;"></div>
<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
	<tr>
		<th style="border:none;">Menu</th>
		<th style="border:none;">Reading Num</th>
		<th style="border:none;">Counter</th>
		<th style="border:none;">Date Start</th>
		<th style="border:none;">Amount</th>
		<th style="border:none;">Cost</th>
		<th style="border:none;">Gain</th>
	</tr>
	<? while($row = mysql_fetch_assoc($qry)){ 
		//$exp = $db->getWHERE("sum(amount) as exptotal","{$tbl}_cashout","where reading='{$row['reading_num']}' group by reading");
	?>
		<tr>
			<td class="data_lightgray"><a href="javascript:viewReport('reports/sales_reports.php?rep=perreading&reading=<?=$row['reading_num']?>&counter=<?=$row['counter']?>&type=<?=$tbl?>')">perItem</a></td>
			<td class="data_lightgray"><a href="javascript:reprintReading(<?=$row['reading_num']?>,<?=$row['counter']?>)"><?= $row['reading_num']?></a></td>
			<td class="data_lightgray"><?= $row['counter']?></td>
			<td class="data_lightgray"><?= date("D M j, Y",strtotime($row['start_date'])) ?></td>
			<td class="data_lightgray" style="text-align:right;"><?= number_format($row['amount'],2) ?></td>
			<td class="data_lightgray"><?=number_format($cost = $db->sumTotalCost($row['reading_num'],$row['counter'],$tbl."_sales_items"),2)?></td>
			<td class="data_lightgray" style="text-align:right;"><?= number_format($row['amount']-$cost,2) ?></td>
		</tr>
	<? $total_amt+=$row['amount'];$total_cost+=$cost;} ?>
	<tr>
		<th style="border:none;" colspan="4">Sub Total</th>
		<th style="border:none;"><?= number_format($total_amt,2) ?></th>
		<th style="border:none;"><?= number_format($total_cost,2) ?></th>
		<th style="border:none;"><?= number_format($total_amt-$total_cost,2) ?></th>
	</tr>
</table>
<script>
	//var rep = './reports/reading_end_reprint.php?readingnum=';
	var rep = './reports/<?=$_SESSION['reports']['XReading']['report_link']?>?readingnum=';
	$(document).ready(function() {
		$('#beg_date').datepicker({
			inline: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:"yy-mm-dd"
		});
		$('#end_date').datepicker({
			inline: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:"yy-mm-dd"
		});
	});
	function reprintReading(readingnum,counter_num){
		if (window.showModalDialog) {
			window.showModalDialog(rep+readingnum+'&counter_num='+counter_num,"Reading Reprint","dialogWidth:500px;dialogHeight:600px");
		} else {
			window.open(rep+readingnum+'&counter_num='+counter_num,"Reading Reprint",'height=600,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
		}
	
	}
	function viewReport(page){
		var win=window.open(page,'_blank');
		win.focus();
	}
</script>