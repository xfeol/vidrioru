<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

mm_showMyFileName(__FILE__);

 ?>
<br />
<table border="0" width="100%" >
       <!-- product333 -->
	<tr>
		<td rowspan="3" align="center" width="200">
			<a href="<?php echo $product_flypage ?>" class="gk_vm_product_image" title="<?php echo $product_name ?>">
        		<?php echo ps_product::image_tag( $product_thumb_image, 'class="browseProductImage" border="0" title="'.$product_name.'" alt="'.$product_name .'"' ) ?>
       		</a>
		</td>
		<td colspan="2">
			 <h2 style="margin:0;font-size:16px;font-weight:bold;"><a href="<?php echo $product_flypage ?>"><?php echo $product_name ?></a></h2>
			 <?php echo $product_rating ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			 <?php echo $product_s_desc ?>
			 &nbsp;&nbsp;
			 <a href="<?php echo $product_flypage ?>">[<?php echo $product_details ?>...]</a>
		</td>

	</tr>
	<tr>
		<td align="right">
			<?php echo $form_addtocart ?>
		</td>
	</tr>
</table>