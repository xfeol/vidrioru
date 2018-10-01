<?php
/**
 * About view
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: view.html.php 945 2009-07-30 07:18:43Z Roland $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.view' );

/**
 * Templates View
 *
 * @package CSVImproved
 */
class CsvimprovedViewAbout extends JView {
	
	/**
	 * About view display method
	 * @return void
	 **/
	function display($tpl = null) {
		
		/* Show the toolbar */
		$this->toolbar();
		
		/* Display it all */
		parent::display($tpl);
	}
	
	/**
	 * Displays a toolbar for a specific page
	 */
	function toolbar() {
		JToolBarHelper::title(JText::_('About'), 'csvivirtuemart_about_48');
	}
}
?>