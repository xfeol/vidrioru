<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

mm_showMyFileName( __FILE__ );

echo '<h2>'. $VM_LANG->_('PHPSHOP_CART_TITLE') .'</h2>';
include(PAGEPATH. 'basket.php');
echo $basket_html;

?>