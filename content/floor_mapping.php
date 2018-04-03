<?php
$floor=$_REQUEST['floor']?"where a.floor='{$_REQUEST['floor']}'":"";
$sql="select a.*,b.customer_name from tbl_floor_mapping a left join tbl_customers b on a.floor=b.floor and a.unit=b.mall_unit_number $floor order by a.floor asc,a.unit asc";
$qry = mysql_query($sql);
?>
<h3>Floor Mapping</h3>

<div style="clear:both;height:10px;"></div>
<form method="post" name="frm">
	<fieldset>
		<legend>Filter</legend>
		<div style="float:left;width:100px;margin-right:10px;">Select Floor:</div>
		<select name="floor" style="float:left;margin-right:10px;width:120px;">
			<option value="">All</option>
			<option value="3">Floor 3</option>
			<option value="4">Floor 4</option>
		</select>
		<input type="submit" value="Search" style="float:left;width:100px;height:20px;"/>
		<div style="float:right;padding:5px;">
			<div style="width:15px;height:15px;background-color:blue;float:left;"></div>
			<div style="width:100px;float:left;">Occupied</div>
			<div style="width:15px;height:15px;background-color:green;float:left;"></div>
			<div style="width:100px;float:left;">Vacant</div>
		</div>
	</fieldset>
</form>
<div style="clear:both;height:10px;"></div>
<div style="width:100%;">
<?php while($row=mysql_fetch_assoc($qry)){ 
	if($row['customer_name']){
		$color="blue";
	}else{
		$color="green";
	}
?>
	<div onclick="viewFloorDetails(<?=$row['floor']?>,<?=$row['unit']?>)" style="float:left;width:50px;height:50px;background-color:<?=$color?>;color:white;border:1px solid black;"><?="F".$row['floor']."U".$row['unit']?></div>
<?php } ?>
</div>
<script>
function viewFloorDetails(floor,unit){
	clickDialog('dialogbox',500,400,'viewFloorDetails&floor='+floor+'&unit='+unit,'-Details-');
}
</script>