<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
mm_showMyFileName(__FILE__);
 ?>


<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" rowspan="2" width="200px">
			<br />
			<?php echo $product_image ?>
		</td>
		<td>
			<h1><?php echo $product_name ?> <!--?php echo $edit_link ?--></h1>
			<!--?php echo "Артикул: " . $product_sku . "<br />" ?-->
			<!--?php echo $product_availability ?-->
			<!--?php echo $product_price_lbl ?--><!--?php echo $product_price ?-->
			<?php echo $ps_product_attribute->list_attribute_list_template($product_id, "N", "", "", "", "", "", "", "", "product_details/includes/addtocart_list_3.tpl.php"); ?>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top;">
		<br />
      		<!--?php echo $addtocart ?-->
      		<br />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $this->vmlistAdditionalImages( $product_id, $images ) ?><br />
			<hr />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $product_description ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<span style="font-style: italic;"><?php echo $file_list ?></span>
			<?php 
	  		if( $this->get_cfg( 'showAvailability' )) {
	  			echo $product_availability; 
	  		}?>	
	  		<hr />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $product_type ?>
			<br />
			<?php echo $product_reviews ?>
			<br />
			<?php echo $product_reviewform ?><br />
			<?php echo $related_products ?><br />
		</td>
	</tr>
</table>
<br />
<hr />
<?php 
if( !empty( $recent_products )) { ?>
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