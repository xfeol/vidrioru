<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsLayout{
	function prepare(){
		echo "<script type=\"text/javascript\">
		window.addEvent('domready', function(){
			var toolbar=$('toolbar-box'),
				elementbox=$('element-box');
				
			if (toolbar) toolbar.remove();
			if (elementbox) {
				elementbox.getFirst().remove();
				elementbox.getLast().remove();
				elementbox.getFirst().className='Hello';
			}
			//$('module-menu').setStyles({'float':'right','margin-right':'20px'});
		});
		</script>";	
	}
}