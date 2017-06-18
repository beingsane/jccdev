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
class JCCDevCreateComponentAdminUninstall extends JCCDevCreateComponent
{		
	protected function condition()
	{
		return !empty($this->tables);
	}
	
	public function initialize()
	{
		$buffer = array();
		
		foreach ($this->tables as $table)
		{
			$buffer[] = "DROP TABLE IF EXISTS #__". $table->dbname .";";
		}

		$this->template->addPlaceholders(array("code" => implode("\n", $buffer)));
		
		return parent::initialize();
	}
	
	protected function getTemplate()
	{
		return new JCCDevTemplate("##code##", false);
	}
	
	public function write($path = "")
	{
		return parent::write($this->createDir . "/admin/sql/uninstall.mysql.utf8.sql");		
	}
}