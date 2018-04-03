<?php if($_SESSION['restrictionid']==5){ //Warehouse ?>
<input id="bt2" class="buthov" type="button" onclick="window.location='?page=prod_maintenance';" value="Product Maintenance" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt4" class="buthov" type="button" onclick="window.location='?page=stockin';" value="StockIn Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=stockout';" value="StockOut Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="viewReport('./reports/prod_inventory_tkc.php');" value="Product Inventory" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }elseif($_SESSION['restrictionid']==8){ //Audit ?>
<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Master Data" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=dynamictbl&tblname=tbl_journal_category'>Journals</a>
						<a href='?page=chart_of_account'>Chart Of Accounts</a>
						<a href='?page=dynamictbl&tblname=tbl_bank_account'>Bank Account</a>
						<a href='?page=dynamictbl&tblname=tbl_customers'>Debtors/Customers</a>
						<a href='?page=dynamictbl&tblname=tbl_supplier'>Suppliers</a>
						<!--a href='#'>Currency</a-->
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
<input id="bt2" class="buthov" type="button" onclick="window.location='?page=prod_maintenance';" value="Product Maintenance" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="viewReport('./reports/prod_inventory_tkc.php');" value="Product Inventory" style="height:40px;width:100%;float:left;"/>
<br/>
<hr/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }elseif($_SESSION['restrictionid']==7){ //Accounting ?>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Cash/Check Voucher" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt3" class="buthov" type="button" onclick="window.location='?page=dynamic_invoicing&tbltype=sales_invoice';" value="Sales Invoice" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="window.location='?page=po';" value="Purchase Order" style="height:40px;width:100%;float:left;"/>
<br/>
<?php }elseif($_SESSION['restrictionid']==4){ //supervisor ?>
<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Master Data" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=dynamictbl&tblname=tbl_journal_category'>Journals</a>
						<a href='?page=chart_of_account'>Chart Of Accounts</a>
						<a href='?page=dynamictbl&tblname=tbl_bank_account'>Bank Account</a>
						<a href='?page=dynamictbl&tblname=tbl_customers'>Debtors/Customers</a>
						<a href='?page=dynamictbl&tblname=tbl_supplier'>Suppliers</a>
						<!--a href='#'>Currency</a-->
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
<input id="bt2" class="buthov" type="button" onclick="window.location='?page=prod_maintenance';" value="Product Maintenance" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="viewReport('./reports/prod_inventory_tkc.php');" value="Product Inventory" style="height:40px;width:100%;float:left;"/>
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
						<!--li class="dropdown3">
							<a href='#'>Point of Sale</a>
							<div class="dropdown-content3">
								<a href='#'>Sales Stations</a>
							</div>
						</li-->
						<li><a href='?page=sales'>Point of Sale</a></li>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=prod_maintenance'>Product Maintenance<img src="./images/bullet.gif" style="float:right;"/></a></li>
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
						<!--a href='#'>Currency</a-->
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
<div class="dropdown">
	<input id="bt13" class="buthov" type="button" value="Overview/Reports" style="height:40px;width:100%;float:left;"/>
	<?php $rec = $db->resultArray("*","tbl_master_details",""); ?>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Point of Sales<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='?page=jqgrid&refid=1'>POS Invoice Reveiw/Summary</a>
						<a href='?page=jqgrid&refid=1'>POS Invoice Payment Review/Summary</a>
						<a href='?page=jqgrid&refid=1'>POS Review by Header</a>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='javascript:window.location="?page=po";'>Purchase Order Review/Summary</a>
						<a href='./reports/prod_inventory_allbranch.php'  target="_blank">Inventory Valuation</a>
						<a href='javascript:showNewDataFilterNotJqgrid(62);'>Inventory Transaction Summary</a>
						<a href='?page=persupplier'>Inventory Status Summary By Warehouse</a>
						<a href='./reports/zero_inventory.php'>Variance Report</a>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<li><a href='?page=journalentry'>Ledger<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li><a href='./reports/cust_trans.php'  target="_blank">A/R Collection<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li><a href='./reports/account_receivable.php'  target="_blank">Accounts Receivable<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li><a href='?page=jqgrid&refid=52&report_type=AccountPayable'>Accounts Payable<img src="./images/bullet.gif" style="float:right;"/></a></li>
						<li class="dropdown3">
							<a href='#'>Financial Statements<img src="./images/bullet.gif" style="float:right;"/></a>
							<div class="dropdown-content3">
								<ul>
									<li><a href='javascript:showNewDataFilterNotJqgrid(65);'>Income Statement<img src="./images/bullet.gif" style="float:right;"/></a></li>
									<li><a href='javascript:showNewDataFilterNotJqgrid(66);'>Balance Sheet<img src="./images/bullet.gif" style="float:right;"/></a></li>
									<li><a href='javascript:showNewDataFilterNotJqgrid(64);'>Trial Balance<img src="./images/bullet.gif" style="float:right;"/></a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div>
<br/>
<hr/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7||$_SESSION['restrictionid']==8){ //Accounting?>
	<?php if($_SESSION['restrictionid']==1){ ?>
	<input id="bt6" class="buthov" type="button" onclick="window.location='?page=payroll';" value="Payroll" style="height:40px;width:100%;float:left;"/>
	<br/>
	<?php } ?>
<input id="bt7" class="buthov" type="button" onclick="window.location='?page=quotation';" value="Quotation" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Voucher" style="height:40px;width:100%;float:left;"/>
<br/>
<hr/>
<input id="bt2" class="buthov" type="button" onclick="window.location='?page=dynamic_invoicing&tbltype=billing_statement';" value="Billing Statement" style="height:40px;width:100%;float:left;"/>
<br/>
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

