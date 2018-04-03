<?php
class MySQLiContainer extends SplObjectStorage{
  public function newConnection($host = null, $username = null, $passwd = null, $dbname = null, $port = null, $socket = null) {
    $mysqli = @new mysqli($host, $username, $passwd, $dbname, $port, $socket);
    if ($mysqli->connect_error) {
		//header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&error_msg=Unable to connect in ".strtoupper($dbname)."!");
		echo "Error connecting...";
	}else{
		$this->attach($mysqli);
		return $mysqli;
	}
	
  }
}

class dbUpdate{
	var $c1;
	var $c2;
	var $dbname="";
	var $ipadd="";
	var $dbname2="";
	var $ipadd2="";
	var $mysqliContainer;
	var $dbusername="admin";
	var $dbpass="webadmin2010";
	
	// var $con_ucbanilad = array('db'=>'lizgan_ucbanilad','ip'=>'localhost');
	// var $con_uclm = array('db'=>'lizgan_uclm','ip'=>'localhost');
	// var $con_ucmain = array('db'=>'lizgan_ucmain','ip'=>'localhost');
	// var $con_ucmambaling = array('db'=>'lizgan_ucmambaling','ip'=>'localhost');
	// var $con_warehouse = array('db'=>'lizgan_warehouse','ip'=>'localhost');
	// var $con_main = array('db'=>'lizgan_main','ip'=>'localhost');
	
	// var $con_ucbanilad = array();
	// var $con_uclm = array();
	// var $con_ucmain = array();
	// var $con_ucmambaling = array();
	// var $con_warehouse = array();
	// var $con_main = array();
	
	
	function __construct(){
		//$con_main = array('db'=>$_SESSION['default_db'],'ip'=>$_SESSION['default_ip']);
		// $con_ucbanilad = array('db'=>$_SESSION['conlist']['ucbanilad']['db_name'],'ip'=>$_SESSION['conlist'][$x]['ipaddress']);
		// $con_uclm = array('db'=>$_SESSION['conlist']['uclm']['db_name'],'ip'=>$_SESSION['conlist'][$x]['ipaddress']);
		// $con_ucmain = array('db'=>$_SESSION['conlist']['ucmain']['db_name'],'ip'=>$_SESSION['conlist'][$x]['ipaddress']);
		// $con_ucmambaling = array('db'=>$_SESSION['conlist']['ucmambaling']['db_name'],'ip'=>$_SESSION['conlist'][$x]['ipaddress']);
		// $con_warehouse = array('db'=>$_SESSION['conlist']['warehouse']['db_name'],'ip'=>$_SESSION['conlist'][$x]['ipaddress']);
		// $con_main = array('db'=>'lizgan_main','ip'=>'192.168.10.217');
		// if($_SESSION['conlist'][$x]){
			// $this->dbname = $_SESSION['conlist'][$x]['db_name'];
			// $this->ipadd = $_SESSION['conlist'][$x]['ipaddress'];
		// }else{
			// $this->dbname = $_SESSION['default_db'];
			// $this->ipadd = $_SESSION['default_ip'];
		// }
		$x = $_SESSION['connect'];
		if($_SESSION['conlist'][$x]){
			$this->dbname = $_SESSION['conlist'][$x]['db_name'];
			$this->ipadd = $_SESSION['conlist'][$x]['ipaddress'];
		}else{
			$this->dbname = $_SESSION['default_db'];
			$this->ipadd = $_SESSION['default_ip'];
		}
	}
	function multiexplode ($delimiters,$string) {
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return  $launch;
	}

	function pdoStyle($host,$db,$sql){
		set_time_limit(0);
		try {
			$conn = @new PDO("mysql:host=$host;dbname=$db;charset=utf8", $this->dbusername,$this->dbpass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare($sql); 
			$stmt->execute();
			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
			$results = $stmt->fetchAll();
			return $results;
		}catch(PDOException $e) {
			echo "$db Error: " . $e->getMessage()."<br/>";
		}
		$conn = null;
	}
	function pdoExec($host,$db,$sql){
		set_time_limit(0);
		try {
			$conn = @new PDO("mysql:host=$host;dbname=$db;charset=utf8", $this->dbusername,$this->dbpass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$stmt = $conn->prepare($sql); 
			if($stmt->execute()){
				return true;
			}else{
				return false;
			}
			
		}catch(PDOException $e) {
			echo "$db Error: " . $e->getMessage()."<br/>";
		}
		$conn = null;
	}
	function pdoTestConnection($host,$db){
		try{
			$dbh = new pdo("mysql:host=$host;dbname=$db",$this->dbusername,$this->dbpass,
							array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			return true;
		}
		catch(PDOException $e){
			//die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
			echo $e->getMessage();
			return false;
			
		}
	}
	function Nection(){
		$con = @new MySQLiContainer();
		if ($con->connect_error) {
			die('Connect Error: ' . $con->connect_error);
		}else{
			$qry = $con->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
			mysql_set_charset($qry,'utf8');
			return $qry;
		}
	}
	function getNextJournalID($type){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		$sql = "select max(id) as id from tbl_vouchering where type='$type' limit 1";
		$qry = $mysqli->query($sql);
		if($qry){
			$row = $qry->fetch_assoc();
			return $row['id']+1;
		}else{
			$_SESSION['error'] .= $mysqli->error."<br/>";
		}
		
		$mysqli->close();
	}
	function insertSJDiffApproach($jrefid,$sql1,$xdate,$entry,$type){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$qry = $mysqli->query($sql1);
			if($qry){
				$refid=$jrefid?$jrefid:$mysqli->insert_id;
				$j = $this->saveJournalEntryDiffApproach($refid,$xdate,$entry,$type);
				if($j){
					return $refid;
				}else{
					$_SESSION['error'].= "Error Journal Entry".$j."<br/>";
				}
			}else{
				$_SESSION['error'].= "Error Voucher Header:".$mysqli->error."<br/>";
			}
			$mysqli->close();
		}
	}
	function saveJournalEntryDiffApproach($refid,$xdate,$entry,$type){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			//$q=$mysqli->query("delete from tbl_journal_entry where refid='$refid' ".($_SESSION['connect']?" and center='{$_SESSION['connect']}'":""));
			$q=$mysqli->query("delete from tbl_journal_entry where refid='$refid' and `type`='$type'");
			foreach($entry as $key => $val){
				if($val['dr']!=""||$val['cr']!=""){
					$sql_header = "`refid`,`date`,`".implode("`,`",array_keys($val))."`";
					$sql_data = "$refid,'$xdate','".implode("','",array_values($val))."'";
					$account_info = $this->getWHERE($mysqli->query("select sub_account,sub_account_group,account_group,account_type,report_type,subsidiary,cash_in_bank from tbl_chart_of_account where account_code='{$val['account_code']}'"));
					if($account_info){
						$sql_header .= ",`".implode("`,`",array_keys($account_info))."`";
						$sql_data .= ",'".implode("','",array_values($account_info))."'";
					}
					$sql ="insert into tbl_journal_entry ($sql_header) values (".$sql_data.") on duplicate key update `date`=values(`date`),account_code=values(account_code),account_desc=values(account_desc),
					dr=values(dr),cr=values(cr),account_group=values(account_group),account_type=values(account_type),report_type=values(report_type),
					subsidiary=values(subsidiary),cash_in_bank=values(cash_in_bank)";
					$qry=$mysqli->query($sql);
					if(!$qry){
						$error.="($sql) \n Saving Journal: ".$mysqli->error;
					}
				}
			}
			if($error){
				$_SESSION['error'].=$error;
			}else{
				return true;
			}
			$mysqli->close();
		}
	}
	/*function saveJournalEntryDiffApproach_old($refid,$xdate,$entry){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$q=$mysqli->query("delete from tbl_journal_entry where refid='$refid' and center='{$_SESSION['connect']}'");
			$sql = "insert into tbl_journal_entry (refid,`date`,account_code,account_desc,center,dr,cr,account_group,
				account_type,report_type,subsidiary,cash_in_bank,ar_refid) values ";
			$flag=false;
			foreach($entry as $key => $val){
				if($val['dr_amt']!=""||$val['cr_amt']!=""){
					$account_info = $this->getWHERE($mysqli->query("select * from tbl_chart_of_account where account_code='{$val['code']}'"));
					if($flag)$sql_data.=",";
					$sql_data.="($refid,'$xdate','{$val['code']}','{$val['desc']}','{$_SESSION['connect']}','".str_replace(',','',$val['dr_amt'])."',
						'".str_replace(',','',$val['cr_amt'])."','{$account_info['account_group']}','{$account_info['account_type']}','{$account_info['report_type']}',
						'{$account_info['subsidiary']}','{$account_info['cash_in_bank']}','{$val['ar_refid']}')";
					$flag=true;
				}
				//echo $val['dr_amt']."|".$val['cr_amt']."<br/>";
			}
			$sql = $sql.$sql_data." on duplicate key update `date`=values(`date`),account_code=values(account_code),account_desc=values(account_desc),
				dr=values(dr),cr=values(cr),account_group=values(account_group),account_type=values(account_type),report_type=values(report_type),
				subsidiary=values(subsidiary),cash_in_bank=values(cash_in_bank)";
			$qry=$mysqli->query($sql);
			if(!$qry){
				$_SESSION['error'].="Saving Journal: ".$mysqli->error;
			}else{
				return true;
			}
			$mysqli->close();
		}
	}*/
	function autoSJ($jrefid,$glref,$total,$remarks){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$autogl = $mysqli->query("select * from tbl_auto_gl_entry where id='$glref'")->fetch_assoc();
			$sql="insert into tbl_vouchering (id,date,type,remarks,total,preparedby,`status`) values 
				('$jrefid','".date('Y-m-d')."','General Ledger','".$autogl['title']." ".$remarks."','".str_replace( ',','',$total)."','".$_SESSION['xid']."','ForApproval') 
				on duplicate key update `date`=values(`date`),type=values(type),remarks=values(remarks),total=values(total)";
			
				$account_dr=$mysqli->query("select * from tbl_chart_of_account where account_code in ({$autogl['dr_entry']})");
				while($row_dr=$account_dr->fetch_assoc()){
					$code[]=$row_dr['account_code'];
					$desc[]=$row_dr['account_desc'];
					$dr_amt[]=$total;
					$cr_amt[]=0;
				}
				
				$account_cr=$mysqli->query("select * from tbl_chart_of_account where account_code in ({$autogl['cr_entry']})");
				while($row_cr=$account_cr->fetch_assoc()){
					$code[]=$row_cr['account_code'];
					$desc[]=$row_cr['account_desc'];
					$dr_amt[]=0;
					$cr_amt[]=$total;
				}
				$refid = $this->insertSJ($jrefid,$sql,date('Y-m-d'),array('code'=>$code,'desc'=>$desc,'dr_amt'=>$dr_amt,'cr_amt'=>$cr_amt));
				if($refid){
					return $refid;
				}else{
					echo $refid;
				}
			$mysqli->close();
		}
	}
	function insertSJ($jrefid,$sql1,$xdate,$entry){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$qry = $mysqli->query($sql1);
			if($qry){
				$refid=$jrefid?$jrefid:$mysqli->insert_id;
				$j = $this->saveJournalEntry($refid,$xdate,$entry);
				if($j){
					return $refid;
				}else{
					echo "Error Journal Entry:".$j;
				}
			}else{
				echo "Error Voucher Header:".$mysqli->error;
			}
			$mysqli->close();
		}
	}
	function saveJournalEntry($refid,$xdate,$entry){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$del = $mysqli->query("delete from tbl_journal_entry where refid='$refid'");
			if($del){
				$sql = "insert into tbl_journal_entry (refid,`date`,account_code,account_desc,dr,cr,account_group,account_type,report_type,subsidiary,cash_in_bank) values ";
				$flag=false;
				for($i=0;$i<count($entry['code']);$i++){
					$account_info = $this->getWHERE($mysqli->query("select * from tbl_chart_of_account where account_code='{$entry['code'][$i]}'"));
					if($flag)$sql_data.=",";
					$sql_data.="($refid,'$xdate','{$entry['code'][$i]}','{$entry['desc'][$i]}','".str_replace(',','',$entry['dr_amt'][$i])."','".str_replace(',','',$entry['cr_amt'][$i])."','{$account_info['account_group']}','{$account_info['account_type']}','{$account_info['report_type']}','{$account_info['subsidiary']}','{$account_info['cash_in_bank']}')";
					$flag=true;
				}
				$sql = $sql.$sql_data." on duplicate key update `date`=values(`date`),account_code=values(account_code),account_desc=values(account_desc),dr=values(dr),cr=values(cr),account_group=values(account_group),account_type=values(account_type),report_type=values(report_type),subsidiary=values(subsidiary),cash_in_bank=values(cash_in_bank)";
				$qry=$mysqli->query($sql);
				if(!$qry){
					echo "Saving Journal: ".$mysqli->error;
				}else{
					return true;
				}
			}else{
				echo "Del Error: ".$mysqli->error;
			}
			$mysqli->close();
		}
	}
	function MySQLigetWHERElocal($col,$tbl,$where){
		$this->getBranch();
		$mysqli = @new mysqli("{$this->ipadd}", $this->dbusername,$this->dbpass, "{$this->dbname}");
		if ($mysqli->connect_error) {
			die('Connect Error: ' . $mysqli->connect_error);
		}else{
			$qry=$mysqli->query("select $col from $tbl $where");
			if(!$qry){
				echo "MySQLi getWhere Error: ".$mysqli->error;
			}else{
				return $qry->fetch_assoc();
			}
			$mysqli->close();
		}
	}
	function transferStockStatus($branch,$datewhere){
		$this->getBranch2($branch);
		$mysqli = @new mysqli($this->ipadd2, $this->dbusername,$this->dbpass, $this->dbname2);
		if ($mysqli->connect_error) {
			//die('Connect Error: ' . $mysqli->connect_error);
			return false;
		}else{
			$qry = $mysqli->query("select * from (select * from tbl_stocktransfer_header $datewhere) tbl where `from`='$branch' order by id desc");
			if(!$qry){
				echo $mysqli->error;
			}
			while($row = $qry->fetch_assoc()){
				$res[$row['id']]=$row;
			}
			return $res;
			$mysqli->close();
		}
	}
	function DelTransferStock($from,$id){
		$mysqli = @new mysqli($_SESSION['conlist']['main']['ipaddress'], $this->dbusername,$this->dbpass, $_SESSION['conlist']['main']['db_name']);
		if ($mysqli->connect_error) {
			//die('Connect Error: ' . $mysqli->connect_error);
			return false;
		}else{
			$qry = $mysqli->query("delete from tbl_stocktransfer_header where `from`='$from' and id='$id'");
			if(!$qry){
				echo $mysqli->error;
			}else{
				$del = $mysqli->query("delete from tbl_stocktransfer_items where `from`='$from' and id='$id'");
				if($del){
					echo "success";
				}
			}
			$mysqli->close();
		}
	}
	function getWHERE($qry){
		if($qry){
			return $qry->fetch_assoc();
		}else{
			echo "getWHERE Error...[$qry]";
		}
		
	}
	function resultArray($qry){
		while($row = $qry->fetch_assoc()){
			$result[] = $row;
		}
		return $result;
	}
	function strposa($haystack, $needles=array(), $offset=0) {
		$chr = array();
		foreach($needles as $needle) {
				$res = strpos($haystack, $needle, $offset);
				if ($res !== false) $chr[$needle] = $res;
				}
		if(empty($chr)) return false;
		return min($chr);
	}
	function uploadData($sql){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		mysql_set_charset($this->c1,'utf8');
		$qry = $this->c1->query($sql);
		if($qry){
			return "Successfully uploaded...";
		}else{
			return $this->c1->error;
		}
	}
	
	function transferStock($conto,$to,$tbl1,$tbl2,$refid,$refname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->getBranch2($conto);
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$this->c2 = $this->mysqliContainer->newConnection($this->ipadd2,$this->dbusername,$this->dbpass,$this->dbname2);
		$qry = $this->c1->query("select * from $tbl1 where $refname='$refid'");
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$col="insert into $tbl2 (`from`,`to`,`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update `to`=values(`to`),$col_update";
					$first=false;
				}
				if($flag){
					$data.=",";
				}
				$data.="('{$_SESSION['connect']}','$to','".implode("','",array_map('mysql_real_escape_string',$row))."')";
				$flag=true;
			}
			//echo $col.$data.$col_update;exit;
			$update_qry = $this->c2->query($col.$data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}else{
			return "Found records ".mysqli_num_rows($qry);
		}
		
	}
	function executePOupdate(){
		$this->updatePO("tbl_po_header");
		$this->updatePO("tbl_po_items");
	}
	function updateStocktransfer($refid,$to){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$qry = $this->c1->query("update tbl_stocktransfer_header set `status`='Received Stock' where id='".$refid."' and `to`='$to'");
		if($qry){
			return true;
		}else{
			return false;
		}
	}
	function getInvAllBranch(){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		if($this->c1){
			$qry = $this->c1->query("select * from tbl_inv_allbranch");
			if($qry){
				while($row = $qry->fetch_assoc()){
					$res[$row['sku_id']] = $row;
				}
				return $res;
			}else{
				return $this->c1->error;
			}
		}
	}
	/*function getInvAllBranch(){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		//$this->c1 = $this->mysqliContainer->newConnection($this->con_main['ip'],'admin','webadmin2010',$this->con_main['db']);
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,'admin','webadmin2010',$this->dbname);
		//$this->c1 = $this->mysqliContainer->newConnection($this->con_warehouse['ip'],'admin','webadmin2010',$this->con_warehouse['db']);
		if($this->c1){
			$qry = $this->c1->query("select * from tbl_inv_allbranch");
			if($qry){
				while($row = $qry->fetch_assoc()){
					$res[$row['sku_id']] = $row;
				}
				return $res;
			}else{
				return $this->c1->error;
			}
		}
	}*/
	function executeReceivedPO($ponum,$refid){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		if($this->c1){
			$qry = $this->c1->query("update tbl_po_header set status='RECEIVED RR No.: $refid',rr_num='$refid' where id='{$_REQUEST['poid']}'");
			if($qry){
				return "Update successfully...";
			}else{
				$_SESSION['error'] .= $this->c1->error;
			}
		}
	}
	function executePartialPO($ponum,$refid){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		if($this->c1){
			$qry = $this->c1->query("update tbl_po_header set status='Partial Received',rr_num='$refid' where id='{$_REQUEST['poid']}'");
			if($qry){
				return "Update successfully...";
			}else{
				$_SESSION['error'] .= $this->c1->error;
			}
		}
	}
	function updatePO($tblval){
		$this->mysqliContainer = new MySQLiContainer();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$this->c2 = $this->mysqliContainer->newConnection($this->con_warehouse['ip'],$this->dbusername,$this->dbpass,$this->con_warehouse['db']);
		if($this->c1 && $this->c2){
			$info = $this->getWHERE($this->c2->query("select max(datetime_changed) as max_datetime from $tblval limit 1"));
			$qry = $this->c1->query("select * from $tblval where datetime_changed >'{$info['max_datetime']}'");
			if(mysqli_num_rows($qry)!=0){
				$first=true;
				$flag=false;
				while($row = $qry->fetch_assoc()){
					if($first){
						$col="insert into $tblval (`".implode("`,`",array_keys($row))."`) values ";
						$f=false;
						foreach(array_keys($row) as $key=>$val){
							if($f){
								$col_update.=",";
							}
							$col_update.="`$val`=values(`$val`)";
							$f=true;
						}
						$col_update=" on duplicate key update $col_update";
						$first=false;
					}
					if($flag){
						$data.=",";
					}
					$data.="('".implode("','",$row)."')";
					$flag=true;
				}
				$update_qry = $this->c2->query($col.$data.$col_update);
				if($update_qry){
					return "Update successfully...";
				}else{
					return $this->c2->error;
				}
			}else{
				return "Found records ".mysqli_num_rows($qry);
			}
		}
	}
	function invBal($location,$skuid,$dt){
		
		switch($location){
			case'uclm':
				$coninfo = $this->con_uclm;
			break;
			case'ucmambaling':
				$coninfo =  $this->con_ucmambaling;
			break;
			case'ucmain':
				$coninfo =  $this->con_ucmain;
			break;
			case'ucbanilad':
				$coninfo =  $this->con_ucbanilad;
			break;
			case'warehouse':
				$coninfo =  $this->con_warehouse;
			break;
		}
		$mycon = new MySQLiContainer();
		$con = $mycon->newConnection($coninfo['ip'],$this->dbusername,$this->dbpass,$coninfo['db']);
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
		$ret = $con->query($sql)->fetch_assoc();
		//return $ret['total_inv'];
		//$ret['sql']=$sql;
		return $ret['total_inv'];
	}
	function updateInv(){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$sql="select skuid as sku_id,sum(inv_sales) inv_sales,sum(inv_po_receipts) inv_po_receipts,sum(inv_transfer_receipts) inv_transfer_receipts,sum(inv_transfer) inv_transfer,sum(inv_adj) inv_adjustment,sum(bal_forwarded) inv_bal_forwarded,sum(total_inv) base_inv from (
					select skuid,sum(qty*divmul) inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from tbl_sales_items group by skuid
					union
					select skuid,null inv_sales,sum(qty*divmul) inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Received from Supplier' group by skuid 
					union
					select skuid,null inv_sales,null inv_po_receipts,sum(qty*divmul) inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Received from Branch' group by skuid
					union
					select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,sum(qty*divmul) inv_transfer,null inv_adj,null bal_forwarded,null total_inv from tbl_stockout_items a left join tbl_stockout_header b on a.stockin_refid=b.id where status='Transfer Stock' group by skuid
					union
					select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,sum(qty*divmul) inv_adj,null bal_forwarded,null total_inv from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Adjustment' group by skuid
					union
					select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,sum(qty*divmul) bal_forwarded,null total_inv from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Bal Forwarded' group by skuid
					union
					(select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv from (
					select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from tbl_sales_items group by skuid
					union
					select skuid,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total from tbl_stockin_items group by skuid
					union
					select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from tbl_stockout_items group by skuid
					) tbl group by skuid)
					) as tbl group by skuid";
		$qry = $this->c1->query($sql);
		$col_data="";
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$colmain="insert into tbl_product_name (`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update $col_update";
					$first=false;
				}
				if($flag)$col_data.=",";
				$col_data.="('".implode("','",$row)."')";
				$flag=true;
			}
			$update_qry = $this->c1->query($colmain.$col_data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c1->error;
			}
		}
	}
	function SendInvToAdmin($colname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$this->c2 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$qry = $this->c2->query("select sku_id,base_inv from tbl_product_name");
		$col_data="";
		if(mysqli_num_rows($qry)!=0){
			$flag=false;
			$col="insert into tbl_inv_allbranch (`sku_id`,`$colname`) values ";
			while($row = $qry->fetch_assoc()){
				if($flag)$col_data.=",";
				$col_data.="('{$row['sku_id']}','{$row['base_inv']}')";
				$flag=true;
			}
			$update_qry = $this->c1->query($col.$col_data." on duplicate key update `$colname`=values(`$colname`)");
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}
	}
	function SendInvToAdminOld($colname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$this->c2 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$qry = $this->c2->query("select a.sku_id,product_name,barcode,cost,unit from tbl_product_name as a
				right join (select * from tbl_barcodes where divmul=1) as b on a.sku_id=b.sku_id 
				where a.sku_id is not null order by product_name asc");
		$col_data="";
		if(mysqli_num_rows($qry)!=0){
			$flag=false;
			$col="insert into tbl_inv_allbranch (`sku_id`,`$colname`) values ";
			$colmain="insert into tbl_product_name (`sku_id`,`base_inv`) values ";
			while($row = $qry->fetch_assoc()){
				$sql="SELECT COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv FROM (
					(SELECT timestamp dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_sales_items 
					WHERE skuid='{$row['sku_id']}') 
					UNION
					(SELECT `date` dt,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total FROM tbl_stockin_items 
					left join tbl_stockin_header on tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='{$row['sku_id']}')
					UNION
					(SELECT `date` dt,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total FROM tbl_stockout_items 
					left join tbl_stockout_header on tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='{$row['sku_id']}')
					) AS tbl";
					$ret = $this->c2->query($sql)->fetch_assoc();
				if($flag)$col_data.=",";
				$col_data.="('{$row['sku_id']}','{$ret['total_inv']}')";
				$flag=true;
			}
			//return $col.$col_data." on duplicate key update `$colname`=values(`$colname`)";
			$this->c2->query($colmain.$col_data." on duplicate key update base_inv=values(base_inv)");
			$update_qry = $this->c1->query($col.$col_data." on duplicate key update `$colname`=values(`$colname`)");
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}
	}
	function copyTbl($tblname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$this->c2 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$qry = $this->c1->query("select * from $tblname");
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$col="insert into $tblname (`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update $col_update";
					$first=false;
				}
				if($flag){
					$data.=",";
				}
				$data.="('".implode("','",array_map('mysql_real_escape_string', $row))."')";
				$flag=true;
			}
			//mysqli_free_result($row);
			$update_qry = $this->c2->query($col.$data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}
	}
	function updateMainTbl($tblname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$this->c2 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$qry = $this->c1->query("select * from $tblname");
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$col="insert into $tblname (`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update $col_update";
					$first=false;
				}
				if($flag){
					$data.=",";
				}
				$data.="('".implode("','",array_map('mysql_real_escape_string', $row))."')";
				$flag=true;
			}
			//mysqli_free_result($row);
			$update_qry = $this->c2->query($col.$data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}
	}
	
	function sendTbl($tblname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$newtblname = $tblname."_{$_SESSION['connect']}";
		$this->c1 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$this->c2 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$c=$this->c1->query("SHOW CREATE TABLE $tblname")->fetch_assoc();
		$cqry = $this->c2->query(str_replace("CREATE TABLE `$tblname`","CREATE TABLE IF NOT EXISTS `$newtblname`",$c['Create Table']));
		$qry = $this->c1->query("select * from $tblname where date_format(datetime_changed,'%Y-%m-%d') <= '".date('Y-m-d')."' and  date_format(datetime_changed,'%Y-%m-%d') >='".date('Y-m-d', strtotime('-5 days'))."'");
		if(!$qry){
			return $this->c1->error;
		}
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$col="insert into $newtblname (`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update $col_update";
					$first=false;
				}
				if($flag){
					$data.=",";
				}
				$data.="('".implode("','",array_map('mysql_real_escape_string', $row))."')";
				$flag=true;
			}
			//mysqli_free_result($row);
			//return $col.$data.$col_update;exit;
			$update_qry = $this->c2->query($col.$data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}
	}
	function updateItems($tblname){
		$this->mysqliContainer = new MySQLiContainer();
		$this->getBranch();
		$this->c1 = $this->mysqliContainer->newConnection($_SESSION['conlist']['main']['ipaddress'],$this->dbusername,$this->dbpass,$_SESSION['conlist']['main']['db_name']);
		$this->c2 = $this->mysqliContainer->newConnection($this->ipadd,$this->dbusername,$this->dbpass,$this->dbname);
		$info1 = $this->getWHERE($this->c1->query("select count(*) as xcount from $tblname limit 1"));
		$info2 = $this->getWHERE($this->c2->query("select count(*) as xcount from $tblname limit 1"));
		if($info1['xcount']!=$info2['xcount']){
			$qry = $this->c1->query("select * from $tblname");
		}else{
			$info = $this->getWHERE($this->c2->query("select max(datetime_changed) as max_datetime from $tblname limit 1"));
			$qry = $this->c1->query("select * from $tblname where datetime_changed >'{$info['max_datetime']}'");
		}
		if(mysqli_num_rows($qry)!=0){
			$first=true;
			$flag=false;
			while($row = $qry->fetch_assoc()){
				if($first){
					$col="insert into $tblname (`".implode("`,`",array_keys($row))."`) values ";
					$f=false;
					foreach(array_keys($row) as $key=>$val){
						if($f){
							$col_update.=",";
						}
						$col_update.="`$val`=values(`$val`)";
						$f=true;
					}
					$col_update=" on duplicate key update $col_update";
					$first=false;
				}
				if($flag){
					$data.=",";
				}
				$data.="('".implode("','",array_map('mysql_real_escape_string', $row))."')";
				$flag=true;
			}
			//mysqli_free_result($row);
			$update_qry = $this->c2->query($col.$data.$col_update);
			if($update_qry){
				return "Update successfully...";
			}else{
				return $this->c2->error;
			}
		}else{
			return "Found records ".mysqli_num_rows($qry);
		}
	}
	function getBranch(){
		$x=$_SESSION['connect'];
		if($_SESSION['conlist'][$x]){
			$this->dbname = $_SESSION['conlist'][$x]['db_name'];
			$this->ipadd = $_SESSION['conlist'][$x]['ipaddress'];
		}else{
			$this->dbname = $_SESSION['default_db'];
			$this->ipadd = $_SESSION['default_ip'];
		}
	}
	function getBranch2($connection){
		$x=$connection;
		if($_SESSION['conlist'][$x]){
			$this->dbname2 = $_SESSION['conlist'][$x]['db_name'];
			$this->ipadd2 = $_SESSION['conlist'][$x]['ipaddress'];
		}else{
			$this->dbname2 = $_SESSION['default_db'];
			$this->ipadd2 = $_SESSION['default_ip'];
		}
	}
	function __destruct(){
		if($this->c1){
			$this->c1->close();
		}
		if($this->c2){
			$this->c2->close();
		}
	}
}


?>