<?php
if($_POST['AddUser']){
	$sql = "insert into tbl_user (id,user,password,first_name,last_name,restriction_id) values 
		('".$_REQUEST['id']."','".$_REQUEST['username']."','".$_REQUEST['password']."','".$_REQUEST['first_name']."','".$_REQUEST['last_name']."','".$_REQUEST['restriction']."')
		on duplicate key update user=values(user),password=values(password),first_name=values(first_name),last_name=values(last_name),restriction_id=values(restriction_id)";
	//$sql = "insert into tbl_user set user='".$_REQUEST['username']."',password='".$_REQUEST['password']."',restriction_id='".$_REQUEST['restriction']."',counter_num='1'";
	$qry = mysql_query($sql);
	if($qry){
		echo "User successfully add...";
	}else{
		echo mysql_error();
	}
}
$users = $db->resultArray("a.*,b.description","tbl_user a left join req_restriction b on a.restriction_id=b.id","order by id asc");
if($_REQUEST['id']){
	$info=$db->getWHERE("*","tbl_user","where id='".$_REQUEST['id']."'");
}
?>
<div style="height:400px;">
<fieldset style="float:left;width:45%;">
	<legend>Create User:</legend>
		<div style="float:left;margin-right:10px;width:100%;">
			<form name="frm_adduser" method="post" onsubmit="return validate();">
				<div style="float:left;margin-right:10px;width:150px;">User Name:</div>
				<input type="text" name="username" id="username" style="float:left;" value="<?=$info['user']?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:10px;width:150px;">Password:</div>
				<input type="password" name="password" id="password" style="float:left;" value="<?=$info['password']?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:10px;width:150px;">First Name:</div>
				<input type="text" name="first_name" id="first_name" style="float:left;" value="<?=$info['first_name']?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:10px;width:150px;">Last Name:</div>
				<input type="text" name="last_name" id="last_name" style="float:left;" value="<?=$info['last_name']?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:10px;width:150px;">Restriction:</div>
				<select name="restriction" id="restriction" style="float:left;width:150px;">
				<option value="">Select restriction...</option>
				<?php $qry = mysql_query("select * from req_restriction");
					echo mysql_error();
					while($row=mysql_fetch_assoc($qry)){
						echo "<option ".($info['restriction_id']==$row['id']?"selected":"")." value='".$row['id']."'>".$row['description']."</option>";
					}
				 ?>
				</select>
				<input type="hidden" name="id" id="id" value="<?=$info['id']?>"/>
				<div style="clear:both;height:5px;"></div>
				<input type="submit" value="Save" name="AddUser" style="height:30px;width:150px;"/>
			</form>
		</div>
</fieldset>
<div style="float:left;margin-left:10px;width:45%;">
	<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th style="border:none;">UserName</th>
				<th style="border:none;">First Name</th>
				<th style="border:none;">Last Name</th>
				<th style="border:none;">Restriction</th>
				<th style="border:none;">Menu</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($users as $key => $val){ ?>
			<tr>
				<td><?=$val['user']?></td>
				<td><?=$val['first_name']?></td>
				<td><?=$val['last_name']?></td>
				<td><?=$val['description']?></td>
				<td><input type="button" value="Edit" onclick="edit(<?=$val['id']?>)"/></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

</div>
<script>
function edit(id){
	window.location="?page=manager_user&id="+id;
}
function validate(){
	var frm = document.frm_adduser;
	if(frm.username.value==""){
		alert("Pls input user name...");
		return false;
	}else if(frm.password.value==""){
		alert("Pls input password...");
		return false;
	}else if(frm.restriction.value==""){
		alert("Pls select restriction...");
		return false;
	}
	return true;
}
</script>