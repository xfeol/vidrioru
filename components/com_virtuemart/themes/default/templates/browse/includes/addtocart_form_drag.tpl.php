<?php
	/**
	* TemplatePlazza.com 
	**/
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
	mm_showMyFileName(__FILE__);

	//$button_lbl = $VM_LANG->_('PHPSHOP_CART_ADD_TO');
	$button_lbl = $VM_LANG->_('PHPSHOP_CART_ADD_TO');
	$button_cls = 'addtocart_button';
	if( CHECK_STOCK == '1' && !$product_in_stock ){
		$button_lbl = $VM_LANG->_('VM_CART_NOTIFY');
		$button_cls = 'button';
		$notify = true;
	}else{
		$notify = false;
	}
	
	$pid		= "";
	$x			= 0;
	$prodids	= str_replace("index.php?", "", $product_flypage);
	$prodids	= explode("&amp;", $prodids);
	foreach($prodids as $prodid){
		list($var,$val)	= split("=", $prodid);
		if($x == 2){ //get id
			$pid= $val;
		}
		$x++;
	}
	$product_id = $pid;
?>
	<div class="producthandle">
	<form action="<?php echo $mm_action_url ?>index.php" method="post" name="addtocart" id="addtocart<?php echo $i . "_" . $product_id; ?>" class="addtocart_form" <?php if( $this->get_cfg( 'useAjaxCartActions', 1 ) && !$notify ) { echo 'onsubmit="handleAddToCart( this.id );return false;"'; } ?>>
		<input type="hidden" value="1" name="quantity[]" />
		<input type="hidden" name="category_id" value="<?php echo  @$_REQUEST['category_id'] ?>" />
		<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
		<input type="hidden" name="prod_id[]" value="<?php echo $product_id ?>" />
		<input type="hidden" name="page" value="shop.cart" />
		<input type="hidden" name="func" value="cartadd" />
		<input type="hidden" name="Itemid" value="<?php echo $sess->getShopItemid() ?>" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="set_price[]" value="" />
		<input type="hidden" name="adjust_price[]" value="" />
		<input type="hidden" name="master_product[]" value="" />
		<div class="imgdragcart" align="center">
			<?php echo ps_product::image_tag( $product_thumb_image, 'border="0" title="'.$product_name.'" alt="'.$product_name .'"' ); ?>
		</div>
	</form>
	</div>