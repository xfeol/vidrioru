<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$db= & JFactory::getDBO();

$q="SHOW COLUMNS FROM `#__vm_product_type_parameter` WHERE `Field`='mode'";
$db->setQuery($q);
$exists=$db->loadResult();

if( !$exists ) {
	$q="ALTER TABLE `#__vm_product_type_parameter` ADD `mode` TINYINT( 1 ) NULL DEFAULT 0";
	$db->setQuery($q);
	$db->query();
}

?>