<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 


ps_checkout::show_checkout_bar();

echo $basket_html;
   
echo '<br />';

?>
<div style="width: 100%; align:left;">
	<?php
// CHECK_OUT_GET_SHIPPING_ADDR
// let the user choose a shipto address
echo ps_checkout::display_address();
	?>

</div><br />




<div style="width: 100%; align:left; float:left;">
<?php
$varname = 'PHPSHOP_CHECKOUT_MSG_' . CHECK_OUT_GET_SHIPPING_ADDR;
echo '<h4>'. $VM_LANG->_($varname) . '</h4>';
?>
</div>
<!-- Customer Ship To -->

<div style="width: 100%; align: left; float: left;">
	<?php
	$ps_checkout->ship_to_addresses_radio($auth["user_id"], "ship_to_info_id", $ship_to_info_id);
	?>
</div>
<br />
<div style="width: 100%; align: left; float:left;">
	<br /><?php echo $VM_LANG->_('PHPSHOP_ADD_SHIPTO_1') ?>
        <a href="<?php $sess->purl(SECUREURL .basename($_SERVER['PHP_SELF']). "?page=account.shipto&next_page=checkout.index");?>">
        <?php echo $VM_LANG->_('PHPSHOP_ADD_SHIPTO_2') ?></a>.<br />
 </div>

<!-- END Customer Ship To -->
<br /><br /><br /><br /><br /><br />