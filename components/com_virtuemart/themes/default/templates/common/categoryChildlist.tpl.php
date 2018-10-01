<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
mm_showMyFileName(__FILE__);

$iCol = 1;
if( !isset( $categories_per_row )) {
	$categories_per_row = 3;
}
$cellwidth = intval( 100 / $categories_per_row );

if( empty( $categories )) {
	return; // Do nothing, if there are no child categories!
}

?>
<div class="childlist">
<?php
foreach($categories as $category) {
?>                                                                             
    <!--div style="float:left;width:320px;height:200px;"-->
    <a title="<?php echo htmlspecialchars($category["category_name"]) ?>" href="<?php $sess->purl(URL."index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=".$category["category_id"]) ?>">
    <!--span class="item"><?php echo htmlspecialchars($category["category_name"]); ?></span-->
       <!--?php
        if ( $category["category_thumb_image"] ) {
				  echo ps_product::image_tag( $category["category_thumb_image"], "alt=\"".$category["category_name"]."\"", 0, "category");
        }
       ?-->
       <span class="item"><?php echo htmlspecialchars($category["category_name"]); ?></span>
    </a>
    <!--/div-->
<?php
}
?>
</div>
<br />
