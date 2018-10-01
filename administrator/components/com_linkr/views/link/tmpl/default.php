<?php defined('_JEXEC') or die;

// Check install
if (!LinkrHelper::isInstalled()) {
	echo $this->loadTemplate('install');
}

// Display landing page
else {
	echo $this->loadTemplate('landing');
}
