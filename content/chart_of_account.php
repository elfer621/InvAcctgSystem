<?php
if($_POST){
	$sql = $db->genSqlInsert($_POST,"tbl_chart_of_account");
	// $sql = "insert into tbl_chart_of_account (account_code,account_desc,account_group,account_type,report_type,default_side,subsidiary,cash_in_bank) values 
	// ('".$_REQUEST['account_code']."','".$_REQUEST['account_desc']."','".$_REQUEST['account_group']."','".$_REQUEST['account_type']."','".$_REQUEST['account_type']."','".$_REQUEST['default_side']."','".$_REQUEST['subsidiary']."','".$_REQUEST['cash_in_bank']."') 
	// on duplicate key update account_desc=values(account_desc),account_group=values(account_group),account_type=values(account_type),report_type=values(report_type),default_side=values(default_side),subsidiary=values(subsidiary),cash_in_bank=values(cash_in_bank)";
	$qry = mysql_query($sql);
	if(!$qry){
		echo mysql_error();
	}else{
		echo "<script>$(document).ready(function(){alertMsg('Save Successfully...');});</script>";
	}
}
if($_REQUEST['refid']){
	$info = $db->getWHERE("*","tbl_chart_of_account","where account_code='{$_REQUEST['refid']}'");
}
?>
<div style="height:600px;background-color:white;padding:10px;">
	<fieldset>
		<legend>Manage</legend>
		<form name="frm" method="post" style="float:left;margin-right:20px;">
		<div style="float:left;margin-right:10px;">
			<div style="float:left;margin-right:5px;width:100px;">Account Code</div>
			<input type="text" style="float:left;width:280px;" name="account_code" value="<?=$info['account_code']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Account Desc</div>
			<input type="text" style="float:left;width:280px;" name="account_desc" value="<?=$info['account_desc']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Sub Account</div>
			<select name="sub_account" style="float:left;width:280px;">
				<option value="">Select</option>
				<?php $sa=$db->resultArray("*","tbl_chart_of_account_subaccount",""); 
				foreach($sa as $key=>$val){
					echo "<option ".($info['sub_account']==$val['sub_account']?"selected":"")." value='".$val['sub_account']."'>".$val['sub_account']."</option>";
				}
				?>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Sub Account Group</div>
			<select name="sub_account_group" style="float:left;width:280px;">
				<option value="">Select</option>
				<?php $sa=$db->resultArray("*","tbl_chart_of_account_sub_account_group",""); 
				foreach($sa as $key=>$val){
					echo "<option ".($info['sub_account_group']==$val['sub_account_group']?"selected":"")." value='".$val['sub_account_group']."'>".$val['sub_account_group']."</option>";
				}
				?>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Account Group</div>
			<select name="account_group" style="float:left;width:280px;">
				<option value="">Select</option>
				<option <?=$info['account_group']=="ASSET"?"selected":""?> value="ASSET">ASSET</option>
				<option <?=$info['account_group']=="LIABILITIES"?"selected":""?> value="LIABILITIES">LIABILITIES</option>
				<option <?=$info['account_group']=="CAPITAL"?"selected":""?> value="CAPITAL">CAPITAL</option>
				<option <?=$info['account_group']=="SALES"?"selected":""?> value="SALES">SALES</option>
				<option <?=$info['account_group']=="COST OF SALES"?"selected":""?> value="COST OF SALES">COST OF SALES</option>
				<option <?=$info['account_group']=="EXPENSES"?"selected":""?> value="EXPENSES">EXPENSES</option>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Account Type</div>
			<select name="account_type" style="float:left;width:280px;">
				<option value="">Select</option>
				<option <?=$info['account_type']=="EXP"?"selected":""?> value="EXP">EXP</option>
				<option <?=$info['account_type']=="AST"?"selected":""?> value="AST">AST</option>
				<option <?=$info['account_type']=="LIA"?"selected":""?> value="LIA">LIA</option>
				<option <?=$info['account_type']=="EQU"?"selected":""?> value="EQU">EQU</option>
				<option <?=$info['account_type']=="REV"?"selected":""?> value="REV">REV</option>
				<option <?=$info['account_type']=="COS"?"selected":""?> value="COS">COS</option>
			</select>
			<div style="clear:both;height:5px;"></div>
		</div>
		<div style="float:right;">
			<div style="float:left;margin-right:5px;width:100px;">Report Type</div>
			<select name="report_type" style="float:left;width:180px;">
				<option value="">Select</option>
				<option <?=$info['report_type']=="PNL"?"selected":""?> value="PNL">PNL</option>
				<option <?=$info['report_type']=="BS"?"selected":""?> value="BS">BS</option>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Default Side</div>
			<select name="default_side" style="float:left;width:180px;">
				<option value="">Select</option>
				<option <?=$info['default_side']=="D"?"selected":""?> value="D">DR</option>
				<option <?=$info['default_side']=="C"?"selected":""?> value="C">CR</option>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Subsidiary</div>
			<select name="subsidiary" style="float:left;width:180px;">
				<option value="">Select</option>
				<option <?=$info['subsidiary']=="Y"?"selected":""?> value="Y">YES</option>
				<option <?=$info['subsidiary']=="N"?"selected":""?> value="N">NO</option>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Cash In Bank</div>
			<select name="cash_in_bank" style="float:left;width:180px;">
				<option value="">Select</option>
				<option <?=$info['cash_in_bank']=="Y"?"selected":""?> value="Y">YES</option>
				<option <?=$info['cash_in_bank']=="N"?"selected":""?> value="N">NO</option>
			</select>
		</div>
		<div style="clear:both;height:5px;"></div>
		<input type="submit" value="Save" style="height:30px;width:100px;"/>
		</form>
		<fieldset style="float:right;width:250px;">
			<legend>Search</legend>
			<input type="text" onchange="search(this.value)" style="width:100%;"/>
		</fieldset>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<fieldset style="height:400px;overflow:auto;">
		<legend>Chart of Account</legend>
		<table class="navigateableMain"  id="mytbl" style="width:100%;">
			<thead>
				<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
					<th style="border:none;" >Menu</th>
					<th style="border:none;" >Account Code</th>
					<th style="border:none;width:300px;">Account Desc</th>
					<th style="border:none;">Sub Account</th>
					<th style="border:none;">SubAccountGroup</th>
					<th style="border:none;">Account Group</th>
					<th style="border:none;">Account Type</th>
					<th style="border:none;">Report Type</th>
					<th style="border:none;">Default Side</th>
					<th style="border:none;">Subsidiary</th>
					<th style="border:none;">Cash In Bank</th>
				</tr>
			</thead>
			<tbody>
				<?
				$where = $_REQUEST['searchTxt']?"where account_code like '%{$_REQUEST['searchTxt']}%' or account_desc like '%{$_REQUEST['searchTxt']}%' or account_group like '%{$_REQUEST['searchTxt']}%'":"";
				$qry = mysql_query("select * from tbl_chart_of_account $where order by account_desc asc");
				while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="?page=chart_of_account&refid=<?=$row['account_code']?>"><img src="./images/cashdetails.png" style="width:30px;height:30px;" title="Edit"/></a></td>
					<td><?= $row['account_code']?></td>
					<td><?= $row['account_desc']?></td>
					<td><?= $row['sub_account']?></td>
					<td><?= $row['sub_account_group']?></td>
					<td><?= $row['account_group']?></td>
					<td><?= $row['account_type']?></td>
					<td><?= $row['report_type']?></td>
					<td><?= $row['default_side']?></td>
					<td><?= $row['subsidiary']?></td>
					<td><?= $row['cash_in_bank']?></td>
				</tr>
				<? } ?>
			</tbody>
			
		</table>
	</fieldset>
</div>
<script>
$(document).ready(function() {
	jQuery.tableNavigationMain();
	return false;
});
function search(txt){
	window.location = '?page=chart_of_account&searchTxt='+txt;
}
</script>