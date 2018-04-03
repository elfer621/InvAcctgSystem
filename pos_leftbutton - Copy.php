<? if($view==2){ ?>
	<input id="bt14" class="buthov" type="button" value="TRA/DR Num" onclick="input_tra()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"/>
	<br/>
	<input id="bt5" class="buthov" type="button" value="More Info" onclick="input_moreinfo()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"/>
	<br/>
	<button id="bt13" class="buthov" type="button" onclick="show_custlist()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">C</span>ustomer Name</button>
	<br/>
	<button id="bt4" class="buthov" type="button" onclick="addNewProd()" style="height:30px;width:100%;float:left;font-size:11px;">Prod Maintenance</button>
	<br/>
	<button id="bt8" class="buthov" type="button" onclick="previewToSession();" style="height:30px;width:100%;float:left;font-size:11px;">Preview Receipt</button>
	<br/>
	<button id="bt2" class="buthov" type="button" onclick="printWS();" style="height:30px;width:100%;float:left;font-size:11px;">Print WS</button>
	<br/>
	<button id="bt10" class="buthov" type="button" onclick="changePass()" style="height:30px;width:100%;float:left;font-size:10px;">Change <span style="font-weight:bold;text-decoration:underline;">P</span>ass</button>
	<br/>
	<input id="bt6" class="buthov" type="button" value="Main" onclick="window.location='index.php';" style="float:right;height:30px;width:100%;float:left;"/>
	<br/>
<? } ?>
<? if($view==3){ ?>
	<button id="bt13" class="buthov" type="button" onclick="show_custlist()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">C</span>ustomer Name</button>
	<br/>
	<button id="bt4" class="buthov" type="button" onclick="addNewProd()" style="height:30px;width:100%;float:left;font-size:11px;">Prod Maintenance</button>
	<br/>
	<button id="bt3" class="buthov" type="button" onclick="reprintReceipt();" style="height:30px;width:100%;float:left;font-size:11px;">Re-Print Receipt</button>
	<br/>
	<button id="bt8" class="buthov" type="button" onclick="previewToSession();" style="height:30px;width:100%;float:left;font-size:11px;">Preview Receipt</button>
	<br/>
	<input id="bt2" class="buthov" type="button" value="Cash Out (F9)" onclick="cashOut()" style="height:30px;width:100%;float:left;"/>
	<br/>
	<button id="bt5" class="buthov" type="button" onclick="cashDetails()" style="height:30px;width:100%;float:left;">C<span style="font-weight:bold;text-decoration:underline;">a</span>sh Details</button>
	<br/>
	<button id="bt10" class="buthov" type="button" onclick="changePass()" style="height:30px;width:100%;float:left;font-size:10px;">Change <span style="font-weight:bold;text-decoration:underline;">P</span>ass</button>
	<br/>
	<input id="bt6" class="buthov" type="button" value="Main" onclick="window.location='index.php';" style="float:right;height:30px;width:100%;float:left;"/>
	<br/>
<? } ?>
<? if($view==1||$view==4){ ?>
	<?php if($view==4){ ?>
	<button id="bt13" class="buthov" type="button" onclick="show_custlist()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">C</span>ustomer Name</button>
	<br/>
	<?php } ?>
	<?php if($transtype!="order"){ ?>
	<button id="bt4" class="buthov" type="button" onclick="reprintReceipt();" style="height:30px;width:100%;float:left;font-size:11px;">Vie<span style="font-weight:bold;text-decoration:underline;">w</span> Receipt</button>
	<br/>
	<button id="bt3" class="buthov" type="button" onclick="addNewProd()" style="height:30px;width:100%;float:left;font-size:11px;">Prod Maintenance</button>
	<br/>
	<input id="bt8" class="buthov" type="button" value="Order List (F1)" onclick="order_list2()" style="float:right;height:30px;width:100%;float:left;font-size:12px;"/>
	<br/>
	<input id="bt2" class="buthov" type="button" value="Cash Out (F9)" onclick="cashOut()" style="height:30px;width:100%;float:left;"/>
	<br/>
	<?php if($view!=4){ ?>
	<input id="bt9" class="buthov" type="button" value="Suspend(F10)" onclick="suspend()" style="height:30px;width:100%;float:left;"/>
	<br/>
	<?php } ?>
	<button id="bt5" class="buthov" type="button" onclick="cashDetails()" style="height:30px;width:100%;float:left;">C<span style="font-weight:bold;text-decoration:underline;">a</span>sh Details</button>
	<br/>
	<button id="bt15" class="buthov" type="button" onclick="shoppers_card()" style="height:30px;width:100%;float:left;font-size:10px;">Shoppers <span style="font-weight:bold;text-decoration:underline;">C</span>ard</button>
	<br/>
	<?php } ?>
	<button id="bt10" class="buthov" type="button" onclick="changePass()" style="height:30px;width:100%;float:left;font-size:10px;">Change <span style="font-weight:bold;text-decoration:underline;">P</span>ass</button>
	<br/>
	<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="float:right;height:30px;width:100%;float:left;"/>
	<br/>
<? } ?>
<? if($view==5){ //LizGan Button ?>
	<input id="bt3" class="buthov" type="button" value="Add NewProd" onclick="prodAdd();" style="height:40px;width:150px;"/>
	<br/>
	<button id="bt13" class="buthov" type="button" onclick="show_studentlist();"  style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">S</span>tudent Name</button>
	<br/>
	<button id="bt12" class="buthov" type="button" onclick="changeCategory()"  style="float:right;height:30px;width:100%;float:left;font-size:11px;">Change Category</button>
	<br/>
	<button id="bt11" class="buthov" type="button" onclick="packages()" style="height:30px;width:100%;float:left;">Tracks</button>
	<br/>
	<!--button id="bt4" class="buthov" type="button" onclick="addNewProd()" style="height:30px;width:100%;float:left;font-size:11px;">Prod Maintenance</button>
	<br/-->
	<button id="bt3" class="buthov" type="button" onclick="reprintReceipt();" style="height:30px;width:100%;float:left;font-size:11px;">Re-Print Receipt</button>
	<br/>
	<!--button id="bt8" class="buthov" type="button" onclick="previewToSession();" style="height:30px;width:100%;float:left;font-size:11px;">Preview Receipt</button-->
	<br/>
	<button id="bt5" class="buthov" type="button" onclick="cashDetails()" style="height:30px;width:100%;float:left;">C<span style="font-weight:bold;text-decoration:underline;">a</span>sh Details</button>
	<br/>
	<button id="bt10" class="buthov" type="button" onclick="changePass()" style="height:30px;width:100%;float:left;font-size:10px;">Change <span style="font-weight:bold;text-decoration:underline;">P</span>ass</button>
	<br/>
	
	<?php if($_SESSION['restrictionid']==1){ ?>
		<input id="bt6" class="buthov" type="button" value="Main" onclick="window.location='index.php';" style="float:right;height:30px;width:100%;float:left;"/>
		<br/>
	<?php }else{ ?>
		<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="float:right;height:30px;width:100%;float:left;"/>
		<br/>
	<?php } ?>
<? } ?>
<? if($view==8){  //Clinic Lab Buttons ?>
	<?php if($_SESSION['restrictionid']!=3){ ?>
		<input id="bt8" class="buthov" type="button" value="Patient Queue (F1)" onclick="order_list2()" style="float:right;height:30px;width:100%;float:left;font-size:12px;"/>
		<br/>
	<?php } ?>
	<button id="bt13" class="buthov" type="button" onclick="show_custlist()" style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">C</span>ustomer Name</button>
	<br/>
	<input id="bt3" class="buthov" type="button" value="Add NewProd" onclick="prodAdd();" style="height:40px;width:150px;"/>
	<br/>
	<button id="bt13" class="buthov" type="button" onclick="show_patientinfo();"  style="float:right;height:30px;width:100%;float:left;font-size:11px;"><span style="font-weight:bold;text-decoration:underline;">P</span>atient</button>
	<br/>
	<button id="bt11" class="buthov" type="button" onclick="packages()" style="height:30px;width:100%;float:left;">Packages</button>
	<br/>
	<button id="bt3" class="buthov" type="button" onclick="reprintReceipt();" style="height:30px;width:100%;float:left;font-size:11px;">Re-Print Receipt</button>
	<br/>
	<button id="bt5" class="buthov" type="button" onclick="cashDetails()" style="height:30px;width:100%;float:left;">C<span style="font-weight:bold;text-decoration:underline;">a</span>sh Details</button>
	<br/>
	<button id="bt10" class="buthov" type="button" onclick="changePass()" style="height:30px;width:100%;float:left;font-size:10px;">Change <span style="font-weight:bold;text-decoration:underline;">P</span>ass</button>
	<br/>
	<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==3){ ?>
		<input id="bt6" class="buthov" type="button" value="Main" onclick="window.location='index.php';" style="float:right;height:30px;width:100%;float:left;"/>
		<br/>
	<?php }else{ ?>
		<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="float:right;height:30px;width:100%;float:left;"/>
		<br/>
	<?php } ?>
<? } ?>