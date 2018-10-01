<?php

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) );

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

function vm_transliterate($name)
{
    $tbl= array(
        'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
        'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
        'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
        'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
        'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
        'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
        'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
        'Ё'=>"Yo", 'Х'=>"H", 'Ц'=>"Ts", 'Ч'=>"Ch", 'Ш'=>"Sh", 'Щ'=>"Shch", 'Ъ'=>"", 'Ь'=>"",
        'Ю'=>"Yu", 'Я'=>"Ya"
    );
    
    $transliteration = strtr($name, $tbl);
    
    $transliteration = JFilterOutput::StringURLSafe($transliteration);
    return $transliteration;
}

$hostname = "localhost";
$username = "vidrioru";
$password = "%Ow}7y&20vuj";
$dbName = "vidrioru_bazavidrioru"; 
$category = "jos_vm_category";
$category_xref = "jos_vm_category_xref";
$userstable = "jos_vm_product";
$pricetable = "jos_vm_product_price";
$usersprice = "jos_vm_product_price";

$product_category_xref =  "jos_vm_product_category_xref";
mysql_connect($hostname,$username,$password) OR DIE("cannot connect to DB");
mysql_select_db($dbName) or die(mysql_error());
mysql_query("SET names 'UTF8'") or die(mysql_error());

echo"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo"<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n";
echo"<yml_catalog date=\"";
echo date('Y-m-d H:i');
echo"\">\n"; 
echo"<shop>\n";

echo"<name>Интернет-магазин Vidrio.ru</name>\n";
echo"<company>ООО &quot;Аклайм&quot;</company>\n";
echo"<url>http://vidrio.ru</url>\n";
echo"<currencies>\n";
echo"<currency  id=\"RUR\" rate=\"1\"/>\n";
echo"</currencies>\n";
echo"<categories>\n";
$query_cat = "SELECT * FROM $category_xref"; 
$res_cat = mysql_query($query_cat) or die(mysql_error()); 
$rw=1; 
while ($row_cat=mysql_fetch_array($res_cat)) { 
$cat_parent_id=$row_cat['category_parent_id'];
$cat_child_id=$row_cat['category_child_id'];
$query2 = "SELECT category_name FROM $category WHERE category_id=".$row_cat['category_child_id'];
$res_cat1 = mysql_query($query2) or die(mysql_error()); 
$name_cat=mysql_fetch_array($res_cat1);
$cat_name=htmlspecialchars($name_cat['category_name']);
if ($cat_parent_id==0) {
echo"<category id=\"".$cat_child_id."\">".$cat_name."</category>\n";
}
else {
echo"<category id=\"".$cat_child_id."\" parentId=\"".$cat_parent_id."\">".$cat_name."</category>\n";
}
$rw++;
}
echo"</categories>\n";
echo"<offers>\n";
$query = "SELECT * FROM $userstable INNER JOIN $usersprice ON $userstable.product_id = $usersprice.product_id ";
$query .= "WHERE $userstable.product_parent_id = 0 AND $usersprice.product_price > 0";
$res = mysql_query($query) or die(mysql_error()); 
$rw=1; 
while ($row=mysql_fetch_array($res)) { 



$product_full_image = "http://vidrio.ru/components/com_virtuemart/shop_image/product/".$row['product_full_image'];
$product_name = $row['product_name'];
$product_s_desc = $row['product_s_desc'];
$query1 = "SELECT product_price FROM $pricetable WHERE product_id=".$row['product_id'];
$res1 = mysql_query($query1) or die(mysql_error()); 
$price=mysql_fetch_array($res1);
$product_price = substr($price['product_price'], 0, -3);
$query3 = "SELECT category_id FROM $product_category_xref WHERE product_id=".$row['product_id'];
$res3 = mysql_query($query3) or die(mysql_error()); 
$product_cat_id1=mysql_fetch_array($res3);
$product_cat_id=$product_cat_id1['category_id'];

$prod_cat_name_req = "SELECT category_name FROM $category WHERE category_id=$product_cat_id";
$prod_name1= mysql_query($prod_cat_name_req) or die(mysql_error() ." $prod_cat_name_req query3:$query3");
$prod_cat_name = mysql_fetch_array($prod_name1);

$url="http://vidrio.ru/home/category/product/".$product_cat_id."-".vm_transliterate($prod_cat_name['category_name'])."/".$row['product_id'] . "-" . vm_transliterate($product_name);

echo"<offer id=\"".$rw."\" available=\"true\" bid=\"11\">\n";
echo"<url>".$url."</url>\n";
echo"<price>$product_price</price>\n";
echo"<currencyId>RUR</currencyId>\n";
echo"<categoryId>".$product_cat_id."</categoryId>\n";
echo"<picture>".$product_full_image ."</picture>\n";
echo"<name>".$product_name."\"</name>\n";
echo"<description>".htmlspecialchars($product_s_desc)." "."</description>\n";
echo"</offer>";
$rw++;
}
echo"</offers>\n";
echo"</shop>\n";
echo"</yml_catalog>\n";
?>
