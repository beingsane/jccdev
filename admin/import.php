<?php
/**
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

require_once JCCDevLIB . '/loader.php';

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

JCCDevLoader::import("archive");
JCCDevLoader::import("exception");
?>