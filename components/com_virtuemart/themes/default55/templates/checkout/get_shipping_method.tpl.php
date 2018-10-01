<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
ps_checkout::show_checkout_bar();

echo $basket_html;

echo '<br />';
$varname = 'PHPSHOP_CHECKOUT_MSG_' . CHECK_OUT_GET_SHIPPING_METHOD;
echo '<h4>2. '. $VM_LANG->_($varname) . '</h4>';

ps_checkout::list_shipping_methods($ship_to_info_id, $shipping_rate_id );

?>
