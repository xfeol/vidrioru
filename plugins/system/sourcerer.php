<?php
/**
 * Main Plugin File
 * Does all the magic!
 *
 * @package     Sourcerer
 * @version     2.10.0
 *
 * @author      Peter van Westen <peter@nonumber.nl>
 * @link        http://www.nonumber.nl
 * @copyright   Copyright © 2011 NoNumber! All Rights Reserved
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die();

if (
		JRequest::getCmd( 'disable_sourcerer' )
	||	JRequest::getCmd( 'format' ) == 'raw'
	||	JRequest::getCmd( 'option' ) == 'com_joomfishplus'
) {
	return;
}

require_once dirname( __FILE__ ).DS.'sourcerer'.DS.'sourcerer.php';