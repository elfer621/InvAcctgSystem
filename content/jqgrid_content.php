<link type="text/css" href="./js/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="./js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<div id='tabs'>
    <ul></ul>
</div>
<table id="list486"></table>
<div id="plist486"></div>
<!--button id='add-tab'>Add tab</button-->
<script>
$(document).ready(function() {
   $(function(){
		$('#tabs').tabs();
	});

	showDataFilter(<?=$_REQUEST['refid']?>);
});

</script>