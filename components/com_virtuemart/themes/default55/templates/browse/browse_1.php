<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); mm_showMyFileName(__FILE__); ?>


<div class="mod_vm_universal1">
	<span class="mod_vm_link1"><b><?php echo $product_name ?></b></span> 

	<a href="<?php echo $product_flypage ?>" class="gk_vm_product_image1" title="<?php echo $product_name ?>">
		<?php echo ps_product::image_tag( $product_thumb_image, 'class="browseProductImage" border="0" title="'.$product_name.'" alt="'.$product_name .'"' ) ?>
	</a>
       		
	<a href="<?php echo $product_flypage ?>" title="<?php echo $product_details ?>" class="mod_vm_readmore1"><?php echo $product_details ?></a>
        
	<span><?php echo $product_price ?></span>       
</div>

<div class="clear1"></div>