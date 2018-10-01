<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?>
<table width="100%" border="0" cellspacing="1" class="basket_table">
  <tr align="left" class="sectiontableheader">
        <th><?php echo $VM_LANG->_('PHPSHOP_CART_NAME') ?></th>
        <!--th><?php echo $VM_LANG->_('PHPSHOP_CART_SKU') ?></th-->
	<!--th><?php echo $VM_LANG->_('PHPSHOP_CART_PRICE') ?></th-->
	<th><?php echo $VM_LANG->_('PHPSHOP_CART_ACTION') ?>/<?php echo $VM_LANG->_('PHPSHOP_CART_QUANTITY') ?></th>
	<th><?php echo $VM_LANG->_('PHPSHOP_CART_SUBTOTAL') ?></th>
  </tr>
<?php foreach( $product_rows as $product ) { ?>
  <tr valign="top" class="<?php echo $product['row_color'] ?>">
	<td><?php echo $product['product_name'] . $product['product_attributes'] ?></td>
	<!--td><?php echo $product['product_sku'] ?></td-->
	<!--td align="right"><?php echo $product['product_price'] ?></td-->
	<td><?php echo $product['update_form'] ?>
		<?php echo $product['delete_form'] ?>
	</td>
    <td align="right"><?php echo $product['subtotal'] ?></td>
  </tr>
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if( $discount_before ) { ?>
  <?php } 
if( $shipping ) { ?>
  <?php } 
if($discount_after) { ?>
  <?php } ?>
  <tr class="sectiontableentry1">
    <td colspan="4" align="right"><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_TOTAL') ?>: </td>
    <td align="right"><strong><?php echo $order_total_display ?></strong></td>
  </tr>
<?php if ( $show_tax ) { ?>
  <?php } ?>
  </table>
  
  <?php include(PAGEPATH. 'checkout.without_register_form.php'); ?>