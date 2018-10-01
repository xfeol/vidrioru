<?php
/**
 * Un-installation file for CSV Improved
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: uninstall.csvimproved.php 948 2009-08-05 08:19:28Z Roland $
 */
 
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Uninstaller
 */
function com_uninstall() {
	global $mainframe;
	$mainframe->enqueueMessage('CSVI VirtueMart 1.9.2 has been uninstalled');
}
?>