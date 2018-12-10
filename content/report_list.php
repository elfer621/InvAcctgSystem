<h3>Report List</h3>
<?php if($_SESSION['restrictionid']==10){ ?>
<input type="button" value="COMPLIANCE" onclick="showReportsPopup('compliance');" style="height:30px;width:300px;"/>
<?php }else{ ?>
<table class="tbl" cellspacing="0" cellpadding="0" border="1" width="100%">
	<tr>
		<th>SALES</th>
		<th>INVENTORY</th>
		<th style="width:300px;">FS</th>
		<th>OTHERS</th>
	</tr>
	<tr>
		<td><input type="button" value="SUMMARY" onclick="showNewDataFilterNotJqgrid(53);" style="height:30px;width:100%;"/></td>
		<td>
			<?php if($db->constatus=='lizgan_warehouse'or$db->constatus=='lizgan_main'){ ?>
			<input type="button" value="PER PRODUCT" onclick="viewReport('reports/prod_inventory_allbranch.php');" style="height:30px;width:100%;"/>
			<?php }else{ ?>
			<input type="button" value="PER PRODUCT" onclick="viewReport('reports/prod_inventory<?=$repExtension?>.php');" style="height:30px;width:100%;"/>
			<?php } ?>
		</td>
		<td><input type="button" value="LEDGER" onclick="showNewDataFilterNotJqgrid(63);" style="height:30px;width:100%;"/></td>
		<td><input type="button" value="GRAPHICAL" onclick="viewReport('reports/graph.php');" style="height:30px;width:100%;"/></td>
	</tr>
	<tr>
		<td><input type="button" value="PER ITEM" onclick="viewReport('reports/sales_reports.php');" style="height:30px;width:100%;"/></td>
		<td><!--input type="button" value="PER SUPPLIER" onclick="viewReport('reports/prod_inventory_allbranch_persupplier.php');" style="height:30px;width:100%;"/--></td>
		<td><input type="button" value="TRIAL BALANCE" onclick="showNewDataFilterNotJqgrid(64);" style="height:30px;width:100%;"/></td>
		<td><input type="button" value="CUSTOMER A/R" onclick="viewReport('reports/customer_balance.php');" style="height:30px;width:100%;"/></td>
	</tr>
	<tr>
		<td>
		<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
		<input type="button" value="PER RECEIPT" onclick="showNewDataFilterNotJqgrid(68);" style="height:30px;width:100%;"/>
		<?php }else{ ?>
		<input type="button" value="PER RECEIPT" onclick="showNewDataFilterNotJqgrid(54);" style="height:30px;width:100%;"/>
		<?php } ?>
		
		</td>
		<td><input type="button" value="SUMMARY" onclick="showNewDataFilterNotJqgrid(62);" style="height:30px;width:100%;"/></td>
		<td><input type="button" value="BALANCE SHEET" onclick="showNewDataFilterNotJqgrid(66);" style="height:30px;width:100%;"/></td>
		<td>
			<?php if($_SESSION['default_db']=="lizgan_main"){ ?>
			<input type="button" value="TEACHER COMMISSION" onclick="viewReport('reports/prod_sold_commission.php');" style="height:30px;width:100%;"/>
			<input type="button" value="CLOTHING MATERIALS" onclick="viewReport('reports/sales_reports_lizgan_clothingmat.php');" style="height:30px;width:100%;"/>
			<?php } ?>
			<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
			<input type="button" value="COMPLIANCE" onclick="showReportsPopup('compliance');" style="height:30px;width:100%;"/>
			<?php } ?>
			<input type="button" value="A/R AGING" onclick="viewReport('reports/customer_balance_agingSummary<?=$_SESSION['repExtension']?>.php');" style="height:30px;width:100%;"/>
		</td>
	</tr>
	<tr>
		<td><input type="button" value="PER CATEGORY" onclick="viewReport('reports/sales_per_category.php');" style="height:30px;width:100%;"/></td>
		<td><input type="button" value="ZERO INVENTORY" onclick="viewReport('reports/zero_inventory.php');" style="height:30px;width:100%;"/></td>
		<td>
			<input type="button" value="INCOME STATEMENT" onclick="showNewDataFilterNotJqgrid(65);" style="height:30px;width:100%;"/>
			<?php if($_SESSION['default_db']=="lizgan_main"){ ?>
			<input type="button" value="INCOME STATEMENT (ReceiptBased)" onclick="viewReport('reports/income_statement.php');" style="height:30px;width:100%;"/>
			<?php } ?>
		</td>
		<td>
			<?php if($_SESSION['default_db']=="lizgan_main"){ ?>
			<input type="button" value="PER TEACHER COMMISSION" onclick="viewReport('reports/prod_sold_commission_teacher.php');" style="height:30px;width:100%;"/>
			<?php } ?>
			<?php if($_SESSION['default_db']=="rber_db"){ ?>
			<input type="button" value="PURCHASES/EXPENSES" onclick="viewReport('reports/purchases_exps_list.php');" style="height:30px;width:100%;"/>
			<?php } ?>
			<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
			<input type="button" value="DEMOGRAPHIC" onclick="viewReport('reports/teletech_demographic.php');" style="height:30px;width:100%;"/>
			<?php } ?>
			<?php if($_SESSION['settings']['system_name']=="RTK"){ ?>
			<input type="button" value="PER AGENT SALES" onclick="viewReport('reports/sales_invoice_per_agent_summary.php');" style="height:30px;width:100%;"/>
			<?php } ?>
		</td>
	</tr>
</table>
<?php } ?>
<script>
function previewRep(stat,report_name){
	var company_name=$("#company_name").val();
	var data_reference=$("#data_reference").val();
	viewReport(report_name+'?company_name='+company_name+'&data_reference='+data_reference);
}
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
function showReportsPopup(type){
	clickDialog("dialogbox",500,200,"popUpforReports&reptype="+type,"More Info");
}
function viewSalesJournal(){
	var counter = $("#counter_num").val();
	var reading = $("#reading").val();
	var begdate = $("#begdate").val();
	var enddate = $("#enddate").val();
	viewReport('reports/sales_journal.php?counter='+counter+'&reading='+reading+'&begdate='+begdate+'&enddate='+enddate);
}
</script>