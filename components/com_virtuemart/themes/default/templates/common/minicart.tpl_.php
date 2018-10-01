<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>

<table class="minicart">

	<tr style="align:middle;">
	<td class="pic_basket">
        <?php if($empty_cart) { ?>
        <img style="display: block;border:0;" src="<?php echo $mm_action_url ?>/components/com_virtuemart/shop_image/ps_image/basket-empty.png" title="Перейти в корзину" alt="Корзина" />
	<?php } else { ?>
        <a href="index.php?page=shop.cart&amp;option=com_virtuemart" target="_self">
        <img style="display: block;border:0;" src="<?php echo $mm_action_url ?>/components/com_virtuemart/shop_image/ps_image/basket-full.png" title="Перейти в корзину" alt="Корзина" /></a>
	<?php } ?>
	</td>
	<td class="text_basket">
	<?php if($empty_cart) { ?>
		<div style="margin: 0 auto;">
		<?php if(!$vmMinicart) { ?>
		<?php }
		echo $VM_LANG->_('PHPSHOP_EMPTY_CART')?>
		</div>
		<?php }

		$qnt = 0;
		// Loop through each row and build the table
		foreach( $minicart as $cart ) { 		

			foreach( $cart as $attr => $val ) {
				// Using this we make all the variables available in the template
				// translated example: $this->set( 'product_name', $product_name );
				$this->set( $attr, $val );
			}
			if(!$vmMinicart) { // Build Minicart
				?>
				<?php $qnt = $qnt + $cart['quantity']; 
			}
		}

	if(!$vmMinicart) { ?>
	<?php } ?>
	<?php if ($total_products != '') echo $VM_LANG->_('PHPSHOP_PRODUCT_COUNT_NAME').': '.$qnt.' '.$VM_LANG->_('PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT');?>
	<br />
	<?php  if ($total_price != '') echo $VM_LANG->_('PHPSHOP_PRODUCTS_PAYS').': '.$total_price; ?>
	<br />
	<?php if ($total_products != '') { ?>
		<a style="color:red;text-decoration:underline;" href="index.php?page=shop.cart&amp;option=com_virtuemart" target="_self">
		<strong>
		<?php 
		echo $VM_LANG->_('PHPSHOP_CHECKOUT_TITLE')?>
		</strong>
		</a>
	<?php }	?>
	</td>
	</tr>
</table>
