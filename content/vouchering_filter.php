<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
// $sql = "select * from view_vouchering_filter where {$_REQUEST['where']} group by refid";
// $qry = mysql_query($sql);
// if(!$qry){
	// echo mysql_error();
// }
$output = preg_split( "/ (and) /", $_REQUEST['where'] );
$flag=false;
foreach($output as $key => $val){
	if($db->strpos_arr($val,array("Campus")) == true){
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
$where = $where?"where $where":"";
// $sql = "select * from (SELECT a.*,b.remarks,b.reference,b.total,b.center centername from tbl_journal_entry a 
	// left join tbl_vouchering b on a.refid=b.id and COALESCE(a.type,'')=COALESCE(b.type,'')) tbl $where"; // 
// $sql = "select * from (SELECT a.*,b.remarks,b.reference,b.total,b.center centername from tbl_journal_entry a 
// left join tbl_vouchering b on a.refid=b.id) tbl $where group by refid";
$sql = "select * from (SELECT a.*,b.remarks,b.reference,b.total,b.center centername,b.type btype from tbl_journal_entry a 
	left join tbl_vouchering b on a.refid=b.id and COALESCE(a.type,'')=COALESCE(b.type,'')) tbl $where group by refid,btype"; // 
//echo $sql;
$arrs=array();
// if($_SESSION['connect']){
	// $con->getBranch2(trim($_SESSION['connect']));
	// $arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	// echo "-Single DB-";
// }else{
	// if($campus){
		// $con->getBranch2(trim($campus[1]));
		// $arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	// }else{
		// $arrs[]=$con->pdoStyle($GLOBALS['default_ip'],$GLOBALS['default_db'],$sql);
		// if($_SESSION['settings']['connection_type']=="multiple"){
			// foreach($_SESSION['conlist'] as $key => $val){
				// $arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			// }
		// }
	// }
// }
$con->getBranch2(trim($_SESSION['connect']));
$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);

$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}
//print_r($list);
//echo count($list);
?>
<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<script src="../js/myjs.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;border-top:1px solid;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selected {background-color: red; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
	</style>
</head>
<body style="padding:5px;height:100%;font-size:10px;">
	<table class="navigateable" style="width:100%;">
		<thead>
			<tr>
				<th>Menu</th>
				<th>FS YR</th>
				<th>Date</th>
				<th>Journal</th>
				<th>Entry #</th>
				<th>Reference</th>
				<th>Remarks</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
			// echo count($list);
			if(count($list)<1){
				echo "<tr><td colspan='8'>No records found...</td></tr>";
			}else{
			//while($row=mysql_fetch_assoc($qry)){
			foreach($list as $key => $row){ 
		?>		
				<tr>
					<td>
						<img onclick="transferStockin('<?=$row['refid'] ?>','<?=$row['type'] ?>','<?=$row['centername']?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
					</td>
					<td><?=$row['fiscal_year']?></td>
					<td><?=$row['date']?></td>
					<td><?=$row['type']?></td>
					<td><?=$row['refid']?></td>
					<td><?=$row['reference']?></td>
					<td><?=$row['remarks']?></td>
					<td style='text-align:right;'><?=number_format($row['total'],2)?></td>
				</tr>
			<?php }}	?>
		</tbody>
	</table>
</body>
<script>
	$(document).ready(function(){
		jQuery.tableNavigation();
		window.opener.location.reload();
	});
	function transferStockin(val,xtype,center){
		var urls = getUrl();
		$.ajax({
			url: './vouchering_ajax.php?execute=viewVoucher&refid='+val+'&center='+center+'&type='+xtype,
			data:urls,
			type:"POST",
			success:function(data){
				if(data=="success"){
					//window.location.href="?page=vouchering";
					window.close();
					//window.opener.location.href="?page=vouchering";
					window.opener.location.reload();
				}
				
			}
		});
	}
</script>