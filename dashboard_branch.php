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
	<input id="bt4" class="buthov" type="button" value="Stock In" onclick="window.location='?page=stockin';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt3" class="buthov" type="button" value="Stock Out" onclick="window.location='?page=stockout';" style="height:40px;width:100%;float:left;"/>
	<br/>
	
	<?php if($view==2){ ?>
	<input id="bt6" class="buthov" type="button" value="Commision Rep" onclick="viewCommReport();" style="height:40px;width:100%;float:left;"/>
	<br/>
	<?php } ?>
	<input id="bt1" class="buthov" type="button" value="Product Maintenance" onclick="window.location='?page=prod_maintenance';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt12" class="buthov" type="button" onclick="window.location='?page=persupplier';" value="Inv LookUp" style="height:40px;width:100%;float:left;"/>
	<br/>
	<!--input id="bt13" class="buthov" type="button" onclick="show_custlist()" value="Customer List" style="height:40px;width:100%;float:left;"/>
	<br/-->
	<input id="bt16" class="buthov" type="button" onclick="window.location='?page=reports';" value="Reports" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt17" class="buthov" type="button" onclick="viewRecordsPackages();" value="Tracks" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt12" class="buthov" type="button" value="Manage User" onclick="window.location='?page=manager_user';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<!--input id="bt14" class="buthov" type="button" value="DB Maintenance" onclick="window.location='?page=dbmaintenance';" style="height:40px;width:100%;float:left;"/>
	<br/-->
<? }else{ ?>
	<input id="bt2" class="buthov" type="button" value="POS Sales" onclick="window.location='?page=pos';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt4" class="buthov" type="button" value="Stock In" onclick="window.location='?page=stockin';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt3" class="buthov" type="button" value="Stock Out" onclick="window.location='?page=stockout';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt17" class="buthov" type="button" value="Stock Transfer" onclick="window.location='?page=stocktransfer';" style="height:40px;width:100%;float:left;"/>
	<br/>
	<input id="bt13" class="buthov" type="button" onclick="show_custlist()" value="Customer List" style="height:40px;width:100%;float:left;"/>
	<br/>
<? } ?>
<div class="dropdown">
	<input id="bt13" class="buthov" type="button" value="Records Viewing" style="height:40px;width:100%;float:left;"/>
	<?php $rec = $db->resultArray("*","tbl_master_details",""); ?>
	<div class="dropdown-content">
		<ul>
		<?php foreach($rec as $key=>$val){ ?>
			<li><a href='javascript:showDataFilter(<?=$val['id']?>);'><img src="./images/bullet.gif"/>&nbsp;<?=$val['title']?></a></li>
		<?php } ?>
		</ul>
	</div>
</div>
<br/>
<div class="dropdown">
	<input id="bt8" class="buthov" type="button" value="DailyTask" style="height:40px;width:100%;float:left;"/>
	<div class="dropdown-content">
		<ul>
			<li><a href='javascript:updateItems();'><img src="./images/bullet.gif"/>&nbsp;Update Items</a></li>
			<li><a href="javascript:SendInvToAdmin();"><img src="./images/bullet.gif"/>&nbsp;SendInvToAdmin</a></li>
		</ul>
	</div>
</div>
<br/>
<input id="bt8" class="buthov" type="button" onclick="window.location='?page=vouchering';" value="Journal Ent" style="height:40px;width:100%;float:left;"/>
<!--input id="bt8" class="buthov" type="button" value="Update Items" onclick="updateItems();" style="height:40px;width:100%;float:left;"/>
<br/>
<input id="bt9" class="buthov" type="button" value="SendInvToAdmin" onclick="SendInvToAdmin();" style="height:40px;width:100%;float:left;"/>
<br/-->
<script>
function viewRecordsPackages(){
	var urls = getUrl();
	$("#prodlist").html("");
	clickDialog('prodlist',1000,550,'packageslist','Package List',urls);
	jQuery.tableNavigation();
	$("#search_prodname").focus();
}

</script>