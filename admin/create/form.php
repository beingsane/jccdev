<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Form
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("create");

/**
 * Basic create class for form
 *
 * @package     JCCDev
 * @subpackage  Create.Form
 */
class JCCDevCreateForm extends JCCDevCreate
{	
	/*
	 * The form data
	 *
	 * @var	array<JObject>
	 */
	protected $item;
	
	/**
	 * The constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		$app = JFactory::getApplication();
		if (!isset($config['item_id']) || empty($config['item_id'])) throw new JCCDevException($this->_name . ": No form id given");
		
		// Get form data
		$this->item = $this->getModel('form')->getItem($config['item_id']);

		// Set template base dirs
		$this->templateDirs[0] = JCCDevXTD . "/templates/form";
		$this->templateDirs[1] = JCCDevTEMPLATES . "/form";
		$this->template = $this->getTemplate();
		
		if ($this->template === false)
		{
			throw new JCCDevException($this->getErrors());
		}
	}

	/**
	 * Create procedure
	 */
	public function create()
	{
		return $this->initialize()->write();
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultAreas()
	{
		$areas = array();
		return array_merge($areas, parent::getDefaultAreas());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultPlaceholders()
	{
		$placeholders = array();
		return array_merge($placeholders, parent::getDefaultPlaceholders());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function initialize()
	{
		// standart placeholders
		$this->template->addAreas($this->getDefaultAreas());
		$this->template->addPlaceHolders($this->getDefaultPlaceholders(), true);
		
		return parent::initialize();
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function loadOverride($type = "", $item_id = "", $name = "")
	{
		$type = $type == "" ? "form" : $type;
		$item_id = $item_id == "" ? $this->item->id : $item_id;
		$name = $name == "" ? $this->templateFile : $name;
		
		return parent::loadOverride($type, $item_id, $name);
	}

	/**
	 * @see	JCCDevCreate
	 */
	public function write($path = '')
	{
		return parent::write(strtolower($path));
	}
}