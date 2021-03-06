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
class JCCDevCreateFormField extends JCCDevCreateForm
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "field.xml";

	/**
	 * @see	JCCDevCreate
	 */
	protected function initialize()
	{
		$this->template->addAreas(array(
			"filter" => (bool) $this->item->filter,
			"validation" => (bool) $this->item->validation,
			"deactivated" => (bool) $this->item->deactivated,
			"readonly" => (bool) $this->item->readonly,
			"required" => (bool) $this->item->required,
			"list" => !empty($this->item->options)
		));
		
		$this->template->addPlaceholders(array(
			"name" => $this->item->name,
			"type" => $this->item->type,
			"label" => $this->item->label,
			"description" => $this->item->description,
			"default" => $this->item->default,
			"maxlength" => $this->item->maxlength,
			"class" => $this->item->class,
			"filter" => $this->item->filter,
			"validation" => $this->item->validation,
			"attributes" => "",
			"options" => $this->getOptions()
		));
		
		return parent::initialize();
	}
	
	/**
	 * Create field options
	 *
	 * @return	string
	 */
	private function getOptions()
	{
		$buffer = "";
		
		if (isset($this->item->options["keys"]))
		{
			$keys = $this->item->options["keys"];
			$vals = $this->item->options["values"];
			
			for ($i = 0; $i < count($keys); $i++)
			{
				$buffer .= "\n\t<option value=\"" . $keys[$i] . "\">" . $vals[$i] . "</option>";
			}
		}
		
		return $buffer;
	}
}