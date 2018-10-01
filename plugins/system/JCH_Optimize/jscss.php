<?php
/**
* JCH Optimize - Joomla! plugin to aggregate and minify external resources for 
*   optmized downloads
* @author Samuel Marshall <smarshall@cwjamaica.com>
* @copyright Copyright (c) 2010 Samuel Marshall. All rights reserved.
* @license GNU/GPLv3, See LICENSE file 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* If LICENSE file missing, see <http://www.gnu.org/licenses/>. 
* 
* This plugin, inspired by CssJsCompress <http://www.joomlatags.org>, was
* created in March 2010 and includes other copyrighted works. See individual 
* files for details.
*/

define('FILE_PATH', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

$jpath_cache = str_replace('plugins'.DS.'system'.DS.'JCH_Optimize', 'cache', FILE_PATH);
define('JPATH_CACHE', $jpath_cache);

$filename = $_REQUEST['f'];
$cache_group='plg_JCH_Optimize';

$path = JPATH_CACHE.DS.$cache_group.DS.$filename.'.php';

if (file_exists($path)) {
	$data = @file_get_contents($path);
	if($data) {
		// Remove the initial die() statement
		$data	= preg_replace('/^.*\n/', '', $data);
		$cached = unserialize( $data );
		$output = $cached['output'];
		$file = $cached['result'];
	}
}

$expire = @file_get_contents($path.'_expire');

if($_REQUEST['type'] == 'css'){
	header('Content-type: text/css; charset=UTF-8');
}elseif($_REQUEST['type'] == 'js'){
	header('Content-type: text/javascript; charset=UTF-8');
}
header('Expires: ' . gmdate('D, d M Y H:i:s', $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT');
header('Cache-Control: Public');
header('Vary: Accept-Encoding');

if($_REQUEST['gz'] == 'gz'){
	header("Content-Encoding: gzip");
	$file = gzencode($file);
}

echo  $file;
