<?php
/**
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Directories
define('JCCDARCHIVE', JPATH_COMPONENT_ADMINISTRATOR.'/archive');

define('JCCDevCONFIG', JPATH_COMPONENT_ADMINISTRATOR.'/config');
define('JCCDevCREATE', JPATH_COMPONENT_ADMINISTRATOR.'/create');
define('JCCDevINSTALL', JPATH_COMPONENT_ADMINISTRATOR.'/tmp');
define('JCCDevLAYOUTS', JPATH_COMPONENT_ADMINISTRATOR.'/layouts');
define('JCCDevLIB', JPATH_COMPONENT_ADMINISTRATOR.'/library');
define('JCCDevLIVE', JPATH_COMPONENT_ADMINISTRATOR.'/archive/live');
define('JCCDevTEMPLATES', JPATH_COMPONENT_ADMINISTRATOR.'/templates');
define('JCCDevXTD', JPATH_COMPONENT_ADMINISTRATOR.'/xtd');

// URLs
define('JCCD_URL', JURI::root().'/administrator/components/com_jccdev');
define('JCCDARCHIVEURL', JCCD_URL . "/archive");
define('JCCDLIVEURL', JCCD_URL . "/archive/live");
?>