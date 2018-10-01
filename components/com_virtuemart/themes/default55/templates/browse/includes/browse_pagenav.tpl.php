<?php
	/**
	* TemplatePlazza.com 
	**/
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
	mm_showMyFileName(__FILE__);
	if(!@is_object( $pagenav)) return;
?>

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="pagination_bottom">
		<tr>
			<td align="right"><?php $pagenav->writePagesLinks( $search_string ); ?></td>
		</tr>
	</table>