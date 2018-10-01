<?php
  ////////////////////////////////////////////////////////
  // Компонент сервиса VirtuemartCare	                //
  // Разработан для Joomla 1.5.x 						//
  // 2012 (C) Beagler   (beagler.ru@gmail.com)          //
  ////////////////////////////////////////////////////////
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch($task)
{
	case 'photo':
		TOOLBAR_virtuemartcare::_PHOTO();
		break;
	case 'price':
		TOOLBAR_virtuemartcare::_PRICE();
		break;
 
	default:
		TOOLBAR_virtuemartcare::_DEFAULT();
		break;
}

?>