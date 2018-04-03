<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
if(isset($_GET['skuid'])) {
$sql = "SELECT imageType,imageData FROM tbl_product_name WHERE sku_id=" . $_GET['skuid'];
$result = mysql_query("$sql") or die("<b>Error:</b> Problem on Retrieving Image BLOB<br/>" . mysql_error());
$row = mysql_fetch_array($result);
print_r($row);
header("Content-type: " . $row["imageType"]);
echo $row["imageData"];
}
mysql_close($conn);
?>