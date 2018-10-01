<?php
// Set flag that this is a parent file
define( '_JEXEC', 1 );
define('JPATH_BASE', '../../../' );
define( 'DS', DIRECTORY_SEPARATOR );
//require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
//require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( '../../../includes/defines.php' );
require_once ( '../../../includes/framework.php' );

$mainframe =& JFactory::getApplication('site');

require('helper.php');