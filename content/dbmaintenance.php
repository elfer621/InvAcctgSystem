<fieldset style="padding:20px;min-height:400px;">
	<legend> DB Maintenance </legend>
	<input id="bt4" class="buthov" type="button" onclick="cleanDb();" value="Clean DB" style="height:40px;width:150px;float:left;margin-right:10px;"/>
	<div style="clear:both;height:5px;"></div>
	<div style="font-size:12px;min-height:200px;width:100%;" id="msgarea"></div>
</fieldset>
<script>
function cleanDb(){
	$.ajax({
		url: './content/pos_ajax.php?execute=cleandb',
		type:"POST",
		success:function(data){
			$("#msgarea").html(data);
		}
	});
}
</script>