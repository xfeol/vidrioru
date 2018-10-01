<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

foreach($attributes as $attribute) {
    ?>
    
    <div style="clear:both;float:left;margin-right:10px">
    <strong><?php echo $attribute['title'].": "; ?></strong>
    </div>
    <div  style="float: left;text-align:left;white-space:normal;max-width:500px;">
    <?php foreach( $attribute['options_list'] as $options_item )
    {
	echo $options_item['base_value'];
	if ($options_item == end($attribute['options_list']))
	    echo ".";
	else
	    echo", ";
    }
//    endforeach;
    ?>
    <br />
    </div>
    
<?php 
} ?>