<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Field
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("create");

/**
 * Basic create class for field
 *
 * @package     JCCDev
 * @subpackage  Create.Field
 */
class JCCDevCreateField extends JCCDevCreate
{	
	/*
	 * The component data
	 *
	 * @var	JObject
	 */
	protected $component;
	
	/*
	 * The fields data
	 *
	 * @var	JObject
	 */
	protected $field;
	
	/*
	 * The current table data
	 *
	 * @var	JObject
	 */
	protected $table;
	
	/**
	 * The constructor
	 */
	public function __construct($config = array())
	{		
		parent::__construct();
		
		$app = JFactory::getApplication();
		if (!isset($config['FieldId']) || empty($config['FieldId'])) throw new JCCDevException($this->_name . ": No field id given");
		
		// Get field data
		$this->field = $this->getModel('field')->getItem($config['FieldId']);

		// Get table data
		$this->table = $this->getModel('table')->getItem($this->field->table);
		
		// Get component data
		$this->component = $this->getModel('component')->getItem($this->table->component);

		// Create component directory
		$this->createDir = $this->component->createDir;
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultAreas()
	{
		$areas = array(
			'header' => false
		);
		
		return array_merge($areas, parent::getDefaultAreas());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultPlaceholders()
	{
		$placeholders = array(
		);
		
		return array_merge($placeholders, parent::getDefaultPlaceholders());
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function getLanguage()
	{
		return JCCDevLanguage::getStaticInstance("com_" . $this->component->name);
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function getTemplate()
	{
		if (false === $path = JCCDevPath::dots2ds(JCCDevTEMPLATES . "/fields/" . $this->templateFile))
		{		
			$this->setError($this->name . ": Template <i>$this->templateFile</i> not found");
		}
		
		$this->template = new JCCDevTemplate($path);		
		return $this->template;
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
}