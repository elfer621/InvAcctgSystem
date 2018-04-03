<h3>Report List</h3>
<table class="tbl" cellspacing="0" cellpadding="0" border="1" width="100%">
	<tr>
		<th>Title</th>
		<th>Menu</th>
	</tr>
	<?php if($view!=6){ ?>
	<?php if($db->constatus=="lizgan_main"){ ?>
		<tr>
			<td>Product Inventory</td>
			<td><input type="button" value="View" onclick="viewReport('reports/prod_inventory_allbranch.php');" style="height:30px;width:100px;"/></td>
			
		</tr>
		<tr>
			<td>Inventory Per Supplier</td>
			<td><input type="button" value="View" onclick="viewReport('reports/prod_inventory_allbranch_persupplier.php');" style="height:30px;width:100px;"/></td>
			
		</tr>
	<?php }else{ ?>
		<?php if($db->constatus=='lizgan_warehouse'){ ?>
			<!--tr>
				<td>Product Inventory (AllBranch)</td>
				<td><input type="button" value="View" onclick="viewReport('reports/prod_inventory_allbranch.php');" style="height:30px;width:100px;"/></td>
			</tr-->
			<tr>
				<td>Inventory Per Supplier (AllBranch)</td>
				<td><input type="button" value="View" onclick="viewReport('reports/prod_inventory_allbranch_persupplier.php');" style="height:30px;width:100px;"/></td>
			</tr>
		<?php } ?>
		<tr>
			<td>Product Inventory</td>
			<td><input type="button" value="View" onclick="viewReport('reports/prod_inventory.php');" style="height:30px;width:100px;"/></td>
		</tr>
		<!--tr>
			<td>Previous Reading</td>
			<td><input type="button" value="View" onclick="previousReading('<?=$reading_type;?>');" style="height:30px;width:100px;"/></td>
		</tr>
		<tr>
			<td>Z-Reading</td>
			<td><input type="button" value="View" onclick="viewReport('reports/reading_end.php?readingnum=<?=$db->getServerReadingnum()?>&all=true');" style="height:30px;width:100px;"/></td>
		</tr-->
	<?php } ?>
	<tr>
		<td>Zero Inventory</td>
		<td><input type="button" value="View" onclick="viewReport('reports/zero_inventory.php');" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Sales Report (Per Item)</td>
		<td><input type="button" value="View" onclick="viewReport('reports/sales_reports.php');" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Sales Report (Per Receipt)</td>
		<td><input type="button" value="View" onclick="showNewDataFilterNotJqgrid(54);" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Sales Report (Summary)</td>
		<td><input type="button" value="View" onclick="showNewDataFilterNotJqgrid(53);" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Graphical Reports</td>
		<td><input type="button" value="View" onclick="viewReport('reports/graph.php');" style="height:30px;width:100px;"/></td>
	</tr>
	<?php } ?>
	<tr>
		<td>Customer Balance</td>
		<td><input type="button" value="View" onclick="viewReport('reports/customer_balance.php');" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Income Statement</td>
		<td><input type="button" value="View" onclick="viewReport('reports/acctg_report.php?report_type=PNL');" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Balance Sheet</td>
		<td><input type="button" value="View" onclick="viewReport('reports/acctg_report.php?report_type=BS');" style="height:30px;width:100px;"/></td>
	</tr>
	<tr>
		<td>Trial Balance</td>
		<td><input type="button" value="View" onclick="viewReport('reports/acctg_report.php?report_type=TRIALBAL');" style="height:30px;width:100px;"/></td>
	</tr>
	<!--tr>
		<td>Ledger (Summary)</td>
		<td><input type="button" value="View" onclick="showDataFilterNotJqgrid(52);" style="height:30px;width:100px;"/></td>
	</tr-->
	<tr>
		<td>Ledger (Summary)</td>
		<td><input type="button" value="View" onclick="showNewDataFilterNotJqgrid(63);" style="height:30px;width:100px;"/></td>
	</tr>
</table>
<script>
function viewReport(page){
	var win=window.open(page,'_blank');
	win.focus();
}
function previousReading(page){
	var reading = prompt("Enter Reading Number");
	if(reading !=""){
		if($.isNumeric(reading)){
			viewReport(page+reading);
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new qty...");
	}
}
function viewReportPopup(){
	clickDialog('dialogbox',400,200,'viewReportPopup','Report View');
}
function viewSalesJournal(){
	var counter = $("#counter_num").val();
	var reading = $("#reading").val();
	var begdate = $("#begdate").val();
	var enddate = $("#enddate").val();
	viewReport('reports/sales_journal.php?counter='+counter+'&reading='+reading+'&begdate='+begdate+'&enddate='+enddate);
}
</script>