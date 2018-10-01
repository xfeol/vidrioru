<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>

<?php
  if($empty_cart) { ?>
    <div class="cart">
    <a href="javascript:void(0)" id="basket_empty" class="buttonbar fa fa-shopping-cart fa-2x"></a>
	</div>
    <?php
  } else {
    ?>
    <a href="index.php?page=shop.cart&amp;option=com_virtuemart" target="_self">
    
    <?php 
	$qnt = 0;
	// Loop through each row and build the table
	foreach( $minicart as $cart ) { 		

	    foreach( $cart as $attr => $val ) {
		// Using this we make all the variables available in the template
		// translated example: $this->set( 'product_name', $product_name );
		$this->set( $attr, $val );
	    }
	    if(!$vmMinicart) { // Build Minicart
		$qnt = $qnt + $cart['quantity']; 
	    }
	}
    
	?>
	<div class="cart">
	<i id="basket_full" class="buttonbar fa fa-shopping-cart fa-2x"></i>
	<span class="cart_count_items"><?php if ($total_products != '') echo $qnt; ?></span>
	</div>
	<!--div class="cart_count_items"-->
	   <?php
	    //if ($total_products != '') echo $VM_LANG->_('PHPSHOP_PRODUCT_COUNT_NAME').': '.$qnt.' '.$VM_LANG->_('PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT');
	    //echo "<br />";
	    //if ($total_price != '') echo $VM_LANG->_('PHPSHOP_PRODUCTS_PAYS').': '.$total_price;
	   ?>
	<!--/div-->
    </a>
  <?php }; ?>
