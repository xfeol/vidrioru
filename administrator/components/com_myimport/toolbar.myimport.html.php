<?php
  ////////////////////////////////////////////////////////
  // Компонент импорта/экспорта товаров для Virtuemart	//
  // Разработан для Joomla 1.5.x 						//
  // 2010 (C) Ребров О.В.   (admin@webplaneta.com.ua)	//
  ////////////////////////////////////////////////////////
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_myimport {
	function _EXPORT() {
		JToolBarHelper::title( JText::_( 'Экспорт' ), 'generic.png' );
		JToolBarHelper::back();
	}
	function _IMPORT() {
		JToolBarHelper::title( JText::_( 'Импорт' ), 'generic.png' );
		JToolBarHelper::back();
		JToolBarHelper::preferences('com_myimport', '300');
	}
	function _ABOUT() {
		JToolBarHelper::title( JText::_( 'О компоненте' ), 'generic.png' );
		JToolBarHelper::back();
	}
	function _VERSIONS() {
		JToolBarHelper::title( JText::_( 'История версий' ), 'generic.png' );
		JToolBarHelper::back();
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'Импорт/Експорт товаров' ), 'generic.png' );
		JToolBarHelper::preferences('com_myimport', '300');
		
}
	}

?> 
