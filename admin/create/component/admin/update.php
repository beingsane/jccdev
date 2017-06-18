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
JCCDevLoader::importHelper("table");

/**
 * Component Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentAdminUpdate extends JCCDevCreateComponent
{		
	protected function condition()
	{
		return !empty($this->tables);
	}
	
	public function initialize()
	{
		$buffer = array();
		$model = $this->getModel("Field");
		$db = JFactory::getDbo();
		
		foreach ($this->tables as $table)
		{
			if (in_array($table->dbname, $db->getTableList()))
			{
				$add = JCCDevHelperTable::missingExtern($table->id, "#__" . $table->dbname);
				$drop = JCCDevHelperTable::missingIntern($table->id, "#__" . $table->dbname);
				
				foreach ($add as $field) $buffer[] = "ALTER TABLE #__$table->dbname ADD " . $model->toSQL($field) . ";";
				foreach ($drop as $column) $buffer[] = "ALTER TABLE #__$table->dbname DROP `" . $column->Field . "`;";
			}
		}
		
		if (!empty($buffer))
		{
			$this->template->addPlaceholders(array("code" => implode("\n", $buffer)));
		}
		else
		{
			$this->template->addPlaceholders(array("code" => "\n"));
		}

		return parent::initialize();
	}	
	
	protected function getTemplate()
	{
		return new JCCDevTemplate("##code##", false);
	}
	
	public function write($path = "")
	{
		return parent::write($this->createDir . "/admin/sql/updates/" . $this->component->version . ".sql");		
	}
}