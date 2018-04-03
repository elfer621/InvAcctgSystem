<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
<fieldset style="padding:20px;min-height:400px;">
	<legend> Branches </legend>
	<input id="bt4" class="buthov" type="button" onclick="window.location='?connect=mobile1';" value="Mobile 1" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<input id="bt5" class="buthov" type="button" onclick="window.location='?connect=mobile2';" value="Mobile 2" style="height:40px;width:150px;float:left;margin-right:10px;"/>
</fieldset>
<?php }else{ ?>
<fieldset style="padding:20px;min-height:400px;">
	<legend> Branches </legend>
	<input id="bt4" class="buthov" type="button" onclick="window.location='?connect=uclm';" value="UC-LM" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<input id="bt5" class="buthov" type="button" onclick="window.location='?connect=ucmambaling';" value="UC-Mambaling" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<input id="bt6" class="buthov" type="button" onclick="window.location='?connect=ucmain';" value="UC-Main" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<input id="bt7" class="buthov" type="button" onclick="window.location='?connect=ucbanilad';" value="UC-Banilad" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<div style="clear:both;height:10px;"></div>
	<input id="bt8" class="buthov" type="button" onclick="window.location='?connect=warehouse';" value="Warehouse" style="height:40px;width:150px;float:left;margin-right:10px;"/>
</fieldset>
<?php } ?>