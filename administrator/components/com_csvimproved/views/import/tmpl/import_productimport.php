<?php
/**
 * Import product options
 *
 * @package CSVImproved
 * @subpackage Import
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: import_productimport.php 665 2009-01-02 07:40:08Z Suami $
 */
 
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 
?>
<table class="adminlist">
	<thead>
		<tr><th colspan="2"><?php echo JText::_('General import options'); ?></th></tr>
	</thead>
	<tbody>
		<?php
		echo $this->loadTemplate('importlocation');
		?>
	</tbody>
</table>
