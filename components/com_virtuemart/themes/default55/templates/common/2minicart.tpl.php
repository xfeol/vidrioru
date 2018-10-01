<?php /**
* @Copyright Copyright (C) 2008 - 2010 IceTheme
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
******/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

if($empty_cart) { ?>
    
    <div style="margin: 0 auto; text-align:center">
    <?php if(!$vmMinicart) { ?>
   <p> <?php }
    echo $VM_LANG->_('PHPSHOP_EMPTY_CART') ?>
    </p>
    </div>
<?php } 
else {
	$db		= &JFactory::getDBO();
    // Loop through each row and build the table
    foreach( $minicart as $idx => $cart ) { 		

		foreach( $cart as $attr => $val ) {
			// Using this we make all the variables available in the template
			// translated example: $this->set( 'product_name', $product_name );
			$this->set( $attr, $val );
			
		}
	
        if(!$vmMinicart) { // Build Minicart
			echo '<div class="ice-basket-row '.(($idx%2==0)?'even':'odd').'">';
         	$tmp = explode("&", str_replace("&amp;",'&',$cart['url']) );
			$pid = 0;
			foreach( $tmp as $item ){
				list( $var, $val )	= explode( "=", $item );
				if( trim($var) == 'product_id' ){
					$pid = $val;		
				}
			}
			$query	= "SELECT product_thumb_image FROM #__vm_product WHERE product_id=" . $db->quote($pid);
			$db->setQuery($query);
			$thumb	= $db->loadResult();
			echo "<a href=\"" . $cart['url'] . "\">";
			echo ps_product::image_tag( $thumb, "alt=\"".$cart['product_name']."\" class=\"ice-image\"");
			echo "</a>";
			?>
            
                <div class="ice-backet-wrapper">
                
                    <div class="ice-prod-descr">
                    <?php echo $cart['quantity'] ?>&nbsp;x&nbsp;<a href="<?php echo $cart['url'] ?>"><?php echo $cart['product_name'] ?></a>
                    </div>
                    <div class="ice-price">
                    <?php echo $cart['price'] ?>
                    </div>
                    
                    <div class="ice-attributes">
                    <?php echo $cart['attributes'];?>
                    </div>
                    
                </div>
            
           </div> 
           <?php  
        }
    }
}
if(!$vmMinicart) { ?>
<?php } ?>
<div class="ice-cartinfo clearfix" style="clear:both">
    <div class="ice-totalproduct" >
    <?php echo $total_products ?>
    </div>
    <div class="ice-totalprice">
    <?php echo $total_price ?>
    </div>
</div>
<?php if (!$empty_cart && !$vmMinicart) { ?>
   <div class="ice-showcart" align="center">
 	   <?php echo $show_cart ?>
   </div>
<?php } 
echo $saved_cart;
?>