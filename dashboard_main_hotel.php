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
<hr/>
<input id="bt7" class="buthov" type="button" onclick="window.location='?page=roomreservation&iframe=true';" value="Room Reservation" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt2" class="buthov" type="button" value="POS Sales" onclick="window.location='?page=sales';" style="height:40px;width:100%;float:left;"/>
<br/>
<div class="dropdown">
	<input id="bt10" class="buthov" type="button" value="ServerReading" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li><a href='javascript:clickDialog("dialogbox",400,150,"startServerReading","Initialize ServerReading");'><img src="./images/bullet.gif"/>&nbsp;Initialize ServerReading</a></li>
			<li><a href="javascript:loginPermission('reading_end_server');"><img src="./images/bullet.gif"/>&nbsp;End ServerReading</a></li>
		</ul>
	</div>
</div>
<br/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>
<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7||$_SESSION['restrictionid']==8){ //Accounting?>
	<?php if($_SESSION['restrictionid']==1){ ?>
	<input id="bt6" class="buthov" type="button" onclick="window.location='?page=payroll';" value="Payroll" style="height:40px;width:100%;float:left;"/>
	<br/>
<?php } ?>
<hr/>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
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
<?php }
} ?>


<!--input id="bt14" class="buthov" type="button" value="DB Maintenance" onclick="window.location='?page=dbmaintenance';" style="height:40px;width:100%;float:left;"/>
<br/-->

