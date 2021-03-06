<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
* @version $Id: product_type.tpl.php 2286 2011-04-30 11:25:00Z zanardi $
* @package VirtueMart
* @subpackage themes
* @copyright Copyright (C) 2006-2011 VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
?>
<?php if( empty($product_types)) return; 
/**
	 * Available indexes:
	 * 
	 * $product_type_params["parameter_label"] => The label for each product type parameter
	 * $product_type_params["parameter_description"] => The description of each product type parameter
	 * $product_type_params["tooltip"] => Same as above but wrapped in a tooltip
	 * $product_type_params["parameter_value"] => The actual value of the parameter for this product
	 * $product_type_params["parameter_unit"] => The unit of the parameter
	 * $product_type["product_type_name"] => The name of the product type
	 * 
	 */
?>
<!-- Tables of product_types -->

<?php 
foreach( $product_types as $product_type ) { // Loop through all recent products
	foreach( $product_type as $attr => $val ) {
        $this->set( $attr, $val );
        
    }
    
    if (!empty($product_type["product_type_flypage"]) && file_exists($product_type["product_type_flypage"]))
    {
	require($product_type["product_type_flypage"]);
    } else {
?>
    <div class="product_type">
    <?php 
    $i = 0;
	if ( !empty( $product_type["parameters"] ) ) {
		foreach( $product_type["parameters"] as $product_type_params ) {
    		foreach( $product_type_params as $attr => $val ) {
    			$this->set( $attr, $val );
    		}
			if ( $i % 2 ) {
    			$bgcolor = 'row0';
			}
			else {
    			$bgcolor = 'row1';
			}
			if ( $i > $product_type["product_type_count_params"] ) {
				break;
			}

			if (!empty($product_type_params["parameter_value"])) {
			$i++;
			?>
			
			
			<div class="<?php echo $bgcolor;?> prod_type_row">
			    <div class="prod_type_key"><?php echo $product_type_params["parameter_label"]; 
			     if ( !empty($product_type_params["tooltip"] ) ) { ?>
    			      &nbsp;<?php echo $product_type_params["tooltip"]; 
			     } ?>
			    </div>
			    
			    <div class="prod_type_value"><?php echo $product_type_params["parameter_value"];
			     if ( !empty($product_type_params["parameter_unit"] ) ) {
				echo " ".$product_type_params["parameter_unit"];
			     } ?>
			    </div>
			</div>
			<?php 
			
		        }
		}
	}
	?>
    </div>
    <?php 
  }
}
?>
