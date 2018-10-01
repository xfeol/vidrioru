<?php
/**
* VirtueMart SEF Router
* NOTE: THIS SCRIPT REQUIRES THE PHPSHOP COMPONENT!
*
* @copyright (C) 2010 JFactory Project
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* This extention is Free Software.
*/

// формируемый путь: [pagetype]/[segment_1]/[seqment_2]/.../[segment_1_value]/[segment_2_value]/...

//no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// функция возвращает таблицу соответсвия между виртовским именем страницы и sef заменителем
function vm_getPageTypes($flip = false)
{
	$pageTypes = array(
		'shop.browse' => 'browse',
		'shop.product_details' => 'details',
		'shop.feed' => 'feed',
		'shop.ask' => 'ask',
		'shop.waiting_list' => "notify",
		'shop.search' => "search",
		'shop.getfile' => "getfile",
		'shop.registration' => "registration",
		'shop.recommend' => "recommend",
		'shop.tos' => "terms",
		'shop.cart' => "cart",
		'checkout.index' => 'checkout',
		'account.billing' =>"billing",
		'account.shipto' => "shipto",
		'account.shipping' => "shipping",
		'account.index' => "account",
		'account.order_details' => "order:details",
		'store.index' => 'administration'
	);
	return ($flip) ? array_flip($pageTypes) : $pageTypes;
}

// функция возвращает таблицу соответсвия между виртовским именем переменной и sef заменителем
function vm_getSectionTypes($flip = false)
{
	$sectionTypes = array(
		'category_id'			=> 'category',
		'manufacturer_id'	=> 'manufacturer',
		'product_id'			=> 'product',
		'file_id'					=> 'file',
		'order_id'				=> 'order',
		'ssl_redirect'		=> 'ssl',
		'redirected'			=> 'redirected'
	);
	return ($flip) ? array_flip($sectionTypes) : $sectionTypes;
}

// функция сортирует массив переменных так чтобы вначале шла category_id, затем manufacturer_id и наконец product_id
function vm_sort($f1, $f2)
{
	if ($f1 == 'category_id') {
		if ($f2 == 'manufacturer_id') return 1;
		if ($f2 == 'product_id') return -1;
	}
	if ($f1 == 'manufacturer_id') {
		if ($f2 == 'category_id') return 1;
		if ($f2 == 'product_id') return -1;
	}
	if ($f1 == 'product_id') {
		if ($f2 == 'category_id') return 1;
		if ($f2 == 'manufacturer_id') return 1;
	}
}

// функция создает sef ссылку из массива переменных запроса
function virtuemartBuildRoute(&$query)
{
	$segments = array();

	$sections = array();
	$sectionTypes = vm_getSectionTypes();

	$page = null;
	$pageTypes = vm_getPageTypes();

	if (isset($query['page'])) {
		$page = $query['page'];
		unset($query['page']);
	}

	if ( $page && isset($pageTypes[$page]) && !(($page == 'shop.browse' && isset($query['category_id'])) || ($page == 'shop.product_details' && isset($query['product_id'])) ) ) {
		$segments[] = $pageTypes[$page];
	}

	foreach ($query as $key => $value)
	{
		if ( in_array($key, array_keys($sectionTypes)) ) {
			$sections[] = $key;
		}
	}

	uasort($sections, 'vm_sort');

	foreach ($sections as $section)
	{
		$segments[] = $sectionTypes[$section];
	}


	foreach ($sections as $section)
	{
		$alias = vm_getAlias($section, $query[$section]);
		$segments[] = $query[$section] . ( ($alias) ? ':'.$alias : '' );
		unset($query[$section]);
	}

	if (isset($query['limitstart']))
	{
	    $limitstart=$query['limitstart'];
	    unset($query['limitstart']);
	}
	if (isset($query['start']))
	{
	    $limitstart=$query['start'];
	    unset($query['start']);
	}
	$limit = 60;
	if (isset($query['limit']))
	{
	    $limit = $query['limit'];
	    unset($query['limit']);
	}
    
	if (isset($limitstart))
	{
	    $segments[] = "page" . $limitstart . "-" . $limit;
	}

	// забьем на эти параметры ;)
	if (isset($query['flypage'])) {
		unset($query['flypage']);
	}
	if (isset($query['pop'])){
		unset($query['pop']);
	}

	// дело сделано ;)
	return $segments;
}

// функция конвертит сегменты красивого урла в переменные запроса
function virtuemartParseRoute($segments)
{
	// массив который вернем как результат
	$vars = array();

	// таблица соответствия типов страниц, отраженная
	$pageTypes = vm_getPageTypes( true );

	// если первый сегмент - не тип страницы, то вставляем пустой сегмент, чтобы не путать логику дальнейшей работы
	isset($pageTypes[$segments[0]]) || array_unshift($segments, null);

	// массив распознанных переданных переменных
	$sections = array();
	$sectionTypes = vm_getSectionTypes( true );

	// распознаем имена переменных
	$i = 1;	$break = false;
	while ( ($i < count($segments)) && !$break )
	{
		if ( in_array($segments[$i], array_keys($sectionTypes)) ) {
			$sections[$segments[$i]] = 0;
		} else {
			$break = true;
		}
		$i++;
	}

	// распознаем значения переменных
	for ($i = 1; $i < count($sections) + 1; $i++)
	{
		$sections[$segments[$i]] = isset($segments[$i +count($sections)]) ? $segments[$i +count($sections)] : null;
	}

	// забиваем в результирующую переменную
	foreach ( $sections as $key => $value )
	{
		$vars[$sectionTypes[$key]] = $value;
	}

	$i = count($sections) * 2 + 1;
	//echo "i =  " . $i . "  segments: ". count($segments). "   sections:" . count($sections);

	$break = false;
	$page = NULL;
	while ( ($i < count($segments)) && !$break)
	{
	    //echo "{ seg: " . $segments[$i] . "}";
	    if (strncmp($segments[$i], "page", 4) == 0)
	    {
		$break = true;
		$page = $segments[$i];
	    }
	    $i++;
	}
	$limitstart = 0;
	$limit = 60;

	// забиваем тип страницы
	$vars['page'] = isset( $pageTypes[$segments[0]] ) ? $pageTypes[$segments[0]] : false;

	// если тип не указан, устанавливаем в зависимости от набора переменных как shop.browse, shop.product_details или shop.index
	if ( !$vars['page'] ) {
		if ( isset($vars['product_id']) ) {
			$vars['page'] = 'shop.product_details';
		} elseif ( isset($vars['category_id']) ) {
			$vars['page'] = 'shop.browse';
			if ($page)
			{
			    $page = substr($page, 4);
			    $el = explode(":", $page);
			    $vars['limitstart'] = $el[0];
			    $vars['limit'] = $el[1];
			    //echo "limit=" . $el[0] . " limitstart=" . $el[1];
			} else {
			    $vars['limitstart'] = $limitstart;
			    $vars['limit'] = $limit;
			}
		} elseif ( isset($vars['manufacturer_id']) ) {
			$vars['page'] = 'shop.browse';
		} elseif ( JRequest::getVar('keyword') ) {
			$vars['page'] = 'shop.browse';
		} else {
			$vars['page'] = 'shop.index';
		}
	}

	// дело сделано ;)
	return $vars;
}



// возвращает категорию по id как объект
function &vm_getCategory($id)
{
	static $categories;
	if( isset( $categories[$id] ) ) return $categories[$id];

	$db			=& JFactory::getDBO();
	$query = 'SELECT c.category_id as id, c.category_name as name '
					.'FROM #__vm_category AS c '
					;
	$db->setQuery($query);

	$categories = $db->loadObjectList('id');
	return $categories[$id];
}

// возвращает производителя по id как объект
function &vm_getManufacturer($id)
{
	static $manufacturers;
	if( isset( $manufacturers[$id] ) ) return $manufacturers[$id];

	$db			=& JFactory::getDBO();
	$query = 'SELECT manufacturer_id as id, mf_name as name '
					.'FROM #__vm_manufacturer '
					;
	$db->setQuery($query);

	$manufacturers = $db->loadObjectList('id');
	return $manufacturers[$id];
}


// возвращает продукт по id как строку
function vm_getProduct($id)
{
	static $products;
	if( isset( $products[$id] ) ) return $products[$id];

	$db			= & JFactory::getDBO();
	$query = 'SELECT product_name as name FROM #__vm_product '
					.'WHERE product_id = ' . (int) $id . ' '
					;
	$db->setQuery($query);

	$products[$id] = $db->loadResult();
	return $products[$id];
}


// возвращает файл по id как строку
function vm_getFile($id)
{
	static $files;
	if( isset( $files[$id] ) ) return $files[$id];

	$db			= & JFactory::getDBO();
	$query = 'SELECT file_title as name FROM #__vm_product_files '
					.'WHERE file_id = ' . (int) $id . ' '
					;
	$db->setQuery($query);

	$files[$id] = $db->loadResult();
	return $files[$id];
}


// возвращает алиас если переданный тип переменной можно заалиасить
function vm_getAlias($type, $id)
{
	switch ($type)
	{
		case 'category_id':
			$name = &vm_getCategory($id)->name;
		break;
		case 'product_id':
			$name = &vm_getProduct($id);
		break;
		case 'manufacturer_id':
			$name = &vm_getManufacturer($id)->name;
		break;
		case 'file_id':
			$name = &vm_getFile($id);
		break;
		default:
		return null;
	}

	$alias = vm_transliterate($name);

	$alias = JFilterOutput::stringURLSafe($alias);

	return $alias;
}

// функция которая транслитит имя
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

	return $transliteration;
}