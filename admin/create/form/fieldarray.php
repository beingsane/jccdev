<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Form
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("form", JCCDevCREATE);

/**
 * Form Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Modue
 */
class JCCDevCreateFormFieldarray extends JCCDevCreateForm
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "fieldarray.xml";

	/**
	 * @see	JCCDevCreate
	 */
	protected function initialize()
	{
		$this->template->addPlaceholders(array(
			"name" => $this->item->name,
			"fields" => $this->getFields()
		));

		return parent::initialize();
	}
	
	/**
	 * Create fields
	 *
	 * @param	int	$id		The parent node id
	 *
	 * @return	string
	 */
	private function getFields()
	{
		$model = $this->getModel("Form");
		$table = $model->getTable();		
		$children = $table->getTree($this->item->id);
		
		$buffer = "";
		
		foreach ($children as $field)
		{
			if ($field->level != $this->item->level + 1)
			{
				continue;
			}
			elseif ($field->id == $this->item->id)
			{
				continue;
			}
			elseif ($table->isLeaf($field->id))
			{
				$buffer .= "\t" . JCCDevCreate::getInstance("form.field", array("item_id" => $field->id))->getBuffer();
			}
			else
			{
				$buffer .= JCCDevCreate::getInstance("form.fieldarray", array("item_id" => $field->id))->getBuffer();
			}
		}
		
		return $buffer;
	}
}