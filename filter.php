<?php

define( '_JEXEC', 1);

define( 'JPATH_BASE', dirname(__FILE__) );

define( 'DS', DIRECTORY_SEPARATOR );

require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once( JPATH_BASE .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart_parser.php' );
require_once( CLASSPATH.'ps_product.php' );
require_once( CLASSPATH.'ps_filter_categories.php' );

$psF = new vm_ps_filter_Categories();

$entities = $psF->get_filter_entities_all();

$subs = $psF->get_childs_for_category(3);
print_r($subs);

$psF->get_variants_for_category(3);
