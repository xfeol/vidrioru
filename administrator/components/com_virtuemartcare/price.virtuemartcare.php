<?php
////////////////////////////////////////////////////////
// Компонент сервиса VirtuemartCare	                  //
// Разработан для Joomla 1.5.x 						  //
// 2012 (C) Beagler   (beagler.ru@gmail.com)          //
////////////////////////////////////////////////////////
header('Content-Type: text/html; charset=utf-8');
defined('_JEXEC') or die('Restricted access');
if( !isset( $mosConfig_absolute_path ) ) {
	$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}
global $mosConfig_absolute_path, $page;
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );
require_once( CLASSPATH.'ps_product_discount.php' );
require_once( CLASSPATH.'ps_manufacturer.php' );
require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category = new ps_product_category();
require_once (CLASSPATH . 'ps_shopper_group.php') ;

global  $ps_html, $my, $root_label, $mosConfig_allowUserRegistration, $jscook_type, $jscookMenu_style, $jscookTree_style, $VM_LANG, $sess, $mm_action_url;
$db = new ps_DB( ) ;
$db1 = new ps_DB( ) ;
function list_shopper_groups($name,$shopper_group_id='0', $extra='') {
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		global $perm;
		$dbm = new ps_DB;

		if( !$perm->check("admin")) {
			$qm  = "SELECT shopper_group_id,shopper_group_name,vendor_id,'' AS vendor_name FROM #__{vm}_shopper_group ";
			$qm .= "WHERE vendor_id = '$ps_vendor_id' ";
		}
		else {
			$qm  = "SELECT shopper_group_id,shopper_group_name,#__{vm}_shopper_group.vendor_id,vendor_name FROM #__{vm}_shopper_group ";
			$qm .= ",#__{vm}_vendor WHERE #__{vm}_shopper_group.vendor_id = #__{vm}_vendor.vendor_id ";
		}
		$qm .= "ORDER BY shopper_group_name";
		$dbm->query($qm);
		$shopper_groups[0] = 'Все';
		while ($dbm->next_record()) {
			$shopper_groups[$dbm->f("shopper_group_id")] = $dbm->f("shopper_group_name"); 
		}
		return ps_html::selectList( $name, $shopper_group_id, $shopper_groups, 1, '', $extra );
	}
function list_manufacturer($manufacturer_id='0') {

		$dbm = new ps_DB;

		$qm = "SELECT manufacturer_id as id,mf_name as name FROM #__{vm}_manufacturer ORDER BY mf_name";
		$dbm->query($qm);
		$dbm->next_record();

		// If only one vendor do not show list
		if ($dbm->num_rows() == 1) {

			echo '<input type="hidden" name="manufacturer_id" value="'. $dbm->f("id").'" />';
			echo $dbm->f("name");
		}
		elseif( $dbm->num_rows() > 1) {
			$dbm->reset();
			$array = array();
			$array[0] = 'Все';
			while ($dbm->next_record()) {
				$array[$dbm->f("id")] = $dbm->f("name");
			}
			$code = ps_html::selectList('manufacturer_id', $manufacturer_id, $array ). "<br />\n";
			echo $code;
		}
		else  {
			echo '<input type="hidden" name="manufacturer_id" value="1" />Please create at least one Manufacturer!!';
		}
	}
//begin form
//$IMG_DATE=JRequest::getint('IMG_DATE', 0);
		$DECEL = JRequest::getfloat('DECEL', 0);
       $RSIGN = JRequest::getint('RSIGN', 0);
	    $ALG = JRequest::getvar('alg', '0');
       $ROUNDED = JRequest::getint('ROUNDED', 0);
		$shopper_group_id = JRequest::getint('shopper_group_id', 0);
		$manufacturer_id = JRequest::getint('manufacturer_id', '');
		if( !empty($_REQUEST['product_categories']) && is_array($_REQUEST['product_categories'])) {
			$product_categories=$_REQUEST['product_categories'];
			foreach( $_REQUEST['product_categories'] as $catid ) $my_categories[$catid] = '1';
		} else {
			$product_categories='';
			$my_categories = array();
		}
		if($manufacturer_id==0) $manufacturer_id='';	
		$test = JRequest::getint('test', 0);
		$process = JRequest::getint('process', 0);		
?>
<form name="price" method="post" action="/administrator/index.php">
<fieldset>
		<legend>Сервис изменения цен Virtuemart</legend>
		<div style="float:left;padding-right:5px;"><img align="middle" border="0" src="components/com_virtuemartcare/images/price.jpg"></div>
		<div style="color: #0B55C4;font-size: 12px;">
		Сервис цен никак не связан со скидками. <br>Все изменения будут происходить с полем <b>product_price</b> таблицы <b>#__{vm}_product_prices</b> причем независимо от валюты. <br>
		Для того чтобы посмотреть какие в базе текущие цены и во что они превратятся - используйте режим "<b>пробный тест</b>".<br>
		При округлении значение количества знаков после запятой может быть отрицательным. Например '-1' - округление до десятков рублей.<br>
		Значение изменения может быть отрицательным и положительным, вводите только цифры. Разделитель дробной части - "<b>.</b>"</div><br style="clear:both;">
		<fieldset style="float:left;width: 625px;">
		<legend><b>Формулы расчета:</b></legend>
		<table style="color: #0B55C4;font-size: 12px;"><tr><td>
		"Прибавить или отнять значение изменения"<br>
		"Уменьшить или увеличить на %"  <br>
		"Приравнять цены к значению изменения "
		</td><td>
		- новая_цена=новая_цена+значение_изменения<br>
		- новая_цена=новая_цена+(цена/100)*значение_изменения<br>
		- новая_цена=значение_изменения</td></tr></table>
		</fieldset>
		<br style="clear:both;">
		<fieldset style="float:left;width: 305px;height: 350px;">
			<legend>Задаем условия отбора товаров</legend>
			<fieldset>
			<legend>Выбираем категории (Ctrl - несколько)</legend>
		<table><tr>
		<td><?php 
			$ps_product_category->list_all("product_categories[]", "", $my_categories, 10, false, true); 
		        		?>
		</td></tr></table>
		</fieldset>
		<fieldset>
			<legend>Выбираем производителя</legend>
		<table><tr>
		<td>
		<?php list_manufacturer($manufacturer_id);  ?>
		
		</td>
		</tr></table></fieldset>
		<fieldset>
			<legend>Выбираем группу пользователей</legend>
		<table><tr>
		<td><?php
		echo list_shopper_groups( "shopper_group_id", $shopper_group_id ) ;
		?>
		</td>
		</tr>
		</table>
			</fieldset>
			<input type=submit value=Старт>
			<span style="float:right;">
			<input type="checkbox" name="test" class="inputbox" value="1" <?php if ($test == '1') echo 'checked="checked"'; ?>/> пробный тест (без сохранения в базе)
			</span>
		</fieldset>
		<fieldset style="float:left;width: 305px;height: 350px;">
			<legend>Алгоритм изменения цен</legend>
			<fieldset>
			<legend>Введите значение изменения</legend>
			<table><tr><td class="labelcell" width="180px">
			значение изменения может быть </br>отрицательным и положительным,<br>вводите только цифры
			</td>
			<td>
			<input type="text" name="DECEL" class="inputbox" value="<?php echo $DECEL; ?>" />
			</td></tr></table></fieldset>
		<fieldset>
			<legend>Выберите способ изменения цен:</legend>
			<fieldset>
			<input type="radio" name="alg" value="1" <?php if ($ALG == '1') echo 'checked="checked"'; ?>> Прибавить или отнять значение изменения
			</fieldset>
			<fieldset>
			<input type="radio" name="alg" value="2" <?php if ($ALG == '2') echo 'checked="checked"'; ?>> Уменьшить или увеличить на %
			</fieldset>
			<fieldset>
			<input type="radio" name="alg" value="3" <?php if ($ALG == '3') echo 'checked="checked"'; ?>> Приравнять цены к значению изменения
			</fieldset>
			</fieldset>
			<fieldset>
			<legend>Округление</legend>
			<table><tr>
                    <td class="labelcell" width="180px">Включить округление результата<br/>Количество знаков после запятой</td>
                    <td>
					<input type="checkbox" name="ROUNDED" class="inputbox" <?php if ($ROUNDED == '1') echo 'checked="checked"'; ?>  value="1" /> 
					<br/><input type="text" name="RSIGN" size="5" class="inputbox" value="<?php echo $RSIGN; ?>" /> 
                    </td>
                    
                </tr></table></fieldset>
		</fieldset> 
		</fieldset>
		<input type="hidden" value="com_virtuemartcare" name="option">
        <input type="hidden" value="price" name="task">
		<input type="hidden" value="1" name="process">
        
<form>
<?php
if ($process=='1') {
if ($test=='1')
{ ?>
<p style="color: #0B55C4;font-size: 12px;">
Пробный тест, изменений в базе <b>не делаем</b>. <br>
Внимательно просмотрите таблицу товаров, <b>последнюю колонку</b>. <br>
Если сейчас снять галку "пробный тест" и <b>нажать "Старт" - изменения запишутся в базу</b>.
</p>
<?php
$where='';
$fields='a.product_id, a.product_name, a.product_sku, a.product_publish, a.product_in_stock, p.product_price, p.product_currency, p.product_price_id, mn.mf_name, cn.category_name, s.shopper_group_name  ';
$base='`#__{vm}_product` a, `#__{vm}_product_price` p, `#__{vm}_product_mf_xref` m, `#__{vm}_manufacturer` mn, `#__{vm}_product_category_xref` c, `#__{vm}_category` cn, `#__{vm}_shopper_group` s ';
if($manufacturer_id>0) {
$where.= ' AND m.manufacturer_id='.$manufacturer_id.' ';
}			
if(is_array($product_categories)) {
$where.= ' AND c.category_id in ('.implode(',',$product_categories).') ';
}
if($shopper_group_id>0) {
$where.= ' AND s.shopper_group_id='.$shopper_group_id.' ';
}
$q='SELECT '.$fields.' FROM '.$base.'
			where a.product_id=p.product_id 
			 AND a.product_id=m.product_id AND m.manufacturer_id=mn.manufacturer_id
			 AND a.product_id=c.product_id AND c.category_id=cn.category_id
			 AND p.shopper_group_id=s.shopper_group_id '.$where;
		
require_once( CLASSPATH . "htmlTools.class.php" );
// Create the List Object with page navigation
$listObj = new listFactory();

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"10px\"", 
					"product_id" => "width=\"40px\"",
					"product_name" => "",
					"product_sku" => "",
					"опубликовано" => "width=\"30px\"",
					"остаток" => "width=\"30px\"",
					"manufacturer_name" => "width=\"80px\"",
					"category_name" => "",
					"shopper_group_name" => "width=\"80px\"",
					"product_price" => "width=\"70px\"",
					"валюта" => "width=\"30px\"",
					"новая цена" => "width=\"70px\"",

				);
$listObj->writeTableHeader( $columns );

$db->query($q);
$i = 0;

while ($db->next_record()) {
	$i++;
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $i );
	$listObj->addCell( $db->f("product_id") );
	$listObj->addCell( $db->f("product_name") );
	$listObj->addCell( $db->f("product_sku") );
	$listObj->addCell( $db->f("product_publish") );
	$listObj->addCell( $db->f("product_in_stock") );
	$listObj->addCell( $db->f("mf_name") );
	$listObj->addCell( $db->f("category_name") );
	$listObj->addCell( $db->f("shopper_group_name") );
	$listObj->addCell( $db->f("product_price") );
	$listObj->addCell( $db->f("product_currency") );
	$price_new=$db->f("product_price");
	if(!empty($DECEL) && $DECEL!=0){
		switch($ALG){
			case '1':
				$price_new+=$DECEL;
				break;
			case '2':
				$price_new+=($price_new/100)*$DECEL;
				break;
			case '3':
				$price_new=$DECEL;
				break;
		}
		if($ROUNDED=='1' ) $price_new=round($price_new,$RSIGN);
	
	}
	
	$listObj->addCell( '<b>'.$price_new.'</b>' );
	
}
$listObj->writeTable();

$listObj->endTable();

} else {
	if(empty($DECEL) || $DECEL==0 || $ALG=='0') { 
		echo "<h1>Установите значения алгоритма! (Если хотите просто увидить товар - включите 'пробный тест')</h1>";
	return;
	}
	$where='';
	$fields='a.product_id, a.product_name, a.product_sku, a.product_publish, a.product_in_stock, p.product_price, p.product_currency, p.product_price_id, mn.mf_name, cn.category_name, s.shopper_group_name  ';
	$base='`#__{vm}_product` a, `#__{vm}_product_price` p, `#__{vm}_product_mf_xref` m, `#__{vm}_manufacturer` mn, `#__{vm}_product_category_xref` c, `#__{vm}_category` cn, `#__{vm}_shopper_group` s ';
	if($manufacturer_id>0) {
	$where.= ' AND m.manufacturer_id='.$manufacturer_id.' ';
	}			
	if(is_array($product_categories)) {
	$where.= ' AND c.category_id in ('.implode(',',$product_categories).') ';
	}
	if($shopper_group_id>0) {
	$where.= ' AND s.shopper_group_id='.$shopper_group_id.' ';
	}
	$q='SELECT '.$fields.' FROM '.$base.'
				where a.product_id=p.product_id 
				 AND a.product_id=m.product_id AND m.manufacturer_id=mn.manufacturer_id
				 AND a.product_id=c.product_id AND c.category_id=cn.category_id
				 AND p.shopper_group_id=s.shopper_group_id '.$where;
			
require_once( CLASSPATH . "htmlTools.class.php" );
// Create the List Object with page navigation
$listObj = new listFactory();

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"10px\"", 
					"ok" => "width=\"10px\"",
					"product_id" => "width=\"40px\"",
					"product_name" => "",
					"product_sku" => "",
					"опубликовано" => "width=\"30px\"",
					"остаток" => "width=\"30px\"",
					"manufacturer_name" => "width=\"80px\"",
					"category_name" => "",
					"shopper_group_name" => "width=\"80px\"",
					"product_price" => "width=\"70px\"",
					"валюта" => "width=\"30px\"",
					"старая цена" => "width=\"70px\"",

				);
$listObj->writeTableHeader( $columns );

$db->query($q);
$i = 0;

while ($db->next_record()) {
	$i++;
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $i );
	$listObj->addCell(vmCommonHTML::getYesNoIcon( 'Y', 'Цена установлена'));
	$listObj->addCell( $db->f("product_id") );
	$listObj->addCell( $db->f("product_name") );
	$listObj->addCell( $db->f("product_sku") );
	$listObj->addCell( $db->f("product_publish") );
	$listObj->addCell( $db->f("product_in_stock") );
	$listObj->addCell( $db->f("mf_name") );
	$listObj->addCell( $db->f("category_name") );
	$listObj->addCell( $db->f("shopper_group_name") );
	$price_new=$db->f("product_price");
	if(!empty($DECEL) && $DECEL!=0){
		switch($ALG){
			case '1':
				$price_new+=$DECEL;
				break;
			case '2':
				$price_new+=($price_new/100)*$DECEL;
				break;
			case '3':
				$price_new=$DECEL;
				break;
		}
		if($ROUNDED=='1' ) $price_new=round($price_new,$RSIGN);
	
	$q1='UPDATE `#__{vm}_product_price` set product_price='.$price_new.'
		WHERE product_price_id='.$db->f("product_price_id");
	$db1->query($q1);
	}
	
	$listObj->addCell( '<b>'.$price_new.'</b>' );
	$listObj->addCell( $db->f("product_currency") );
	$listObj->addCell( $db->f("product_price") );
	
	
	
}
$listObj->writeTable();

$listObj->endTable();


}
}
?>