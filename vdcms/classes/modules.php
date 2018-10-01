<?php

defined ('_VDEXEC') or die('Restricted access');

class Module
{
    function show($modulename, $caption = NULL)
    {
	echo '<div class="moduletable">';
	if (!is_null($caption))
	    echo "<h3>$caption</h3>";
	include('vdcms/template/modules/'.$modulename);
	echo '</div>';
    }
}