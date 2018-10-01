<?php 
// Set flag that this is a parent file
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('administrator');

require_once('helper.php');