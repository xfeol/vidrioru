<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>

<!-- The product name DIV. -->
 <?php if( $show_product_name ) : ?>
<div style="height:20px; text-align:center; width: 100%;line-height:14px;">
<a title="<?php echo $product_name ?>" href="<?php echo $product_link ?>"><?php echo $product_name; ?></a>
<br />
</div>
<?php endif;?>

<!-- The product image DIV. -->
<div style="height:190px;width: 100%;text-align:center;">
<a title="<?php echo $product_name ?>" href="<?php echo $product_link ?>">
	<?php
		// Print the product image or the "no image available" image
		echo ps_product::image_tag( $product_thumb_image, "alt=\"".$product_name."\"");
	?>
</a>
</div>

<!-- The product price DIV. -->
<div style="width: 100%;float:left;text-align:center;">
<?php
if( !empty($price) ) {
	echo $price;
}
?>
</div>


