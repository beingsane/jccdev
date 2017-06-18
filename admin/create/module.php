<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Module
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("create");

/**
 * Basic Module Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Modue
 */
class JCCDevCreateModule extends JCCDevCreate
{	
	/*
	 * The component folder
	 *
	 * @var	string
	 */
	protected $createDir;

	/*
	 * The module item
	 *
	 * @var	JObject
	 */
	protected $item;
		
	/*
	 * The template header
	 *
	 * @var	string
	 */
	protected static $templateHeader;
	
	/**
	 * The constructor
	 */
	public function __construct($config = array())
	{		
		parent::__construct();

		$app = JFactory::getApplication();
		if (!isset($config['item_id']) || empty($config['item_id'])) throw new JCCDevException($this->_name . ": No module id given");
		
		// Get module data
		$this->item = $this->getModel('module')->getItem($config['item_id']);

		// Create module directory
		$this->createDir = $this->item->createDir;
		
		// Set template base dirs
		$this->templateDirs[0] = JCCDevXTD . "/templates/module";
		$this->templateDirs[1] = JCCDevTEMPLATES . "/module";
		$this->template = $this->getTemplate();
		
		if ($this->template === false)
		{
			throw new JCCDevException($this->getErrors());
		}

		// Get the template header
		$params = JComponentHelper::getParams('com_jccdev');
		$header = new JCCDevTemplate(JCCDevTEMPLATES . '/fileheader.txt');
		$header->addPlaceholders(
			array(
				'Author' => $params->get('author'),
				'Copyright' => $params->get('copyright'),
				'License' => $params->get('license'),
				'Version' => $this->item->version,
				'Extension' => ucfirst($this->item->display_name),
			)
		);
		self::$templateHeader = $header->getBuffer();
	}
	
	/**
	 * Create procedure
	 */
	public function create()
	{
		return $this->initialize()->write();
	}
	
	/**
	 * Execute Create Instance
	 *
	 * @param	array	$config	Configuration
	 */
	public static function execute($config = array())
	{
		foreach (JFolder::files(JCCDevCREATE . "/module", "php$") as $file)
		{			
			$class = JCCDevCreate::getInstance("module." . JFile::stripExt($file), $config);
			if (!$class->create())
			{
				$errors = $class->getErrors();
				if (!empty($errors)) throw new JCCDevException($errors);
			}
		}
		
		JCCDevCreate::getInstance("language.module", $config)->create();
		JCCDevCreate::getInstance("language.module.sys", $config)->create();
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultPlaceholders()
	{		
		$pk = "id";
		
		if (!empty($this->item->table))
		{
			$columns = JFactory::getDbo()->getTableColumns("#__" . $this->item->table, false);
			$pk = "id";
			foreach ($columns as $column) if ($column->Key == "PRI") $pk = $column->Field;
		}
		
		$placeholders = array(
			'header' => self::$templateHeader,
			'module' => $this->item->name,
			'pk' => $pk,
			'table_db' => $this->item->table,
			'version' => $this->item->version
		);
		
		return array_merge($placeholders, parent::getDefaultPlaceholders());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultAreas()
	{
		$areas = array(
			'header' => false,
			'table' => !empty($this->item->table)
		);
		
		return array_merge($areas, parent::getDefaultAreas());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getLanguage($name = "")
	{
		return JCCDevLanguage::getStaticInstance("mod_" . $this->item->name);
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	public function initialize()
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
		$type = $type == "" ? "module" : $type;
		$item_id = $item_id == "" ? $this->item->id : $item_id;
		$name = $name == "" ? $this->templateFile : $name;
		
		return parent::loadOverride($type, $item_id, $name);
	}

	/**
	 * @see	JCCDevCreate
	 */
	public function write($path = '')
	{
		if ($path == '') 
		{
			$path = $this->templateFile;
			$path = str_replace(".", DS, JFile::stripExt($path)) . "." . JFile::getExt($path);
			$path = preg_replace('/module/', 'mod_'. strtolower($this->item->name), $path);
			
			$path = $this->createDir . "/" . $path;
			$path = strtolower($path);
		}
		
		return parent::write($path);
	}
}