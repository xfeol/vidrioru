<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>

<div class="vmCartContainer">
<?php
mm_showMyFileName(__FILE__);
// This function lists all product children ( = Items)
// or, when not children are defined, the product_id
// SO LEAVE THIS IN HERE!

if ($has_attributes)
{
    list($html,$children) = $ps_product_attribute->list_attribute( ( $product_parent_id > 0 )  ? $product_parent_id : $product_id, null, "YM" );    
}
//echo "product_parent_id: " . $product_parent_id . "; product_id:". $product_id . "; children:" . $children . "; has_attributes: " . $has_attributes . "<br />";

if ($children != "multi") { 

    if( CHECK_STOCK == '1' && !$product_in_stock ) {
     	$notify = true;
    } else {
    	$notify = false;
    }

?>
    <form action="<?php echo $mm_action_url ?>index.php" method="post" name="addtocart" id="<?php echo uniqid('addtocart_') ?>" class="addtocart_form" <?php if( $this->get_cfg( 'useAjaxCartActions', 1 ) && !$notify ) { echo 'onsubmit="handleAddToCart( this.id );return false;"'; } ?>>

<?php
}
echo $html;
if (USE_AS_CATALOGUE != '1' && $product_price != "" && !stristr( $product_price, $VM_LANG->_('PHPSHOP_PRODUCT_CALL') )) { ?>
        <?php if ($children != "multi") { ?>
    <div class="vmCartChildElementM">
    <?php 
    $button_lbl = $VM_LANG->_('PHPSHOP_CART_ADD_TO');
    $button_cls = 'addtocart_button_small';
    if( CHECK_STOCK == '1' && !$product_in_stock ) {
     	$button_lbl = $VM_LANG->_('VM_CART_NOTIFY');
     	$button_cls = 'notify_button';
    }
    ?>
	<div class="product-Add-button" style="float: right; text-align: right; margin-top: 0px;">
	    <input type="submit" class="addtocart_button_small" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
	</div>
    <div class="product-Actual-Price">
    <?php echo $product_price; ?>
    </div>
    <div style="clear:both;"></div>

    <?php  } ?>  
    <input type="hidden" name="quantity[]" value="1" />
    <input type="hidden" name="flypage" value="shop.flypage.tpl" />
    <input type="hidden" name="category_id" value="<?php echo @$_REQUEST['category_id'] ?>" />
    <input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
    <input type="hidden" name="prod_id[]" value="<?php echo $product_id ?>" />
    <input type="hidden" name="page" value="shop.cart" />
    <input type="hidden" name="func" value="cartAdd" />
    <input type="hidden" name="Itemid" value="<?php echo $sess->getShopItemid() ?>" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="set_price[]" value="" />
    <input type="hidden" name="adjust_price[]" value="" />
    <input type="hidden" name="master_product[]" value="" />
    </div>    
    <?php
}
if ($children != "multi") { ?>
	</form>
<?php 
} 
    if($children == "radio") { ?>
    
    <script language="JavaScript" type="text/javascript">//<![CDATA[
    function alterQuantity(myForm) {
        for (i=0;i<myForm.selItem.length;i++){
            setQuantity = myForm.elements['quantity'];
            selected = myForm.elements['selItem'];
            j = selected[i].id.substr(7);
            k= document.getElementById('quantity' + j);
            if (selected[i].checked==true){
                k.value = myForm.quantity_adjust.value; }
            else {
                k.value  = 0;
            }
        }
    }
	//]]>   
	</script>
<?php } ?>
</div>