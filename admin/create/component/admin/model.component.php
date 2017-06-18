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
 * Component Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentAdminModelComponent extends JCCDevCreateComponent
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "admin.models.component.php";

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