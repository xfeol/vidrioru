<?php

define ('_VDEXEC', 1);
define ('VDPATH_BASE', dirname(__FILE__));
define ('DS', DIRECTORY_SEPARATOR);
define ('VD_BASE', 'vdcms');

require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'mysqldatabase.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'mysqlresultset.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'configuration.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'session.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'pscategories.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'modules.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'router.php');
require_once (VDPATH_BASE .DS.VD_BASE.DS. 'classes'.DS.'psproducts.php');

$sess =  Session::getInstance();
$conf = new VDConfig();

$db = MySqlDatabase::getInstance();
$db->connect($conf->db_host, $conf->db_user, $conf->db_password, $conf->db_name);

$cats = new CCategories();

$bout = ob_start();
/********************************/

require(VDPATH_BASE .DS.VD_BASE.DS. 'template'.DS. 'index.php');

/********************************/
if ($bout)
    while(@ob_end_flush());
