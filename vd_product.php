<?php

print "php test file";


$host = 'localhost';
$user = 'vidrioru_root';
$password = 'omp3FE06q8';
$db_name = 'vidrioru_bazavidrioru';

$link = mysql_connect($host, $user, $password);
mysql_select_db($db_name);

mysql_query("SET NAMES utf8");

$sqlstr = mysql_query("SELECT * FROM jos_vm_product");
while ($row = mysql_fetch_array($sqlstr)) {
?>
    <p><?= $row['product_id'] ?></p>
<?php
}

echo 'Array $_SERVER';
echo '<pre>';
print_r($_SERVER);
print_r($_SESSION);

