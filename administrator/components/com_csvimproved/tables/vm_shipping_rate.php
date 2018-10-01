<?php
/**
 * Virtuemart Shipping rates table
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: vm_product_files.php 869 2009-04-14 14:00:35Z Suami $
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * @package CSVImproved
 */
class TableVm_shipping_rate extends JTable {
	
	/**
	* @param database A database connector object
	*/
	function __construct($db) {
		$this->reset();
		parent::__construct('#__vm_shipping_rate', 'shipping_rate_id', $db );
	}
	
	/**
	 * Cleans the class variables
	 */
	public function reset() {
		$this->setProperties(CsvimprovedModelAvailablefields::DbFields('vm_shipping_rate'));
	}
}
?>
