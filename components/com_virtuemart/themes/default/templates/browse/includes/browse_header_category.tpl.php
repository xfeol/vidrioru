<?php
	/**
	* TemplatePlazza.com 
	**/
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
	mm_showMyFileName(__FILE__);
?>

	<h1>
	<?php 
		echo $browsepage_lbl;
		if( $this->get_cfg( 'showFeedIcon', 1 ) && (VM_FEED_ENABLED == 1) ) {
	?>
			<a href="index.php?option=<?php echo VM_COMPONENT_NAME ?>&amp;page=shop.feed&amp;category_id=<?php echo $category_id ?>" title="<?php echo $VM_LANG->_('VM_FEED_SUBSCRIBE_TOCATEGORY_TITLE') ?>">
				<img src="<?php echo VM_THEMEURL ?>/images/feed-icon-14x14.png" alt="feed" >
			</a>
	<?php } ?>
	</h1>

<?php if($navigation_childlist){ ?><div class="childlist"><?php echo $navigation_childlist; ?></div><?php } ?>
