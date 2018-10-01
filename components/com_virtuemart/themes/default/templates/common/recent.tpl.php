<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); ?>
<?php if( empty($recent_products)) return; 
?>
<!-- List of recent products -->
<h3><?php echo $VM_LANG->_('VM_RECENT_PRODUCTS') ?></h3>
<ul class="vmRecentDetail">
<?php 
foreach( $recent_products as $recent ) { // Loop through all recent products
	foreach( $recent as $attr => $val ) {
    	//echo $attr." - ".$val."<br />";
        $this->set( $attr, $val );
    }
	/**
	 * Available indexes:
	 * 
	 * $recent["product_name"] => The user ID of the comment author
	 * $recent["category_name"] => The username of the comment author
	 * $recent["product_thumb_image"] => The name of the comment author
	 * $recent["product_url"] => The UNIX timestamp of the comment ("when" it was posted)
	 * $recent["category_url"] => The rating; an integer from 1 - 5
	 * $recent["product_s_desc"] => The comment text
	 * 
	 */
	 
	 $product_thumb_image = $recent["product_thumb_image"];
	 $product_name = $recent["product_name"];
	 
	?>
	
	<div class="product">
	    <a href="<?php echo $recent['product_url']; ?>" class="gk_vm_product_image" title="<?php echo $recent['product_name'] ?>">
    		<span class="product_name"><?php echo $recent['product_name'] ?></span>
		<div style="text-align:center;">
        	    <?php echo ps_product::image_tag( $product_thumb_image, 'class="browseProductImage" title="'.$product_name.'" alt="'.$product_name .'"' ) ?>
    		</div>
	    </a>
	</div>
	
	<?php
}
?>
</ul>