<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
mm_showMyFileName(__FILE__);
 ?>


<table border="0" width="100%" cellspacing="5" cellpadding="3">
	<tr>
		<td align="center" width="36%" rowspan="2">
			<?php echo $product_image ?>				
		</td>
		<td>
			<h1><?php echo $product_name ?> <?php echo $edit_link ?></h1>
			<?php echo $product_price_lbl ?><b><?php echo $product_price ?></b>
      		<br />
      		<?php echo $product_packaging ?>
      		<br />
      		<?php echo $ask_seller ?>
      			
		</td>
	</tr>
	<tr>
		<td>
      		<?php echo $addtocart ?>
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