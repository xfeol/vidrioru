<?php
  ////////////////////////////////////////////////////////
  // Компонент импорта/экспорта товаров для Virtuemart	//
  // Разработан для Joomla 1.5.x 						//
  // 2010 (C) Ребров О.В.   (admin@webplaneta.com.ua)	//
  ////////////////////////////////////////////////////////
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch($task)
{
	case 'import':
		TOOLBAR_myimport::_IMPORT();
		break;
	case 'export':
		TOOLBAR_myimport::_EXPORT();
		break;
	case 'about':
		TOOLBAR_myimport::_ABOUT();
		break;
		case 'versions':
		TOOLBAR_myimport::_VERSIONS();
		break;
 
	default:
		TOOLBAR_myimport::_DEFAULT();
		break;
}

?>