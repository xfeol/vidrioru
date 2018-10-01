<?php
/**
 * About page
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: about.php 947 2009-08-05 08:00:22Z Roland $
 */
 
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<table class="adminlist">
<tr><td colspan="2"><?php echo JHTML::_('image', JURI::base().'components/com_csvimproved/assets/images/csvivirtuemart_about_32.png', JText::_('ABOUT')); ?></td></tr>
<tbody>
<tr><th>Name:</th><td>CSVI</td></tr>
<tr><th>Version:</th><td>1.9.2</td></tr>
<tr><th>Coded by:</th><td>RolandD Cyber Produksi</td></tr>
<tr><th>Contact:</th><td>contact@csvimproved.com</td></tr>
<tr><th>Support:</th><td><?php echo JHTML::_('link', 'http://www.csvimproved.com/', 'CSVI Homepage', 'target="_blank"'); ?></td></tr>
<tr><th>Copyright:</th><td>Copyright (C) 2008 - 2012 RolandD Cyber Produksi</td></tr>
<tr><th>License:</th><td>GNU/GPL v3</td></tr>
</tbody>
</table>