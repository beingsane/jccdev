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
class JCCDevCreateTableSiteTmplCategoryDefault_children extends JCCDevCreateTable
{		
	/*
	 * Look for language keys in the template and add them
	 *
	 * @var	boolean
	 */
	protected $getLangKeys = true;

	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "site.views.category.tmpl.default_children.php";

	/**
	 * Check whether file should be created or not
	 *
	 * @return	boolean
	 */
	protected function condition()
	{
		return $this->table->jfields['catid'];
	}
}