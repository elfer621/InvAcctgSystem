<?php 
//$db->openDb("main");
$colfields=[];
$colfields_extra=[];
$not = array("base_inv","base_unit","freight_handling","supplier_id","group_id","manufacturer_id","category_id","reorder_level","school_level",
		"year","subject_name","subjtype","tax_type","inv_bal_forwarded","inv_po_receipts","inv_direct_purchases","inv_transfer_receipts","inv_sales_returns",
		"inv_purchase_returns","inv_issuances","inv_sales","inv_adjustment","inv_transfer","hidden","datetime_changed");
if($_POST){
	$sql = "insert into {$_REQUEST['tblname']} (`".implode("`,`",array_keys($_POST))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $_POST))."') 
	on duplicate key update "; 
	$flag=false;
	foreach(array_keys($_POST) as $a => $b){
		if($flag)$sql.=",";
		$sql.="$b=values($b)";
		$flag=true;
	}
	//echo $sql;exit;
	$qry=mysql_query($sql);
	if($qry){
		echo "<script>$(document).ready(function(){alertMsg('Save Successfully...');});</script>";
	}else{
		echo mysql_error();
	}
}
if($_REQUEST['execute']){
	session_start();
	require_once"../settings.php";
	require_once"../class/dbConnection.php";
	require_once"../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		case'editRec':
			$col=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],"SHOW FULL COLUMNS FROM {$_REQUEST['tblname']}");
			foreach($col as $colk=>$colv){
				if($db->strpos_arr($colv['Field'],$not) === false){
					$colfields[]=$colv['Field'];
				}
			}
			// echo "<pre>";
			// print_r($col);
			// echo "</pre>";
			$info = $db->getWHERE("*","{$_REQUEST['tblname']}","where {$_REQUEST['refname']}='{$_REQUEST['refid']}'");
			?>
			<form method="post">
				<?php foreach($col as $k => $v){ 
					if($db->strpos_arr($v['Field'],$not) === false){
					?>
					<div style="width:20%;float:left;margin-right:5px;"><?=$v['Field']?></div>
					<input type="text" name="<?=$v['Field']?>" value="<?=$info[$v['Field']]?>" style="float:left;width:75%;"/>
					<div style="clear:both;height:5px;"></div>
					<?php }
					if($v['Comment']){
						$com=explode("|",$v['Comment']);
						?>
						<div style="width:20%;float:left;margin-right:5px;"><?=$v['Field']?></div>
						<select name="<?=$v['Field']?>" style="float:left;width:75%;">
							<option value="">Select Records</option>
							<?php 
							foreach($db->resultArray("*",$com[2],"") as $a => $b){
							echo "<option ".($info[$v['Field']]==$b[0]?"selected":"")." value='{$b[0]}'>{$b[1]}</option>";
							}
							?>
						</select>
						<div style="clear:both;height:5px;"></div>
						<?
					}
					} ?>
				<input type="submit" value="Save" style="height:40px;width:100px;"/>
			</form>
			<?
		break;
		case'delRec':
			$sql="delete from {$_REQUEST['tblname']} where {$_REQUEST['refname']}='{$_REQUEST['refid']}'";
			$qry=mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
	}
}else{
	if($_REQUEST['join']){
		$leftjoin.=" ".$_REQUEST['join'];
		$colfields_extra=explode(",",$_REQUEST['join_fields']);
	}
	$col=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],"SHOW FULL COLUMNS FROM {$_REQUEST['tblname']}");
	foreach($col as $colk=>$colv){
		if($db->strpos_arr($colv['Field'],$not) === false){
			$colfields[]=$colv['Field'];
		}
		if($colv['Comment']){
			$com=explode("|",$colv['Comment']);
			$colfields_extra[]=$com[1];
			$leftjoin.=" ".$com[0];
		}
	}
	// echo "<pre>";
	// print_r($colfields_extra);
	// echo "</pre>";
	$sql="select ".implode(",",$colfields).($colfields_extra?",".implode(",",$colfields_extra):"")." from {$_REQUEST['tblname']} ";
	$sql.=$leftjoin;
	if($_REQUEST['searchTxt']){
		$flag=false;
		
		foreach(array_unique(array_merge($colfields, $colfields_extra)) as $k => $v){
			if($flag)$search.=" or ";
			$search.="`{$v}` like '%".$_REQUEST['searchTxt']."%'";
			$flag=true;
		}
		$sql.=" where $search";
	}
	// echo $sql;
	// echo "<hr/>";
	$rec=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
	?>
	<style>
		#fmeanu.floating {
			position: fixed;
			top: 0;
			background-color:white;
		}
	</style>
	<div style="clear:both;height:10px;"></div>
	<div class="content" style="min-height:300px;width:100%!important;">
		<fieldset id="fmeanu">
			<legend>MENU</legend>
			<input id="bt7" class="buthov" type="button" value="Add" onclick="addRec()" style="height:40px;width:150px;float:left;margin:5px;"/>
			<input id="bt8" class="buthov" type="button" value="Edit" onclick="editRec()" style="height:40px;width:150px;float:left;margin:5px;"/>
			<input id="bt9" class="buthov" type="button" value="Delete" onclick="delRec()" style="height:40px;width:150px;float:left;margin:5px;"/>
			<div style="float:right;">
				<div style="float:left;margin-right:10px;">Search: </div>
				<input type="text" onchange="search(this.value)" style="width:250px;float:left;" />
			</div>
		</fieldset>
		<div style="clear:both;height:10px;"></div>
		<div style="min-width:1100px;height:800px;overflow:auto;" id="tbldiv">
			<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%" >
				<thead>
					<tr>
					<?php /*foreach($col as $k => $v){ ?>
						<th><?=$v['Field']?></th>
					<?php }*/
					echo "<th>".implode("</th><th>",$colfields).($colfields_extra?"</th><th>".implode("</th><th>",$colfields_extra):"")."</th>";
					?>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach($rec as $key => $val){ ?>
						<tr>
							<?php 
								/*foreach($col as $k => $v){
									echo "<td>{$val[$v['Field']]}</td>";
								}*/
								foreach($val as $k => $v){
									echo "<td>".(is_numeric($v)&&!fnmatch("*id*",$k)?number_format($v,2):$v)."</td>";
								}
							?>
						</tr>
					<?php }?>
				</tbody>
			</table>
		</div>
		<div class="modal" style="float:left;margin-right:5px;display:none;" id="modalisimo">
			<div class="modal-content" style="width:50%;">
				<span style="float:left;font-size:15px;font-weight:bold;">Add New Records</span><span style="float:right;" class="close" onclick="$('#modalisimo').hide();">&times;</span>
				<div style="clear:both;height:5px;"></div>
				<form method="post">
					<?php foreach($colfields as $k => $v){ ?>
						<div style="width:150px;float:left;margin-right:5px;"><?=$v?></div>
						<input type="text" name="<?=$v?>" style="float:left;width:250px;"/>
						<div style="clear:both;height:5px;"></div>
					<?php } ?>
					<input type="submit" value="Save" style="height:40px;width:100px;"/>
				</form>
				<div style="clear:both;height:5px;"></div>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		jQuery.tableNavigationMain();
		$(window).scroll( function() {
			if ($(window).scrollTop() > $('#ajax_content').offset().top)
				$('#fmeanu').addClass('floating');
			else
				$('#fmeanu').removeClass('floating');
		} );
		$('input[name*="date"]').datepicker({
			changeMonth: true,
			changeYear: true,
			inline: true,
			dateFormat:"yy-mm-dd"
		});
	});
	function search(txt){
		window.location = '?page=dynamictbl&tblname='+getParam('tblname')+'&searchTxt='+txt;
	}
	function addRec(){
		$("#modalisimo").show();
	}
	function editRec(){
		var tblname = '<?=$_REQUEST['tblname']?>';
		var refname = $("thead tr:first").find("'th:eq(0)'").text();
		var refid = $("tr.selected").find("'td:eq(0)'").text();
		clickDialog('dialogbox2',700,500,'editRec&tblname='+tblname+'&refid='+refid+'&refname='+refname,'Edit Record',null,'dynamictbl_add_edit.php');
		$('input[name*="date"]').datepicker({
			changeMonth: true,
			changeYear: true,
			inline: true,
			dateFormat:"yy-mm-dd"
		});
	}
	function delRec(){
		var tblname = '<?=$_REQUEST['tblname']?>';
		var refname = $("thead tr:first").find("'th:eq(0)'").text();
		var refid = $("tr.selected").find("'td:eq(0)'").text();
		var r=confirm("Are you sure to DELETE this record?");
		if (r==true){
			$.ajax({
				url: './content/dynamictbl_add_edit.php?execute=delRec&tblname='+tblname+'&refid='+refid+'&refname='+refname,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location=document.URL;
					}else{
						alertMsg(data);
					}
				}
			});
		}
	}
	</script>
<?php }?>