<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>

<?php
// User is not allowed to see a price or there is no price
if( !$auth['show_prices'] || !isset($price_info["product_price_id"] )) {
	
	$link = $sess->url( $_SERVER['PHP_SELF'].'?page=shop.ask&amp;product_id='.$product_id.'&amp;subject='. urlencode( $VM_LANG->_('PHPSHOP_PRODUCT_CALL').": $product_name") );
	echo vmCommonHTML::hyperLink( $link, $VM_LANG->_('PHPSHOP_PRODUCT_CALL') );
}
?>

<?php
// DISCOUNT: Show old price!
if(!empty($discount_info["amount"])) {
	?>
	<span class="product-Old-Price">
		<?php echo $CURRENCY_DISPLAY->getFullValue($undiscounted_price); ?>
	</span>
	&nbsp;&nbsp;
	<?php
}
?>
<?php
if( !empty( $price_info["product_price_id"] )) { ?>
		<!--strong-->
		<?php echo $CURRENCY_DISPLAY->getFullValue($base_price) ?>
		<?php echo $text_including_tax ?>
		<!--/strong-->
<?php
}
echo $price_table;
?>

<?php
// DISCOUNT: Show the amount the customer saves
//if(!empty($discount_info["amount"])) {
//	echo "<br />";
//	echo $VM_LANG->_('PHPSHOP_PRODUCT_DISCOUNT_SAVE').": ";
//	if($discount_info["is_percent"]==1) {
//		echo $discount_info["amount"]."%";
//	}
//	else {
//		echo $CURRENCY_DISPLAY->getFullValue($discount_info["amount"]);
//	}
//}
?>