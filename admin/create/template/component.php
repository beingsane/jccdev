<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Template
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("template", JCCDevCREATE);

/**
 * Template Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Template
 */
class JCCDevCreateTemplateComponent extends JCCDevCreateTemplate
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "component.php";
}