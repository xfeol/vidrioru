<?php
	/**
	* TemplatePlazza.com 
	**/
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
	mm_showMyFileName(__FILE__);
	
	vmCommonHTML::loadSlimBox();

	//echo $buttons_header;
	echo $browsepage_header;
	//echo $parameter_form;
	echo $orderby_form;

	echo '<div id="product_list">';
	
	$data =array(); // Holds the rows of products
	$i = $row = $tmp_row = 0; // Counters
	$num_products = count( $products );
	foreach( $products as $product ){
		foreach( $product as $attr => $val ) {
			// Using this we make all the variables available in the template
			// translated example: $this->set( 'product_name', $product_name );
			$this->set( $attr, $val );
		}
		
		// Parse the product template (usually 'browse_x') for each product
		// and store it in our $data array 
		echo $this->fetch( 'browse/'.$templatefile .'.php' );
	}
	echo '</div>';

	echo $browsepage_footer;
		
	if( trim(str_replace( "<br />", "" , $desc)) != "" ) { ?>

		<div style="width:100%;float:left;">
			<?php echo $desc; ?>
		</div>

		<?php
        }



	
	/*
	// Show Featured Products
	if( $this->get_cfg( 'showFeatured', 1 )) {
		//featuredproducts(random, no_of_products,category_based) no_of_products 0 = all else numeric amount
		//edit featuredproduct.tpl.php to edit layout
		echo $ps_product->featuredProducts(true,10,true);
	}
	*/
	?>
	<div class="clr"></div>
	<?php
	echo $recent_products;
?>