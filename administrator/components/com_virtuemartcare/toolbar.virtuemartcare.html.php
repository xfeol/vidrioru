<?php
  ////////////////////////////////////////////////////////
  // Компонент сервиса VirtuemartCare	                //
  // Разработан для Joomla 1.5.x 			//
  // 2012 (C) Beagler   (beagler.ru@gmail.com)          //
  ////////////////////////////////////////////////////////
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_virtuemartcare {
	
	function _PHOTO() {
		JToolBarHelper::title( JText::_( 'Сервис фото товаров' ), 'generic.png' );
		JToolBarHelper::back();
		JToolBarHelper::preferences('com_virtuemartcare', '300');
	}
	function _PRICE() {
		JToolBarHelper::title( JText::_( 'Сервис цен' ), 'generic.png' );
		JToolBarHelper::back();
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'Сервис фото товаров' ), 'generic.png' );
		JToolBarHelper::preferences('com_virtuemartcare', '300');
		
}
	}

?> 
