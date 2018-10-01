<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?>
<table border="1" width="100%" style="border-collapse: collapse">
	<tr class="sectiontableheader">
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_CART_NAME') ?></b></td>
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_CART_SKU') ?></b></td>
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_CART_PRICE') ?></b></td>
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_CART_QUANTITY') ?>&nbsp; /&nbsp; <?php echo $VM_LANG->_('PHPSHOP_CART_ACTION') ?></b></td>
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_DELETE') ?></b></td>
		<td align="center" bgcolor="#000000"><b><?php echo $VM_LANG->_('PHPSHOP_CART_SUBTOTAL') ?></b></td>
	</tr>
	<?php foreach( $product_rows as $product ) { ?>
	<tr class="<?php echo $product['row_color'] ?>">
		<td align="left"><?php echo $product['product_name'] . $product['product_attributes'] ?></td>
		<td align="center"><?php echo $product['product_sku'] ?></td>
		<td align="center"><?php echo $product['product_price'] ?></td>
		<td align="center"><?php echo $product['update_form'] ?></td>
		<td align="center"><?php echo $product['delete_form'] ?></td>
		<td align="right"><?php echo $product['subtotal'] ?> </td>
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
    <td colspan="4" align="right"> </td>
    <td align="right" colspan="2"><strong><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_TOTAL') ?>:&nbsp; &nbsp;<?php echo $order_total_display ?></strong></td>
  </tr>
<?php if ( $show_tax ) { ?>
  <?php } ?>
  </table>
