<?php
/**
 * Virtuemart product table
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: vm_product.php 930 2009-06-07 17:19:20Z Roland $
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * @package CSVImproved
 */
class TableVm_product extends JTable {
	
	/**
	* @param database A database connector object
	*/
	function __construct($db) {
		$this->reset();
		parent::__construct('#__vm_product', 'product_id', $db );
	}
	
	/**
	 * Clean the variables
	 */
	public function reset() {
		$this->setProperties(CsvimprovedModelAvailablefields::DbFields('vm_product'));
	}
}
?>