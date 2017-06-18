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
class JCCDevCreateTableAdminFields extends JCCDevCreateTable
{
	/**
	 * Create field content
	 */
	public function create()
	{
		foreach ($this->getModel("fields")->getTableFields($this->table->id) as $field)
		{
			if (!empty($field->formfield_id))
			{
				$formfield = $this->getModel("formfield")->getItem($field->formfield_id);
				$this->setTemplate(
					new JCCDevTemplate( preg_replace("/^[ ]*<\?php/", "<?php\n" . self::$templateHeader, $formfield->source), false)
				);
				$this->write($this->createDir . "/admin/models/fields/" . $formfield->name . ".php");
			}
		}
	}
	
	protected function getTemplate()
	{
		return null;
	}
}