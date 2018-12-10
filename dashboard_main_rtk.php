<?php if($_SESSION['restrictionid']==5){ //Warehouse ?>
<input id="bt5" class="buthov" type="button" onclick="window.location='?page=po';" value="Purchase Order" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt2" class="buthov" type="button" onclick="window.location='?page=prod_maintenance';" value="Product Maintenance" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt4" class="buthov" type="button" onclick="window.location='?page=stockin';" value="StockIn Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=stockout';" value="StockOut Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="viewReport('./reports/prod_inventory<?=$_SESSION['repExtension']?>.php');" value="Product Inventory" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt7" class="buthov" type="button" onclick="viewReport('./reports/supplier_received.php');" value="Receiving Summary" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }else if($_SESSION['restrictionid']==9){ //Invoices ?>
<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Master Data" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Sales<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=dynamictbl&tblname=req_agent'>Sales Agent</a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_location'>Location</a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_employees'>Employees</a></li>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=prod_maintenance'>Product Maintenance<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_category'>Product Category<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<!--a href='#'>Book Group</a-->
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=dynamictbl&tblname=tbl_bank_account'>Bank Account</a>
						<a href='?page=dynamictbl&tblname=tbl_customers'>Debtors/Customers</a>
						<a href='?page=dynamictbl&tblname=tbl_supplier'>Suppliers</a>
						<a href='?page=dynamictbl&tblname=tbl_cost_center'>Cost Center</a>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=dynamic_invoicing&tbltype=sales_invoice';" value="Sales Invoice" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }else if($_SESSION['restrictionid']==10){ //Invoices ?>
<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }else{ //Admin credential here ?>
<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Master Data" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Sales<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=dynamictbl&tblname=req_agent'>Sales Agent</a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_location'>Location</a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_employees'>Employees</a></li>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=prod_maintenance'>Product Maintenance<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li><a href='?page=dynamictbl&tblname=tbl_category'>Product Category<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<!--a href='#'>Book Group</a-->
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=dynamictbl&tblname=tbl_journal_category'>Journals</a>
						<a href='?page=chart_of_account'>Chart Of Accounts</a>
						<a href='?page=dynamictbl&tblname=tbl_bank_account'>Bank Account</a>
						<a href='?page=dynamictbl&tblname=tbl_customers'>Debtors/Customers</a>
						<a href='?page=dynamictbl&tblname=tbl_supplier'>Suppliers</a>
						<a href='?page=dynamictbl&tblname=tbl_cost_center'>Cost Center</a>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Payroll<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=dynamictbl&tblname=tbl_employee'>Employees</a>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div>
<br/>
<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Transactions" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=po'>Purchase Order Entry</a>
						<a href='javascript:alert("Warehouse will be the one to received stocks...");'>Stock Transactions</a>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=gl'>Journal Entry</a>
						<hr/>
						<a href="?page=vouchering&defaulttype=CRJ">A/R Collection Entry</a>
						<a href='?page=vouchering&defaulttype=CDJ'>A/P Disbursement Entry</a>
						<hr/>
						<a href='#'>Bank Reconciliation</a>
						<hr/>
						<a href='?page=vouchering&begbal=true'>Balance Forwarded</a>
						<a href='#'>Financial Year Closing Date</a>
						<a href='#'>Transition To New Financial Year</a>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div>
<br/>
<hr/>
<input id="bt5" class="buthov" type="button" value="Commision Rep" onclick="viewCommReport();" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7||$_SESSION['restrictionid']==8){ //Accounting?>
	<?php if($_SESSION['restrictionid']==1){ ?>
	<input id="bt6" class="buthov" type="button" onclick="window.location='?page=payroll';" value="Payroll" style="height:40px;width:100%;float:left;"/>
	<br/>
	<?php } ?>
<input id="bt7" class="buthov" type="button" onclick="window.location='?page=quotation';" value="Quotation" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
<br/>
<hr/>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=dynamic_invoicing&tbltype=sales_invoice';" value="Sales Invoice" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt11" class="buthov" type="button" onclick="window.location='?page=dynamic_invoicing&tbltype=tra';" value="TRA" style="height:40px;width:100%;float:left;"/>
<br/>
<br/>
<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
<br/>
<hr/>
<input id="bt5" class="buthov" type="button" onclick="window.location='?page=po';" value="Purchase Order" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt4" class="buthov" type="button" onclick="window.location='?page=stockin';" value="StockIn Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=stockout';" value="StockOut Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<!--input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Tenant List" style="height:40px;width:100%;float:left;"/>
<br/-->
<?php }
} ?>


<!--input id="bt14" class="buthov" type="button" value="DB Maintenance" onclick="window.location='?page=dbmaintenance';" style="height:40px;width:100%;float:left;"/>
<br/-->

