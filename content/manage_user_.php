<?php
if($_POST['AddUser']){
	$sql = "insert into tbl_user set user='".$_REQUEST['username']."',password='".$_REQUEST['password']."',restriction_id='".$_REQUEST['restriction']."',counter_num='1'";
	$qry = mysql_query($sql);
	if($qry){
		echo "User successfully add...";
	}else{
		echo mysql_error();
	}
}
?>
<div style="height:400px;overflow:auto;">
<fieldset>
	<legend>Create User:</legend>
	<form name="frm_adduser" method="post" onsubmit="return validate();">
		<div style="float:left;margin-right:10px;">
			<div style="float:left;margin-right:10px;width:150px;">User Name:</div>
			<input type="text" name="username" style="float:left;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:150px;">Password:</div>
			<input type="password" name="password" style="float:left;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:150px;">Restriction:</div>
			<select name="restriction" style="float:left;width:150px;">
			<option value="">Select restriction...</option>
			<?php $qry = mysql_query("select * from req_restriction");
				echo mysql_error();
				while($row=mysql_fetch_assoc($qry)){
					echo "<option value='".$row['id']."'>".$row['description']."</option>";
				}
			 ?>
			</select>
		</div>
		<input type="submit" value="Save" name="AddUser" style="float:left;height:30px;width:150px;"/>
	</form>
</fieldset>
</div>
<script>
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