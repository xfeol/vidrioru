<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
mm_showMyFileName(__FILE__);
 ?>

<div class="flypage_hdr">
    <h1><?php echo $product_name ?> <?php echo $edit_link ?></h1>
</div>

<div class="flypage_img">
    <?php echo $product_image ?>
</div>

<div class="flypage_info">
    <?php echo "Артикул: " . $product_sku . "<br />" ?>
    <?php echo $product_availability ?>
    <?php echo $product_packaging ?>
	<?php
		$file = JPATH_ROOT . '/media/vmart/additional_categories.php';
		if (file_exists($file)) include ($file);
	?>
    <div class="flypage_cart"><?php echo $addtocart ?></div>
</div>

<div class="clr"></div>
<div class="flypage_imgs">
    <?php echo $this->vmlistAdditionalImages( $product_id, $images ) ?>
</div>

<div class="flypage_desc">
    <?php
    $file = JPATH_ROOT . '/media/vmart/'.$category_id.'_header.php';
    if (file_exists($file)) include ($file);
    echo $product_description
    ?>
</div>

<div class="flypage_type">
    <?php echo $product_type ?>
    <?php echo $related_products ?>
</div>

<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="button" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir"></div>


<?php 
if( !empty( $recent_products )) { ?>
	<br />
	<hr />
	<div class="vmRecent">
	<?php echo $recent_products; ?>
	</div>
<?php 
}
if( !empty( $navigation_childlist )) { ?>
	<?php echo $VM_LANG->_('PHPSHOP_MORE_CATEGORIES') ?><br />
	<?php echo $navigation_childlist ?><br style="clear:both"/>
<?php
} ?>