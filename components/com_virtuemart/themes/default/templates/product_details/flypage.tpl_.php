<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
mm_showMyFileName(__FILE__);
 ?>


<table style="width:100%;">
	<tr>
		<td align="center" rowspan="2" width="200px">
			<br />
			<?php echo $product_image ?>
		</td>
		<td>
			<h1><?php echo $product_name ?> <?php echo $edit_link ?></h1>
			<?php echo "Артикул: " . $product_sku . "<br />" ?>
			<?php echo $product_availability ?>
			<?php echo $product_price_lbl ?><?php echo $product_price ?>
      			<?php echo $product_packaging ?>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top;">
		<br />
      		<?php echo $addtocart ?>

      		<br />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $this->vmlistAdditionalImages( $product_id, $images ) ?><br />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php
			$file = JPATH_ROOT . '/media/vmart/'.$category_id.'_header.php';
			if (file_exists($file)) include ($file);
			echo $product_description 
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<span style="font-style: italic;"><?php echo $file_list ?></span>
			<?php 
	  		if( $this->get_cfg( 'showAvailability' )) {
	  			echo $product_availability; 
	  		}?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $product_type ?>
			<?php echo $product_reviews ?>
			<?php echo $product_reviewform ?>
			<?php echo $related_products ?>
		</td>
	</tr>
</table>
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