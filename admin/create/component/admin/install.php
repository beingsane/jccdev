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
class JCCDevCreateComponentAdminInstall extends JCCDevCreateComponent
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "admin.sql.install.mysql.utf8.sql";
	
	protected function condition()
	{
		return !empty($this->tables);
	}
	
	protected function initialize()
	{
		$buffer = array();
		
		foreach ($this->tables as $table)
		{
			$create = JCCDevCreate::getInstance("table.admin.sql", array("item_id" => $table->id));
			$buffer[] = $create->getBuffer();
		}
		
		$this->template = new JCCDevTemplate(implode("\n\n", $buffer), false);		
		return parent::initialize();
	}
}