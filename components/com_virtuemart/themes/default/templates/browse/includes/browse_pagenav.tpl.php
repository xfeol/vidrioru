<?php
    /**
    * TemplatePlazza.com
    **/
    if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
    mm_showMyFileName(__FILE__);
    if(!@is_object( $pagenav)) return;
?>

<div class="pagination_bottom">
    <?php $pagenav->writePagesLinks( $search_string ); ?>
</div>