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
class JCCDevCreateTableAdminRules extends JCCDevCreateTable
{
	/**
	 * Create field content
	 */
	public function create()
	{
		foreach ($this->getModel("fields")->getTableFields($this->table->id) as $field)
		{
			if (!empty($field->formrule_id))
			{
				$rule = $this->getModel("formrule")->getItem($field->formrule_id);
				$this->setTemplate(new JCCDevTemplate(self::$templateHeader . $rule->source, false));
				$this->write($this->createDir . "/admin/models/rules/" . $rule->name . ".php");
			}
		}
	}
	
	protected function getTemplate()
	{
		return null;
	}
}