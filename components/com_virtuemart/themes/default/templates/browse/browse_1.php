<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

mm_showMyFileName(__FILE__);

 ?>
<div class="product">
    <a href="<?php echo $product_flypage ?>" class="gk_vm_product_image" title="<?php echo $product_name ?>">
        <span class="product_name"><?php echo $product_name ?></span>
	<div style="text-align:center;position:relative;">
            <?php
                if (isset($features)) {
		    echo "<div data-tooltip=\"$features\" class=\"colors\"></div>";
		}
		    echo ps_product::image_tag( $product_thumb_image, 'class="browseProductImage" title="'.$product_name.'" alt="'.$product_name .'"' )
	    ?>
        </div>
        
    </a>
    <span class="mod_vm_price"><?php echo $product_price ?></span>
</div>
