<?php
class dbConnect{
	var $dbname="";
	var $ipadd="";
	var $dbusername="admin";
	var $dbpass="webadmin2010";
	var $con="";
	var $contstatus="";
	var $stockin_header = "";
	var $receipt_header = "<span style='font-size:18px;font-weight:bold;'>LS TECHNOLOGY</span><br/>
		MANDAUE CITY, CEBU<br/>
		Tel. Num. 000-0000<br/>
		TIN: 000-000-000-000<br/>
		Permit: 000000<br/>Serial: 00000<br/>
		<div style='clear:both;height:30px;'></div>";
	var $receipt_footer = "THIS IS NOT AN OFFICIAL RECEIPT<br/>
				PLS ASK FOR AN OFFICIAL RECEIPT<br/>
				THANK YOU...GOD BLESS!!!<br/>";
	function __construct(){
		$this->stockin_header = $_SESSION['settings']['system_fullname'];
	}
	function __destruct(){
		mysql_close($this->con);
	}
	
	function openDb($bypassname=null){
		$x = $bypassname?$bypassname:$_SESSION['connect'];
		if($_SESSION['conlist'][$x]){
			$this->dbname = $_SESSION['conlist'][$x]['db_name'];
			$this->ipadd = $_SESSION['conlist'][$x]['ipaddress'];
		}else{
			$this->dbname = $_SESSION['default_db'];
			$this->ipadd = $_SESSION['default_ip'];
		}
		$this->con = mysql_connect($this->ipadd,$this->dbusername,$this->dbpass);
		if($this->con){
			mysql_select_db($this->dbname,$this->con);
			$this->constatus = $this->dbname;
			mysql_set_charset($this->con,'utf8');
		}else{
			//die('Unable to connect to database ['. mysql_error().']');
			unset($_SESSION['settings']);
			header("location: ./?connect=main&error_msg=Unable to connect!");
		}
			
	}
	function genSqlInsert($data,$tbl){
		$sql = "insert into $tbl (`".implode("`,`",array_keys($data))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $data))."') 
			on duplicate key update "; 
		$flag=false;
		foreach(array_keys($data) as $a => $b){
			if($flag)$sql.=",";
			$sql.="`$b`=values(`$b`)";
			$flag=true;
		}
		return $sql;
	}
	function getWeek($date){
		$time = strtotime($date); // or whenever
		$week_of_the_month = ceil(date('d', $time)/7);
		return $week_of_the_month;
	}
	
	function make_cmp(array $sortValues){
		return function ($a, $b) use (&$sortValues) {
			foreach ($sortValues as $column => $sortDir) {
				$diff = strcmp($a[$column], $b[$column]);
				if ($diff !== 0) {
					if ('asc' === $sortDir) {
						return $diff;
					}
					return $diff * -1;
				}
			}
			return 0;
			};
	}
	function array_sort($array, $on, $order=SORT_ASC){

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
	function utf8ize($d) {
		if (is_array($d)){ 
			foreach ($d as $k => $v) {
				$d[$k] = $this->utf8ize($v);
			}
		}else if(is_object($d)){
			foreach ($d as $k => $v) {
				$d->$k = $this->utf8ize($v);
			}
		}else{ 
			return utf8_encode($d);
		}
		return $d;
	}
	/*function openDb_local(){
		$this->con = mysql_connect($this->ipadd,$this->dbusername,$this->dbpass);
		if($this->con){
				mysql_select_db($this->dbname,$this->con);
				$this->constatus = "Local";
				return true;
			}else{
				return false;
			}
	}*/
	function unsetExcept($keys) {
	  foreach ($_SESSION as $key => $value)
		if (!in_array($key, $keys))
		  unset($_SESSION[$key]);
	}
	function closeDb(){
			mysql_close($this->con);
		}
	function getWHERE($column,$tbl,$condition,$con=null){
			if($con){
				$this->openDb($con);
			}
			$qry = mysql_query("select $column from $tbl $condition limit 1");
			
			if(!$qry){
				//echo mysql_error();
			}else{
				return mysql_fetch_assoc($qry);
			}
		}
	function query($sql,$con=null){
			if($con){
				$this->openDb($con);
			}
			$qry = mysql_query($sql);
			
			if(!$qry){
				return false;
			}else{
				return true;
			}
		}
	function strpos_arr($haystack, $needle) { //find string in array
		if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) {
			if(($pos = strpos($haystack, $what))!==false) return $pos;
		}
		return false;
	}
	function resultSingleArray($column,$tbl,$condition){
		$qry = mysql_query("select $column from $tbl $condition");
		while($row = mysql_fetch_array($qry)){
			$result[] = $row;
		}
		return $result;
	}
	function resultArray($column,$tbl,$condition,$con=null){
			if($con){
				$this->openDb($con);
			}
			$qry = mysql_query("select $column from $tbl $condition");
			while($row = mysql_fetch_array($qry, MYSQL_BOTH)){
				$result[] = $row;
			}
			return $result;
		}
	function saveJournalEntry($refid,$xdate,$entry){
		$sql = "insert into tbl_journal_entry (refid,`date`,account_code,account_desc,dr,cr,account_group,account_type,report_type,subsidiary,cash_in_bank) values ";
		$flag=false;
		for($i=0;$i<count($entry['code']);$i++){
			$account_info = $this->getWHERE("*","tbl_chart_of_account","where account_code='{$entry['code'][$i]}'");
			if($flag)$sql_data.=",";
			$sql_data.="($refid,'$xdate','{$entry['code'][$i]}','{$entry['desc'][$i]}','".str_replace(',','',$entry['dr_amt'][$i])."','".str_replace(',','',$entry['cr_amt'][$i])."','{$account_info['account_group']}','{$account_info['account_type']}','{$account_info['report_type']}','{$account_info['subsidiary']}','{$account_info['cash_in_bank']}')";
			$flag=true;
		}
		$sql = $sql.$sql_data." on duplicate key update `date`=values(`date`),account_code=values(account_code),account_desc=values(account_desc),dr=values(dr),cr=values(cr),account_group=values(account_group),account_type=values(account_type),report_type=values(report_type),subsidiary=values(subsidiary),cash_in_bank=values(cash_in_bank)";
		$qry=mysql_query($sql);
		if(!$qry){
			echo "Saving Journal: ".mysql_error();
			echo $sql;
		}
	}
	
	function sum_array($arr, $col_name){ //sum array specific col name
		$sum = 0;
		foreach ($arr as $item) {
			$sum += $item[$col_name];
		}
		return $sum;
	}
	function getCommission($gain,$item,$qty){
		$found = strpos((string)$item,'FLOUR');
		if($found){
			$com = .5;
		}else{
			switch($gain){
				case $gain >= 30:
					$com = 1;
				break;
				case $gain < 30 and $gain >= 25:
					$com = .5;
				break;
				default:
					$com = 0;
				break;
			}
		}
		return $com * $qty;
	}
	function UniqueMachineID($salt = "") {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
			if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
			$output = shell_exec("diskpart /s ".$temp);
			$lines = explode("\n",$output);
			$result = array_filter($lines,function($line) {
				return stripos($line,"ID:")!==false;
			});
			if(count($result)>0) {
				$result = array_shift(array_values($result));
				$result = explode(":",$result);
				$result = trim(end($result));       
			} else $result = $output;       
		} else {
			$result = shell_exec("blkid -o value -s UUID");  
			if(stripos($result,"blkid")!==false) {
				$result = $_SERVER['HTTP_HOST'];
			}
		}   
		return md5($salt.md5($result));
	}
	function createOverlay($width,$height,$content){
		return '<div class="ui-overlay"><div class="ui-widget-overlay"></div><div class="ui-widget-shadow ui-corner-all" style="width: '.($width+22).'px; height: '.($height+22).'px; position: absolute; left: 50px; top: 30px;"></div></div>
			<div style="position: absolute; width: '.$width.'px; height: '.$height.'px;left: 50px; top: 30px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">
				<div class="ui-dialog-content ui-widget-content" style="background: none; border: 0;width:100%;height:100%;">
					<p>'.$content.'</p>
				</div>
			</div>';
	}
	function dateDiff($date1,$date2){
		$datetime1 = new DateTime($date1);
		$datetime2 = new DateTime($date2);
		$interval = $datetime1->diff($datetime2);
		return $interval->format('%y yrs %m mons %d days');
	}
	function subval_sort($a,$subkey,$sort) {
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		$sort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	}
	function checkSalesTbl($counternum){
		$sql = "select count(*) as count from information_schema.tables where table_schema = '".$this->dbname."' and table_name='tbl_sales_receipt_$counternum'";
		$qry = mysql_query($sql);
		return mysql_result($qry,0)==1;
	}
	function updateInv($qty,$skuid,$type){
		switch($type){
			case'in':
				$qry = mysql_query("update tbl_product_name set base_inv=(base_inv + ".$qty.") where sku_id='$skuid'");
			break;
			case'out':
				$sql = "update tbl_product_name set base_inv=(base_inv - ".$qty.") where sku_id='$skuid'";
				$qry = mysql_query($sql);
			break;
		}
		if(!$qry){
			echo "Update Inv: ".mysql_error()."/n $sql";
		}
	}
	function updateInvNew($skuid){
		/*$sql="SELECT COALESCE( SUM( in_total ) , 0 ) - (COALESCE( SUM( out_total ) , 0 )+COALESCE( SUM( out_total2 ) , 0 )) AS total_inv FROM (
		(SELECT CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total,CONCAT(0) AS out_total2 FROM tbl_sales_items WHERE skuid='$skuid') 
		UNION
		(SELECT COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total,CONCAT(0) AS out_total2 FROM tbl_stockin_items WHERE skuid='$skuid')
		UNION
		(SELECT CONCAT(0) AS in_total,CONCAT(0) AS out_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total2 FROM tbl_stockout_items WHERE skuid='$skuid')
		) AS tbl";
		$qry = mysql_query($sql);
		$ret = mysql_fetch_assoc($qry);*/
		//$update = mysql_query("update tbl_product_name set base_inv=".$ret['total_inv']." where sku_id='$skuid'");
		$update = mysql_query("update tbl_product_name set base_inv=".$this->invBal($skuid)." where sku_id='$skuid'");
		if(!$update){
			echo "UpdateInvNew: ".mysql_error();
		}
	}
	function invBal($skuid,$dt){
		if($dt){
			$qrydt1 = "and date_format(`timestamp`,'%Y-%m-%d') <= '$dt'";
			$qrydt2 = "and `date` <= '$dt'";
		}else{
			$qrydt1 = "";$qrydt2="";
		}
		$sql="SELECT COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv FROM (
		(SELECT timestamp dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_sales_items 
		WHERE skuid='$skuid' $qrydt1) 
		UNION
		(SELECT `date` dt,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total FROM tbl_stockin_items 
		left join tbl_stockin_header on tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='$skuid' $qrydt2)
		UNION
		(SELECT `date` dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_stockout_items 
		left join tbl_stockout_header on tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='$skuid' $qrydt2)
		) AS tbl";
		// $sql="SELECT COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv FROM (
		// (SELECT timestamp dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_sales_items 
		// WHERE skuid='$skuid') 
		// UNION
		// (SELECT `date` dt,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total FROM tbl_stockin_items 
		// left join tbl_stockin_header on tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='$skuid')
		// UNION
		// (SELECT `date` dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_stockout_items 
		// left join tbl_stockout_header on tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='$skuid')
		// ) AS tbl";
		$qry = mysql_query($sql);
		$ret = mysql_fetch_assoc($qry);
		return $ret['total_inv'];
	}
	function outputInvBal($bal,$skuid){
		$uom_default = $this->getWHERE("*","tbl_barcodes","where sku_id='$skuid' and divmul = 1");
		$uom = $this->getWHERE("*","tbl_barcodes","where sku_id='$skuid' and divmul != 1 order by divmul desc");
		$inv = $this->NumberBreakdown($bal);
		if($uom){
			if($inv[1]){
				$bungkig = $inv[1] / $uom['divmul'];
				return $inv[0]." ".$uom_default['unit']. " &amp; ".floor($bungkig)." ".$uom['unit'];
			}else{
				if($uom['divmul']<1){
					return $bal." ".$uom_default['unit'];
				}else{
					$whole_inv = $bal / $uom['divmul'];
					$inv = $this->NumberBreakdown($whole_inv);
					$bungkig = $inv[1] * $uom['divmul'];
					return $inv[0]." ".$uom['unit']. " &amp; ".(float)$bungkig." ".$uom_default['unit'];
				}
			}
			
		}else{
			return $inv[0]." ".$uom_default['unit'];
		}
	}
	function outputCost($bal,$skuid){
		$uom_default = $this->getWHERE("*","tbl_barcodes","where sku_id='$skuid' and divmul = 1");
		$uom = $this->getWHERE("*","tbl_barcodes","where sku_id='$skuid' and divmul != 1 order by divmul desc");
		$inv = $this->NumberBreakdown($bal);
		if($uom){
			if($inv[1]){
				$bungkig = $inv[1] / $uom['divmul'];
				return number_format($uom_default['cost'],2)."/".$uom_default['unit']. " or ".number_format($uom['cost'],2)."/".$uom['unit'];
			}else{
				if($uom['divmul']<1){
					return number_format($uom_default['cost'],2)."/".$uom_default['unit'];
				}else{
					$whole_inv = $bal / $uom['divmul'];
					$inv = $this->NumberBreakdown($whole_inv);
					$bungkig = $inv[1] * $uom['divmul'];
					return number_format($uom['cost'],2)."/".$uom['unit']. " or ".number_format($uom_default['cost'],2)."/".$uom_default['unit'];
				}
			}
			
		}else{
			return number_format($uom_default['cost'],2)."/".$uom_default['unit'];
		}
	}
	function NumberBreakdown($number, $returnUnsigned = false){
		  $negative = 1;
		  if ($number < 0)
		  {
			$negative = -1;
			$number *= -1;
		  }

		  if ($returnUnsigned){
			return array(
			  floor($number),
			  ($number - floor($number))
			);
		  }

		  return array(
			floor($number) * $negative,
			($number - floor($number)) * $negative
		  );
	}
	function salesvoid($refid){
		$counter=$_SESSION['counter_num'];
		$sql="update `tbl_sales_receipt_$counter` set type='VOID' where receipt_id='$refid'";
		$qry_void = mysql_query($sql);
		if($qry_void){
			$prod = $this->resultArray("*","tbl_sales_items","where receipt='$refid' and counter='$counter' and reading='{$_SESSION['readingnum']}'");
			$del = mysql_query("delete from tbl_sales_items where receipt='$refid' and counter='$counter' and reading='{$_SESSION['readingnum']}'");
			if($del){
				foreach($prod as $key=>$val){
					$this->updateInvNew($val['skuid']);
				}
				//$del1 = mysql_query("delete from tbl_customers_trans where receipt='$refid' and counter='$counter' and reading='{$_SESSION['readingnum']}'");
				$add = mysql_query("insert into tbl_sales_voiditems set receipt='$refid',counter='$counter',reading='{$_SESSION['readingnum']}',item_array='".serialize($prod)."'");
				if($add){
					echo "success";
				}else{
					$error.=mysql_error();
				}
			}else{
				$error.=mysql_error();
			}
		}else{
			$error.=mysql_error();
		}
	}
	function saveCustTrans($data=array()){
		$sql = "insert into tbl_customers_trans set 
			date='".(!empty($data['date'])?$data['date']:date('Y-m-d'))."',
			cust_id='{$data['cust_id']}',
			receipt='{$data['receipt']}',
			counter='{$data['counter']}',
			reading='{$data['reading']}',
			transtype='{$data['transtype']}',
			details='{$data['details']}',
			".($data['more_details']?"more_details='{$data['more_details']}',":"")."
			amount='{$data['amount']}'";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
			return false;
		}else{
			return true;
		}
	}
	function editCustTrans($data=array()){
		$sql = "update tbl_customers_trans set 
			date='".(!empty($data['date'])?$data['date']:date('Y-m-d'))."',
			cust_id='{$data['cust_id']}',
			receipt='{$data['receipt']}',
			counter='{$data['counter']}',
			reading='{$data['reading']}',
			transtype='{$data['transtype']}',
			details='{$data['details']}',
			".($data['more_details']?"more_details='{$data['more_details']}',":"")."
			amount='{$data['amount']}'
			where receipt='{$data['receipt']}'";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
			return false;
		}else{
			return true;
		}
	}
	function customeFormat($num,$len=4){
		$count = strlen($num);
		$add = $len - $count;
		$output = $num;
		for($x=1;$x<=$add;$x++){
			$output = '0'.$output;
		}
		return $output;
	}
	
	function sumCostInArray($array){
		$cost=0;
		foreach($array as $val){
			$cost+=($val['qty']*$val['cost']);
		}
		return $cost;
	}
	function sumInfo($array,$percent_qualify,$points_divisor){
		$cost=0;
		$vat=0;
		foreach($array as $val){
			$cost+=($val['qty']*$val['cost']);
			$gain = ($val['price']-$val['cost']);
			$percentage = $gain / $val['cost'];
			if($percentage > $percent_qualify){
				$points += ($val['price'] * $val['qty']) / $points_divisor;
			}
			if($val['tax_type']==3){
				//$vat+=($val['total']/ 9.333);
				$vat+=$val['total'];
			}
		}
		return array('cost'=>$cost,'points'=>$points,'vat'=>$vat);
	}
	function updateCost($bcode,$newcost){
		$sql = "update tbl_barcodes set cost='$newcost' where barcode='$bcode'";
		$qry = mysql_query($sql);
		if($qry){
			return true;
		}else{
			echo "Error on updating cost: ".mysql_error();
		}
	}
	function updatePrice($bcode,$newprice){
		$sql = "update tbl_barcodes set price='$newprice' where barcode='$bcode'";
		$qry = mysql_query($sql);
		if($qry){
			return true;
		}else{
			echo "Error on updating Price: ".mysql_error();
		}
	}
	function savingEntry($refid,$val,$tbl){
		$sql_items = "insert into $tbl set 
			stockin_refid=$refid,
			count='".$val['count']."',
			barcode='".$val['bcode']."',
			item_desc='".$val['prod_name']."',
			qty=".$val['qty'].",
			unit='".$val['unit']."',
			cost='".$val['cost']."',
			discount='".$val['discount']."',
			total='".((($val['cost']*$val['qty']))*(1-$val['discount']))."',
			skuid='".$val['sku']."',
			divmul='".$val['divmul']."'
			ON DUPLICATE KEY UPDATE
			qty=".$val['qty'].",
			cost='".$val['cost']."',
			discount='".$val['discount']."',
			total='".((($val['cost']*$val['qty']))*(1-$val['discount']))."'
			";
		$qry_items = mysql_query($sql_items);
		if($qry_items){
			return true;
		}else{
			return false;
		}
	}
	function sqlUpdateInv($skuid){
		$sql = $sql="SELECT COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv FROM (
		(SELECT timestamp dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_sales_items 
		WHERE skuid='$skuid') 
		UNION
		(SELECT `date` dt,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total FROM tbl_stockin_items 
		left join tbl_stockin_header on tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='$skuid')
		UNION
		(SELECT `date` dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_stockout_items 
		left join tbl_stockout_header on tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='$skuid')
		) AS tbl";
		return $sql;
	}
	function savingEntry2($refid,$array,$tbl){ //used in stockin page only
		$del = mysql_query("delete from $tbl where stockin_refid='{$refid}'");
		$sql_produpdate = "insert into tbl_product_name (sku_id,base_inv) values ";
			$flag = false;

			foreach($this->subval_sort($array,'count',arsort) as $key => $val){
				if($flag){$sql_items.=",";$sql_produpdate.=",";}
				if($val['id']){$id_item1="{$val['id']},";}else{$id_item1="'',";}
				$sql_items .= "($id_item1 $refid,'".$val['count']."','".$val['bcode']."','".mysql_real_escape_string($val['prod_name'])."',".$val['qty'].",
				'".$val['unit']."','".$val['cost']."','".$val['discount']."','".((($val['cost']*$val['qty']))*(1-$val['discount']))."',
				'".$val['sku']."','".$val['divmul']."','".$val['price']."')";
				$sql_produpdate.="('{$val['sku']}',({$this->sqlUpdateInv($val['sku'])}))";
				$flag=true;
			}
		
			$sql_header = "insert into $tbl (id,stockin_refid,count,barcode,item_desc,qty,unit,cost,discount,total,skuid,divmul,selling) values ";
			$sql_items .= " ON DUPLICATE KEY UPDATE
				qty=VALUES(qty),cost=values(cost),discount=values(discount),total=values(total),selling=values(selling),skuid=values(skuid)";
			$sql_produpdate.="ON DUPLICATE KEY UPDATE base_inv=values(base_inv)";
			$qry_items = mysql_query($sql_header.$sql_items);
			//echo $sql_header.$sql_items."<br/>";exit;
			
			if($qry_items){
				//return true;
				$produpdate = mysql_query($sql_produpdate);
				if($produpdate){
					return true;
				}else{
					$_SESSION['error'].="ItemSaving 1.) ".mysql_error().$sql_header.$sql_items."<br/><hr/><br/>";
					return false;
				}
			}else{
				$_SESSION['error'].="ItemSaving 2.) ".mysql_error().$sql_header.$sql_items."<br/><hr/><br/>";
				return false;
			}
		
	}
	function delProd($id,$tbl){
		/*switch($tbl){
			case'tbl_stockin_items':case'tbl_stockout_items':
				$sql ="DELETE FROM $tbl WHERE stockin_refid='$id' and barcode='$barcode'";
			break;
			default:
				$sql ="DELETE FROM $tbl WHERE receipt='$id' and barcode='$barcode' and counter='$counter'";
			break;
		}*/
		$val = $this->getWHERE("skuid","$tbl","where id='".$id."'");
		$sql ="DELETE FROM $tbl WHERE id='$id'";
		$qry = mysql_query($sql);
		if($qry){
			$this->updateInvNew($val['skuid']);
			return true;
		}else{
			//echo "Error on deleting record(s) ".mysql_error();
			return false;
		}
	}
	function genSKU($qtd){
		//Under the string $Caracteres you write all the characters you want to be used to randomly generate the code.
		//$Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
		$Caracteres = '0123456789';
		$QuantidadeCaracteres = strlen($Caracteres);
		$QuantidadeCaracteres--;

		$Hash=NULL;
			for($x=1;$x<=$qtd;$x++){
				$Posicao = rand(0,$QuantidadeCaracteres);
				$Hash .= substr($Caracteres,$Posicao,1);
			}

		return $Hash;
	}
	function createSalesTable($counter){
		$sql = "CREATE TABLE `tbl_sales_receipt_$counter` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`receipt_id` INT(11) NOT NULL DEFAULT '1',
				`counter_num` INT(11) NOT NULL,
				`reading` INT(11) NOT NULL,
				`date` DATE NOT NULL,
				`amount` DOUBLE NOT NULL,
				`tender` DOUBLE NOT NULL,
				`change` DOUBLE NOT NULL,
				`cost` DOUBLE NOT NULL,
				`gain` DOUBLE NOT NULL,
				`vat` DOUBLE NOT NULL,
				`cashier` VARCHAR(50) NOT NULL,
				`type` VARCHAR(50) NOT NULL,
				`payment` VARCHAR(50) NOT NULL,
				`orderslip` VARCHAR(50) NOT NULL,
				`studentid` VARCHAR(50) NOT NULL,
				`studentname` VARCHAR(100) NOT NULL,
				`course` VARCHAR(50) NOT NULL,
				`yr` VARCHAR(50) NOT NULL,
				`category_id` INT(5) NOT NULL,
				`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unique` (`receipt_id`, `counter_num`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=InnoDB
			;";
			
		$qry = mysql_query($sql);
		if($qry){
			return true;
		}else{
			return false;
		}
	}
	
	function getManufacturer(){
		$sql = "select * from tbl_manufacturer order by manufacturer_name asc";
		$qry = mysql_query($sql);
		while($row=mysql_fetch_assoc($qry)){
			$result .= "<option value='".$row['id']."'>".$row['supplier_name']."</option>";
		}
		echo $result;
	}
	function getCategory(){
		$sql = "select * from tbl_category order by category_name asc";
		$qry = mysql_query($sql);
		while($row=mysql_fetch_assoc($qry)){
			$result .= "<option value='".$row['category_id']."'>({$row['category_id']})".$row['category_name']."</option>";
		}
		echo $result;
	}
	function getSupplier(){
		$sql = "select * from tbl_supplier order by supplier_name asc";
		$qry = mysql_query($sql);
		while($row=mysql_fetch_assoc($qry)){
			$result .= "<option value='".$row['id']."'>".$row['supplier_name']."</option>";
		}
		echo $result;
	}
	function getServerReadingnum(){
		$info=$this->getWHERE("*","tbl_reading_server","where end_date='0000-00-00' order by reading_num desc");
		if($info){
			return $info['reading_num'];
		}else{
			//return "no records";
			return false;
		}
	}
	function getReadingnum($counter_num){
		$qry = mysql_query("select * from tbl_reading where end_date='0000-00-00' and counter='".$counter_num."' order by reading_num desc limit 1");
		$row = mysql_fetch_assoc($qry);
		mysql_free_result($qry);
		return $row['reading_num'];
	}
	function getNextID($colid,$tblname){
		$qry = mysql_query("select max($colid) as id from $tblname limit 1");
		$info = mysql_fetch_assoc($qry);
		return $info['id'] + 1;
	}
	function getNextSI(){
		$qry = mysql_query("select id from tbl_sales_invoice_header order by counting desc limit 1");
		$info = mysql_fetch_assoc($qry);
		return "A".$this->customeFormat(filter_var($info['id'], FILTER_SANITIZE_NUMBER_INT) + 1,6);
	}
	function readingNext($counter){
		$qry = mysql_query("select max(reading_num) as readingnum from tbl_reading where counter='".$counter."' limit 1");
		$info = mysql_fetch_assoc($qry);
		return $info['readingnum'] + 1;
	}
	function getNextIdSOA(){
		$qry = mysql_query("select max(id) as soa_id from tbl_soa limit 1");
		$info = mysql_fetch_assoc($qry);
		return $info['soa_id'] + 1;
	}
	function getReceipt($counter,$reading,$transtype){
		$tblname = $transtype=="order"?"tbl_order_receipt":"tbl_sales_receipt_".$counter;
		$qry = mysql_query("select max(receipt_id) as receipt_num from $tblname limit 1");
		if($qry){
			$info = mysql_fetch_assoc($qry);
			return $info['receipt_num'];
		}else{
			return 0;
		}
	}
	function sumTotalCost($reading,$counter,$tbl){
		$sql = "select sum(cost * qty) as total_cost from $tbl where reading='$reading' and counter='$counter'";
		$qry = mysql_query($sql);
		if($qry){
			$info = mysql_fetch_assoc($qry);
			return $info['total_cost'];
		}else{
			return 0;
		}
	}
	function intToWords($x){ //double to words
		$nwords = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 
					'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 
					'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 
					'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty', 
					50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 
					90 => 'ninety',
					'dollars' => 'dollars', 'cents' => 'cents');

		if(!is_float($x) && !is_numeric($x))
		{
			$w = '#';
		}
		else
		{
			if($x < 0)
			{
				$w = 'minus '; 
				$x = -$x; 
			}
			else
			{
				$w = ''; 
			}
			if($x < 21)
			{
				$w .= $nwords[$x];
			}
			else if($x < 100)
			{
				$w .= $nwords[10 * floor($x/10)];
				$r = fmod($x, 10);
				if($r > 0)
				{
					$w .= '-'. $nwords[$r];
				}
				
				/*if(is_float($x))
				{
					$w .= ' ' . $nwords['cents'];
				}
				else if(is_int($x))
				{
					$w .= ' ' . $nwords['dollars'];
				}*/
			}
			else if($x < 1000)
			{
				$w .= $nwords[floor($x/100)] .' hundred';
				$r = fmod($x, 100);
				if($r > 0)
				{
					//$w .= ' and '. $this->convertCurrencyToWords($r);
					$w .= ' '. $this->convertCurrencyToWords($r);
				}
			}
			else if($x < 1000000)
			{
				$w .= $this->convertCurrencyToWords(floor($x/1000)) .' thousand';
				$r = fmod($x, 1000);
				if($r > 0)
				{
					$w .= ' '; 
					if($r < 100)
					{
						$w .= ' and'; 
					}
					$w .= $this->convertCurrencyToWords($r); 
				}
			}
			else
			{
				$w .= $this->convertCurrencyToWords(floor($x/1000000)) .' million'; 
				$r = fmod($x, 1000000);
				if($r > 0)
				{ 
					$w .= ' ';
					if($r < 100)
					{
						$word .= ' and ';
					}
					$w .= $this->convertCurrencyToWords($r);
				}
			}
		}
		return $w; 
	}
	function convertCurrencyToWords($number) {
			if(!is_numeric($number)) return false;
			$nums = explode('.', number_format($number,2));
			$newnums = preg_replace("/[^0-9.]/", "", $nums[0]);
			$out = $this->intToWords($newnums);
			if(($nums[1]*1)!=0) {
				$out .= ' pesos and '.$this->intToWords($nums[1]*1).' cents';
				//$out .= ' pesos and '.$nums[1].' cents';
			}
		return $out;
		
	}
}


?>