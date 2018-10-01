<?php
/**
 * Virtuemart Shopper Group table
 *
 * @package CSVImproved
 * @subpackage Tables
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: vm_user_info.php 665 2009-01-02 07:40:08Z Suami $
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * @package CSVImproved
 * @subpackage Tables
 */
class TableVm_shopper_group extends JTable {
	
	/**
	* @param database A database connector object
	*/
	function __construct($db) {
		$this->reset();
		parent::__construct('#__vm_shopper_group', 'shopper_group_id', $db );
	}
	
	/**
	 * Set a value for the class
	 */
	public function setValue($field, $value) {
		$this->$field = $value;
	}
	
	/**
	 * Get a value from the class
	 */
	public function getValue($field) {
		return $this->$field;
	}
	
	/**
	 * Cleans the class variables
	 */
	public function reset() {
		$this->setProperties(CsvimprovedModelAvailablefields::DbFields('vm_shopper_group'));
	}
}
?>