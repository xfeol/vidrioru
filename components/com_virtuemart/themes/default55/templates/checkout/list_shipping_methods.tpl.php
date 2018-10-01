<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
foreach( $PSHOP_SHIPPING_MODULES as $shipping_module ) {
    $vmLogger->debug( 'Starting Shipping module: '.$shipping_module );
	if( file_exists( CLASSPATH. "shipping/".$shipping_module.".php" )) {
		include_once( CLASSPATH. "shipping/".$shipping_module.".php" );
	}
	if( class_exists( $shipping_module )) {
		$SHIPPING = new $shipping_module();
		$SHIPPING->list_rates( $vars );
		echo "<br /><br />";
	}
}

?>