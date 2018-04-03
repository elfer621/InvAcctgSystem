<div class="dropdown">
	<input id="bt1" class="buthov" type="button" value="Master Data" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li class="dropdown2">
				<a href='#'>&nbsp;Sales<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<!--a href='#'>Mode of Payment</a>
						<a href='#'>Price Levels</a>
						<a href='#'>Item Prices</a>
						<a href='#'>Sales Representative</a>
						<a href='#'>Courses</a>
						<a href='#'>Prospectus</a>
						<a href='#'>Publishers</a>
						<a href='#'>Teachers</a>
						<a href='#'>Schedules</a>
						<a href='#'>Class List</a>
						<a href='#'>Student</a-->
						<hr/>
						<li class="dropdown3">
							<a href='#'>Point of Sale</a>
							<div class="dropdown-content3">
								<a href='?page=sales'>Sales Stations</a>
							</div>
						</li>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Inventory<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<!--a href='#'>Warehouses</a>
						<a href='#'>Book Group</a-->
						<!--a href='?page=prod_maintenance'>Books</a-->
						<li class="dropdown3">
							<a href='#'>Books<img src="./images/bullet.gif" style="float:right;"/></a>
							<div class="dropdown-content3">
								<ul>
									<li><a href='?page=prod_maintenance'>Product Maintenance<img src="./images/bullet.gif" style="float:right;"/></a></li>
									<li><a href='?page=dynamictbl&tblname=tbl_product_name&join=left join (select sku_id skuid,price,cost from tbl_barcodes) b on tbl_product_name.sku_id=b.skuid&join_fields=price,cost'>
										Manage Records<img src="./images/bullet.gif" style="float:right;"/>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<a href='?page=dynamictbl&tblname=tbl_subject_name'>Subject Name</a>
					</ul>
				</div>
			</li>
			<li class="dropdown2">
				<a href='#'>&nbsp;Accounting<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<!--a href='#'>Main Account Group</a>
						<a href='#'>Sub Account Groups</a>
						<a href='#'>Account Groups</a-->
						<a href='?page=dynamictbl&tblname=tbl_journal_category'>Journals</a>
						<a href='?page=chart_of_account'>Chart Of Accounts</a>
						<!--a href='#'>Business Units</a>
						<a href='#'>Cost Center</a>
						<a href='#'>Projects</a>
						<a href='#'>EWT Types</a>
						<a href='#'>Terms of Payment</a-->
						<a href='?page=dynamictbl&tblname=tbl_bank_account'>Bank Account</a>
						<a href='?page=dynamictbl&tblname=tbl_customers'>Debtors/Customers</a>
						<a href='?page=dynamictbl&tblname=tbl_supplier&join_fields=ave_voldisc&join=left join (select supplier_id,sum(volume_discount)/sum(total) ave_voldisc from tbl_po_header group by supplier_id) b on tbl_supplier.id=b.supplier_id'>Creditor/Suppliers</a>
						<a href='?page=dynamictbl&tblname=tbl_prod_group'>Product Group</a>
						<!--a href='#'>Currency</a-->
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
			<!--li class="dropdown2">
				<a href='#'>&nbsp;Point of Sales<img src="./images/bullet.gif" style="float:right;"/></a>
				<div class="dropdown-content2">
					<ul>
						<a href='#'>Start New Sales Day</a>
						<a href='#'>Edit/End Sales Day</a>
						<hr/>
						<a href='#'>Start New Cashier Period</a>
						<a href='#'>Edit/End Cashier Period</a>
						<hr/>
						<a href='#'>Cashier POS Edit</a>
						<a href='#'>Cashier POS Edit (Header)</a>
					</ul>
				</div>
			</li-->
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
						<a href='#'>Consignment Date Report</a>
						<a href='#'>Item Transaction Card</a>
						<a href='./reports/prod_inventory_allbranch.php'  target="_blank">Inventory Valuation</a>
						<a href='javascript:showNewDataFilterNotJqgrid(62);'>Inventory Transaction Summary</a>
						<a href='#'>Inventory Status Summary</a>
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
						<li><a href='?page=jqgrid&refid=51&report_type=AccountReceivable'>Accounts Receivable<img src="./images/bullet.gif" style="float:right;"/></a></li>
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
			<?php /*foreach($rec as $key=>$val){ ?>
				<li><a href='javascript:showDataFilter(<?=$val['id']?>);'><img src="./images/prev.png" style="width:15px;height:15px;"/>&nbsp;<?=$val['title']?></a></li>
			<?php }*/ ?>
		</ul>
	</div>
</div>
<br/>
<hr/>
<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
<br/>

<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==6){ //Operation ?>
<!--input id="bt1" class="buthov" type="button" value="Product Maintenance" onclick="window.location='?page=prod_maintenance';" style="height:40px;width:100%;float:left;"/>
<br/-->
<input id="bt11" class="buthov" type="button" onclick="window.location='?page=package_create';" value="Create Packages" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt5" class="buthov" type="button" onclick="window.location='?page=po';" value="Purchase Order" style="height:40px;width:100%;float:left;"/>
<br/>
<?php } ?>
<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7||$_SESSION['restrictionid']==8){ //Accounting?>
<?php if($view==5){ ?>
<!--input id="bt11" class="buthov" type="button" onclick="window.location='?page=package_create';" value="Create Packages" style="height:40px;width:100%;float:left;"/>
<br/-->
<input id="bt3" class="buthov" type="button"  value="Commission" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt4" class="buthov" type="button" onclick="window.location='?page=import_files';" value="Import EDP Data" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt12" class="buthov" type="button" onclick="window.location='?page=persupplier';" value="Inv LookUp" style="height:40px;width:100%;float:left;"/>
<br/>
<?php } ?>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
<!--input id="bt9" class="buthov" type="button" onclick="window.location='?page=gl';" value="General Ledger" style="height:40px;width:100%;float:left;"/-->
<br/>
<!--div class="dropdown">
	<input id="bt10" class="buthov" type="button" value="Journalizing" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<a href="javascript:window.location='?page=journalentry';"><img src="./images/bullet.gif"/>&nbsp;Entry</a>
		<a href="javascript:window.location='?page=chart_of_account';"><img src="./images/bullet.gif"/>&nbsp;Chart Of Account</a>
	</div>
</div>
<br/-->
<?php } ?>
<?php if($_SESSION['settings']['connection_type']=="multiple"){ ?>
<input id="bt2" class="buthov" type="button" value="View Branches" onclick="window.location='?page=view_branches';" style="height:40px;width:100%;float:left;"/>
<br/>
<?php } ?>

<!--input id="bt14" class="buthov" type="button" value="DB Maintenance" onclick="window.location='?page=dbmaintenance';" style="height:40px;width:100%;float:left;"/>
<br/-->

