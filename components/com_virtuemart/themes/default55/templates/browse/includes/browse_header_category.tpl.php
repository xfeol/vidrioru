<?php
	/**
	* TemplatePlazza.com 
	**/
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
	mm_showMyFileName(__FILE__);
?>
<div class="producttitle">
	<h3>
	<?php 
		echo $browsepage_lbl;
		if( $this->get_cfg( 'showFeedIcon', 1 ) && (VM_FEED_ENABLED == 1) ) {
	?>
			<a href="index.php?option=<?php echo VM_COMPONENT_NAME ?>&amp;page=shop.feed&amp;category_id=<?php echo $category_id ?>" title="<?php echo $VM_LANG->_('VM_FEED_SUBSCRIBE_TOCATEGORY_TITLE') ?>">
				<img src="<?php echo VM_THEMEURL ?>/images/feed-icon-14x14.png" align="middle" alt="feed" border="0"/></a>
			</a>
	<?php } ?>
		<span class="buttons_heading"><?php echo vmCommonHTML::PrintIcon(); ?></span>
	</h3>
</div>
<?php if($navigation_childlist){ ?><div class="childlist"><?php echo $navigation_childlist; ?></div><?php } ?>
<?php if( trim(str_replace( "<br />", "" , $desc)) != "" ) { ?>

		<div style="width:100%;float:left;">
			<?php echo $desc; ?>
		</div>
		<br class="clr" /><br />
		<?php
     }
?>