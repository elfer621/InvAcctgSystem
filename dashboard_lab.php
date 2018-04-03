<? if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==6){ //admin rights ?> 
	<?php if($db->constatus!='lizgan_warehouse'){ ?>
		<input id="bt2" class="buthov" type="button" value="POS Sales" onclick="window.location='?page=sales';" style="height:40px;width:100%;float:left;"/>
		<br/>
		<input id="bt5" class="buthov" type="button" value="Sales History" onclick="window.location='?page=saleshist';" style="height:40px;width:100%;float:left;"/>
		<br/>
		<input id="bt7" class="buthov" type="button" value="Search OR" onclick="window.location='?page=allor';" style="height:40px;width:100%;float:left;"/>
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
	<?php } ?>
	<button id="bt13" class="buthov" type="button" onclick="show_patientinfo();"  style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">P</span>atient</button>
	<br/>
	<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt8" class="buthov" type="button" value="Input Lab Results" onclick="window.location='?page=dynamic_lab';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt11" class="buthov" type="button" value="Procedure Status" onclick="window.location='?page=procedure_status';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Procedure)" onclick="window.location='?page=queue';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Results)" onclick="window.location='?page=queue&queuetype=results';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Printing)" onclick="window.location='?page=queue&queuetype=printing';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<!--input id="bt7" class="buthov" type="button" value="Information" onclick="window.location='?page=demographic_input';" style="height:40px;width:100%;float:left;"/>
	<br/-->
	<input id="bt5" class="buthov" type="button" onclick="window.location='?page=po';" value="Purchase Order" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt4" class="buthov" type="button" value="Stock In" onclick="window.location='?page=stockin';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt3" class="buthov" type="button" value="Stock Out" onclick="window.location='?page=stockout';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt1" class="buthov" type="button" value="Product Maintenance" onclick="window.location='?page=prod_maintenance';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt17" class="buthov" type="button" onclick="viewRecordsPackages();" value="Packages" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt11" class="buthov" type="button" onclick="window.location='?page=package_create';" value="Create Packages" style="height:40px;width:100%;float:left;"/>
	<br/>
	<!--input id="bt4" class="buthov" type="button" onclick="window.location='?page=import_files';" value="Import Data" style="height:40px;width:100%;float:left;"/>
	<br/-->
	
	<!--input id="bt14" class="buthov" type="button" value="DB Maintenance" onclick="window.location='?page=dbmaintenance';" style="height:40px;width:100%;float:left;"/>
	<br/-->
	<br/>
	<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
	<br/>
	<div class="dropdown">
		<input id="bt3" class="buthov" type="button" value="DataSync" style="height:40px;width:100%;float:left;"/>
		<div class="dropdown-content">
			<ul>
				<li><a href='javascript:dataSync(1);'><img src="./images/bullet.gif"/>&nbsp;Mobile 1 to Server</a></li>
				<li><a href="javascript:dataSync(2);"><img src="./images/bullet.gif"/>&nbsp;Mobile 2 to Server</a></li>
				<li><a href='javascript:dataSync(3);'><img src="./images/bullet.gif"/>&nbsp; Server to Mobile 1</a></li>
				<li><a href="javascript:dataSync(4);"><img src="./images/bullet.gif"/>&nbsp;Server to Mobile 2</a></li>
			</ul>
		</div>
	</div>
	<br/>
<? }elseif($_SESSION['restrictionid']==9){ //Laboratory ?>
	<input id="bt9" class="buthov" type="button" value="Queue (Procedure)" onclick="window.location='?page=queue';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Results)" onclick="window.location='?page=queue&queuetype=results';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Printing)" onclick="window.location='?page=queue&queuetype=printing';" style="height:40px;width:100%;float:left;"/>
	<br/>
<? }elseif($_SESSION['restrictionid']==10){ //Ambulatory ?>
	<input id="bt7" class="buthov" type="button" value="Information (1)" onclick="window.location='?page=demographic_input';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Procedure) (2)" onclick="window.location='?page=queue';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Results)" onclick="window.location='?page=queue&queuetype=results';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt9" class="buthov" type="button" value="Queue (Printing)" onclick="window.location='?page=queue&queuetype=printing';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt13" class="buthov" type="button" onclick="window.location='?page=tenant_list';" value="Customer Details" style="height:40px;width:100%;float:left;"/>
	<br/>
	<button id="bt13" class="buthov" type="button" onclick="show_patientinfo();"  style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">P</span>atient</button>
	<br/>
	<input id="bt17" class="buthov" type="button" onclick="viewRecordsPackages();" value="Packages" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt11" class="buthov" type="button" onclick="window.location='?page=package_create';" value="Create Packages" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt4" class="buthov" type="button" onclick="window.location='?page=import_files';" value="Import Data" style="height:40px;width:100%;float:left;"/>
	<br/>
<? }else{ ?>
	<button id="bt13" class="buthov" type="button" onclick="show_patientinfo();"  style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">P</span>atient</button>
	<br/>
	<input id="bt2" class="buthov" type="button" value="POS Sales" onclick="window.location='?page=order';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt1" class="buthov" type="button" value="Product Maintenance" onclick="window.location='?page=prod_maintenance';" style="height:40px;width:100%;float:left;"/>
	<br/>
<? } ?>
<?php if($_SESSION['settings']['connection_type']=="multiple"){ ?>
	<input id="bt2" class="buthov" type="button" value="View Branches" onclick="window.location='?page=view_branches';" style="height:40px;width:100%;float:left;"/>
	<br/>
<?php } ?>
<script>
function dataSync(ref){
	clickDialogUrl("dialogbox3",600,300,"./content/dataSync.php?execute=modal&refid="+ref,"DataSync")
}


</script>