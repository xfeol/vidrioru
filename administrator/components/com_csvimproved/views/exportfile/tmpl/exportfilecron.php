<?php
/**
 * Export a file via cron
 *
 * @package CSVImproved
 * @subpackage Cron
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: exportfilecron.php 829 2009-03-10 21:11:57Z Suami $
 */
 
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$csvilog = JRequest::getVar('csvilog');

/* Display any messages there are */
if (!empty($csvilog->logmessage)) echo $csvilog->logmessage;
else {
	/* strings to find */
	$find = array();
	$find[] = '<br />';
	$find[] = '<strong>';
	$find[] = '</strong>';
	$find[] = '<hr />';
	
	/* strings to replace with */
	$replace = array();
	$replace[] = "";
	$replace[] = "";
	$replace[] = "";
	$replace[] = "\n";
	
	/* Strings to replace linebreaks */
	$findbr = array();
	$findbr[] = "\r\n";
	$findbr[] = "\r";
	$findbr[] = "\n";
	
	/* String to replace linebreaks with */
	$replacebr = " ";
	
	echo JText::_('Results for').' '.JRequest::getVar('filename')."\n";
	echo str_repeat("=", (strlen(JText::_('Results for'))+strlen(JRequest::getVar('filename'))+1))."\n";
	$logresult = $csvilog->GetStats();
	$logcount = array();
	for ($i=1, $n=count( $logresult ); $i <= $n; $i++) {
		if (isset($logresult[$i])) {
			$row = $logresult[$i];
			echo JText::_('Line'); ?>:<?php echo $i."\n";
			echo JText::_('Result'); ?>:<?php echo $row['result']."\n";
			echo JText::_('Message'); ?>
			<?php
			if (count($row['status']) > 0) {
				foreach ($row['status'] as $result => $details) {
					echo JText::_($result).' :: '.str_ireplace($findbr, $replacebr, str_ireplace($find, $replace, $details['message']))."\n";
				}
			}
		}
	}
/* Get the logcount for statistics */
$logcount = JRequest::getVar('logcount');
?>
<?php echo "\n\n".JText::_('Totals')."\n"; ?>
<?php foreach ($logcount as $result => $count) {
	echo JText::_($result).': '.$count."\n\n";
} 
   /* Show debug log */
   if ($csvilog->debug_message != '') {
	   echo JText::_('Debug message'); ?>
	   <?php echo str_ireplace($find, $replace, $csvilog->debug_message); ?>
   <?php }
}
?>