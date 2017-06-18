<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Module
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("module", JCCDevCREATE);

/**
 * Module Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Modue
 */
class JCCDevCreateModuleBase extends JCCDevCreateModule
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "module.php";
}