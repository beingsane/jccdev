<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Template
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
require_once JCCDevCREATE . '/language.php';
jimport('joomla.filesystem.file');

/**
 * Template language create class
 *
 * @package     JCCDev
 * @subpackage  Create.Template
 */
class JCCDevCreateLanguageTemplateSys extends JCCDevCreateLanguage
{
	/*
	 * The template item
	 *
	 * @var	JObject
	 */
	protected $item;
	
	/*
	 * The language key prefix
	 *
	 * @var	string
	 */
	protected $prefix;
	
	/*
	 * Is this a system language file (file ending sys.ini) ? 
	 *
	 * @var	boolean
	 */
	protected $system = true;
	
	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->item = JModelLegacy::getInstance("Template", "JCCDevModel")->getItem($config['item_id']);

		$this->createDir = $this->item->createDir . "/language";
		$this->element = "tpl_" . strtolower($this->item->name);

		// Language
		$this->languages 	 = $this->item->params['languages'];
		$this->prefix 		 = "";
		
		if (empty($this->languages))
		{
			$this->languages = array('en-GB');
		}
	}
	
	/**
	 *	Creates the language files
	 *
	 *	@return	boolean		Have the files been created successfully?
	 */
	public function create()
	{		
		$this->sections[] =	$this->getINI($this->getSystem(), $this->item->name);

		if (!$this->write())
		{
			$this->setError($this->name . " : Could not create file");
			return false;
		}
		else return true;
	}

	/**
	 * Get the system language keys
	 *
	 * @return	string	The language keys
	 */
	private function getSystem()
	{
		$array = array();
		$array[$this->prefix] = ucfirst($this->item->display_name);
		return $array;
	}
}