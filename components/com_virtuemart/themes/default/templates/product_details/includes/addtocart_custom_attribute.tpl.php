<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

foreach($attributes as $attribute) {
    print_r( $attribute );
    foreach( $attribute as $attr => $val ) {
        // Using this we make all the variables available in the template
        // translated example: $this->set( 'product_name', $product_name );
        $this->set( $attr, $val );
    }
}
