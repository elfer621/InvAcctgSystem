<?php
if($_POST['search_cust']){
	if($_REQUEST['cust_name']){
		$flag=false;
		for($x=1;$x<=$howmanycounter;$x++){
			if($flag)$s.=" union ";
			$s.="select receipt_id,counter_num,reading,date,amount,studentname,course,yr from tbl_sales_receipt_$x where studentname like '%".$_REQUEST['cust_name']."%'";
			$flag=true;
		}
	}else{
		$flag=false;
		for($x=1;$x<=$howmanycounter;$x++){
			if($flag)$j.=" union ";
			$j.="select receipt_id,counter_num,reading,date,amount,studentname,course,yr from tbl_sales_receipt_$x";
			$flag=true;
		}
		$s="select b.receipt_id,b.counter_num,b.reading,b.date,b.amount,b.studentname,b.course,b.yr from (select * from tbl_sales_items where item_desc like '%".$_REQUEST['item_name']."%') a left join 
		 ($j) b on a.receipt=b.receipt_id and a.reading=b.reading
		";
	}
	
	//echo $s;
	$qry=mysql_query($s);
	if(!$qry){
		echo mysql_error();
	}
}
?>
<form name="frm_filter" method="post">
	<div style="float:left;margin-right:30px;">Customer Name</div>
	<input style="float:left;margin-right:50px;width:250px;" type="text" id="cust_name" name="cust_name" value=""/>
	<div style="float:left;margin-right:30px;">Item Name</div>
	<input style="float:left;margin-right:50px;width:250px;" type="text" id="item_name" name="item_name" value=""/>
	<input type="submit" value="Search" name="search_cust" style="float:left;width:100px;margin-right:10px;"/>
	<input type="button" value="Export" onclick="ExportToExcel('tbl');" style="float:left;width:100px;"/>
</form>
<div style="clear:both;height:10px;"></div>
<div style="height:500px;overflow:auto;">
	<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th style="border:none;">Menu</th>
			<th style="border:none;">Receipt Num</th>
			<th style="border:none;">Reading Num</th>
			<th style="border:none;">Counter</th>
			<th style="border:none;">Date</th>
			<th style="border:none;">Amount</th>
			<th style="border:none;">Name</th>
			<th style="border:none;">Course/Grade</th>
		</tr>
		<?php while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td class="data_lightgray"><a href="javascript:viewReceipt(<?=$row['receipt_id']?>,<?=$row['reading']?>,<?=$row['counter_num']?>);">Show</a></td>
				<td class="data_lightgray"><?=$row['receipt_id']?></td>
				<td class="data_lightgray"><?=$row['reading']?></td>
				<td class="data_lightgray"><?=$row['counter_num']?></td>
				<td class="data_lightgray"><?=$row['date']?></td>
				<td class="data_lightgray"><?=number_format($row['amount'],2)?></td>
				<td class="data_lightgray"><?=$row['studentname']?></td>
				<td class="data_lightgray"><?=$row['course']." ".$row['yr']?></td>
			</tr>
		<?php } ?>
	</table>
</div>
<script>
function viewReceipt(num,readingnum,counter) {
	if (window.showModalDialog) {
		window.showModalDialog('./reports/receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt","dialogWidth:350px;dialogHeight:350px");
	} else {
		window.open('./reports/receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
</script>