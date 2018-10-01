<?php
/**
* @author Beliyadm @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


class modVirtUniversalHelper
{
	function getList(&$params)
	{
		global $mainframe;

		echo modUniversal_style();

		$db				=& JFactory::getDBO();
		$user			=& JFactory::getUser();
		$userId			= (int) $user->get('id');
		$max_items 		= $params->get( 'max_items', 6 );
		$selecttype 	= $params->get( 'selecttype', 1 );
		$ceil_price 	= $params->get( 'ceil_price', 0 );
		$discount 		= $params->get( 'discount', 1 );
		$jstooltip 		= $params->get( 'jstooltip', 1 );
		$category_id 	= $params->get( 'category_id', null );
		$checkItemid	= $params->get( 'checkItemid', 1 );
		$ItemidCustom 	= $params->get( 'checkItemidCustom' );


		if (($checkItemid == '1') && ($ItemidCustom == '')) {
			$query = 'SELECT id FROM #__menu WHERE link LIKE "index.php?option=com_virtuemart" LIMIT 1';
			$db->setQuery($query);
			$row = $db->loadObject();
			$Itemid = $row->id;
		} else if ($ItemidCustom != '')  {
			$Itemid = $ItemidCustom;
		} else {
			$Itemid = JRequest::getInt( 'Itemid', 1, 'get' );
		}
		/*
			load JS for tooltip in head or not
			0 - обычные подсказки браузера title
			1 - JS подсказки на mootools
			2 - JS подсказки на jquery
		*/
		$header = '';
		switch ($jstooltip) {
			case '1';
				/*
				Если хотите добиться валидности - убедитесь что библиотека загружена до данного плагина и раскомментируйте строки
				$header .= '<script type="text/javascript" src="'.JURI::base().'modules/mod_virtuemart_universal/files/mootools_tooltip.js"></script>';
            	$mainframe->addCustomHeadTag($header);
            	*/
            	echo '<script type="text/javascript" src="'.JURI::base().'modules/mod_virtuemart_universal/files/mootools_tooltip.js"></script>';
			break;
			case '2';
            	/*
            	Если хотите добиться валидности - убедитесь что библиотека загружена до данного плагина и раскомментируйте строки
            	$header .= '<script type="text/javascript" src="'.JURI::base().'modules/mod_virtuemart_universal/files/jquery_tooltip.js"></script>';
            	$mainframe->addCustomHeadTag($header);
            	*/
            	echo '<script type="text/javascript" src="'.JURI::base().'modules/mod_virtuemart_universal/files/jquery_tooltip.js"></script>';
			break;
			case '3';

			break;
			default:
			break;
        }
        //Округляем цену при необходимости
        if ($ceil_price == '1') {
        	$ceil_price = 'floor(pp.product_price) AS pprice';
        } else {
        	$ceil_price = 'pp.product_price AS pprice';
        }

        //получаем список ID категорий и подставляем в запрос
        $where = ''; $ordering = '';
        if ($category_id)
		{
			$ids = explode( ',', $category_id );
			JArrayHelper::toInteger( $ids );
			$where .= ' AND (cx.category_id=' . implode( ' OR cx.category_id=', $ids ) . ')';
		}

        /*
        	$selecttype - тип сортировки
	        1 - последние товары
	        2 - наиболее продаваемые
	        3 - отмеченные как special
	        4 - случайные товары
        */
        switch ($selecttype) {
        case '1';
        	$ordering 	.= ' p.product_id DESC ';
        break;
        case '2';
        	$ordering 	.= ' p.product_sales DESC ';
        break;
        case '3';
        	$where		.= ' AND p.product_special = "Y" ';
        	$ordering	.= ' p.product_id DESC ';
        break;
        case '4';
            $ordering 	.= ' RAND() ';
        break;
        default:
        break;
        }

		$query = 'SELECT p.product_id AS pid, p.product_sku AS psku, p.product_thumb_image AS pimage, p.product_name AS pname, ' .
			' cx.category_id AS catid, '.$ceil_price.', p.product_s_desc AS pintro, pp.product_currency AS currency, p.product_discount_id AS discount'.
			' FROM #__vm_product p ' .
			' RIGHT JOIN #__vm_product_category_xref AS cx ON p.product_id = cx.product_id'.
			' RIGHT JOIN #__vm_product_price as pp ON pp.product_id = p.product_id ' .
			' WHERE p.product_publish= "Y" '.$where.' GROUP BY pid  ORDER BY '.$ordering.' LIMIT '.$max_items.'';
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$i = 0; $lists = array();

		foreach ( $rows as $row )
		{
			$lists[$i]->link 		= JRoute::_('index.php?page=shop.product_details&category_id='.$row->catid.'&flypage=flypage.tpl&product_id='.$row->pid.'&option=com_virtuemart&Itemid='.$Itemid,false);
			$lists[$i]->pname 		= $row->pname;
			$lists[$i]->pid 		= $row->pid;
			if ($row->pimage) {
				$lists[$i]->pimage 		= JURI::base().'components/com_virtuemart/shop_image/product/'.$row->pimage;
			} else {
				$lists[$i]->pimage 		= JURI::base().'components/com_virtuemart/themes/default/images/noimage.gif';
			}

			$lists[$i]->intro 		= strip_tags($row->pintro);
			$lists[$i]->price 		= $row->pprice;
			$lists[$i]->currency 	= $row->currency;
			if (($row->discount != '0') && ($discount == '1')) {
				$lists[$i]->discount	= '1';
			} else {
				$lists[$i]->discount	= '0';
			}
			$i++;
		}

		return $lists;

	}
}

function modUniversal_style()
	{
		global $mainframe;
		$header 	= '';
		$header 	.= '<link rel="stylesheet" href="'.JURI::base().'modules/mod_virtuemart_universal/files/mod_virtuemart_universal.css" type="text/css" />';
		$mainframe->addCustomHeadTag($header);

	}
