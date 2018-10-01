<?php
/**
 * Templates table
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: csvi_available_fields.php 666 2009-01-02 07:55:31Z Suami $
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * @package CSVImproved
 */
class TableCsvi_available_fields extends JTable {
	
	/** @var integer */
	var $id = 0;
	/** @var string */
	var $csvi_name = null;
	/** @var string */
	var $vm_name = null;
	/** @var string */
	var $vm_table = null;
	
	/**
	* @param database A database connector object
	*/
	function __construct($db) {
		parent::__construct('#__csvi_available_fields', 'id', $db );
	}
}
?>