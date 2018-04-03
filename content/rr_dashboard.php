<div style="width:100%;height:500px;background-color:white;text-align:center;">
	<div style="margin: 0 auto;padding-top:100px;width:50%;">
		<div style="float:left;margin-right:10px;">Select Location:</div>
		<select name="location" id="location" style="float:left;width:200px;">
			<option value="2ndfloor">2nd Floor</option>
			<option value="retail">Retail</option>
			<option value="bodega2">Bodega 2</option>
			<option value="bodegamain">Bodega Main</option>
		</select>
		<div style="clear:both;height:5px;"></div>
		<input id="bt4" class="buthov" type="button" value="Stock In" onclick="clickBtn('stockin')" style="height:40px;width:30%;float:left;"/>
		<input id="bt3" class="buthov" type="button" value="Stock Out" onclick="clickBtn('stockout');" style="height:40px;width:30%;float:left;"/>
		<input id="bt17" class="buthov" type="button" value="Stock Transfer" onclick="clickBtn('stocktransfer');" style="height:40px;width:30%;float:left;"/>
		<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="height:40px;width:30%;float:left;"/>
		<div style="clear:both;height:5px;"></div>
	</div>
</div>
<script>
function clickBtn(val){
	var location = $("#location").val();
	window.location='?page='+val+"&location="+location;
}
</script>