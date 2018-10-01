<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 


ps_checkout::show_checkout_bar();

echo $basket_html;

echo '<br />';

$varname = 'PHPSHOP_CHECKOUT_MSG_' . CHECK_OUT_GET_PAYMENT_METHOD;
echo '<h4>3. '. $VM_LANG->_($varname) . '</h4>';

echo ps_checkout::list_payment_methods( $payment_method_id );
?>