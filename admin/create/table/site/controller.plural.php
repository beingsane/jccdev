<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Table
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("table", JCCDevCREATE);;

/**
 * Table Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Table
 */
class JCCDevCreateTableSiteControllerPlural extends JCCDevCreateTable
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "site.controllers.plural.php";

	/**
	 * Check whether file should be created or not
	 *
	 * @return	boolean
	 */
	protected function condition()
	{
		return (int) $this->table->params['frontend'];
	}
}