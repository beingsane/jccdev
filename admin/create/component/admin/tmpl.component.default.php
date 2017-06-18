<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Component
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("component", JCCDevCREATE);

/**
 * Table Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentAdminTmplComponentDefault extends JCCDevCreateComponent
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "admin.views.component.tmpl.default.php";

	/**
	 * Check whether file should be created or not
	 *
	 * @return	boolean
	 */
	protected function condition()
	{
		return empty($this->tables);
	}
}