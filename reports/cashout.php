<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();

$sql = mysql_query("select * from tbl_cashout where id='".$_REQUEST['refnum']."' limit 1");
$info = mysql_fetch_assoc($sql);
?>
<div style="width:300px;">
<h3>Cash Out <span style="float:right;">Num: <?=$_REQUEST['refnum']?></span></h3>
<hr/>
Counter Num: <?=$_SESSION['counter_num']?> <span style="float:right;"><?=date('Y-m-d')?></span>
<fieldset>
<legend>Remarks:</legend>
<?=$info['remarks']?>
</fieldset>
<div style="clear:both;height:5px;"></div>
Amount: <span style="float:right;"><?=number_format($info['amount'],2)?></span>
</div>
<?php
$db->closeDb();
?>
<script>
onload=function(){
	window.print();
	self.close();
}
</script>