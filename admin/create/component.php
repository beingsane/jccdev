<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Component
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
require_once JCCDevLIB.DS. 'create.php';

/**
 * Basic create class for components
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponent extends JCCDevCreate
{	
	/*
	 * The component data
	 *
	 * @var	JObject
	 */
	protected $component;
	
	/*
	 * The component folder
	 *
	 * @var	string
	 */
	protected $createDir;
	
	/*
	 * The fields data
	 *
	 * @var	array<JObject>
	 */
	protected $fields;
	
	/*
	 * The direction where the file should be created
	 *
	 * @var	string
	 */
	protected $filePath;
	
	/*
	 * Look for language keys in the template and add them
	 *
	 * @var	boolean
	 */
	protected $getLangKeys = false;
	
	/*
	 * The current table data
	 *
	 * @var	JObject
	 */
	protected $table;
	
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
		if (!isset($config['item_id']) || empty($config['item_id'])) throw new JCCDevException($this->_name . ": No component id given");
		$this->config=$config;
		
		// Get component data
		$this->component = $this->getModel('component')->getItem($config['item_id']);
		
		// Get table data
		$this->tables = $this->getModel('tables')->getComponentTables($config['item_id']);
		
		// Get component directory
		$this->createDir = $this->component->createDir;
		$this->filePath = $this->templateFile;
		
		// Set template base dirs
		$this->templateDirs[0] = JCCDevXTD . "/templates/component";
		$this->templateDirs[1] = JCCDevTEMPLATES . "/component";
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
				'Version' => $this->component->version,
				'Extension' => ucfirst($this->component->display_name),
			)
		);
		self::$templateHeader = $header->getBuffer();
	}
	
	/**
	 * Add language keys
	 *
	 * @param	array	$langkeys	The language keys
	 * @param	string	$name		The language section name
	 *
	 * @return	void
	 */
	public function addLanguageKeys($langkeys, $name = "")
	{
$pos = strpos($langkeys, '## singular ##');
if ($pos) {
print $langkeys;
$pos->ppp();
}
		$language = $this->getLanguage($name);
		$prefix = "COM_" . strtoupper($this->component->name);

		foreach ($langkeys as $langkey)
		{
			$search = str_replace($prefix, "JCCDEV", $langkey);
			$search = str_replace("_" . strtoupper($this->table->name), "_TABLE", $search);
			$search = str_replace("_" . strtoupper($this->table->plural), "_PLURAL", $search);
			$search = str_replace("_" . strtoupper($this->table->singular), "_SINGULAR", $search);

			$translation = JText::_($search);
			$translation = preg_replace("/##component##/i", $this->component->display_name, $translation);
			$translation = preg_replace("/##plural##/i", $this->table->plural, $translation);
			$translation = preg_replace("/##singular##/i", $this->table->singular, $translation);

			$language->addKeys(array($langkey => $translation), "", false);
		}
	}

	/**
	 * Initialize template and create the files
	 *
	 * @return	boolean	true on success, false otherwise
	 */
	public function create()
	{
		$db = JFactory::getDbo();
		
		// Check condition
		if ($this->condition())
		{
			if (!$this->initialize()->write())
			{
				$this->setError($this->_name . " : Could not create file");
				return false;
			}
			
			if ($this->getLangKeys)
			{
				$this->addLanguageKeys($this->template->getLanguageKeys(array("COM_" . strtoupper($this->component->name) . "_[A-Z0-9_]*")));
			}
		}
		
		return true;
	}
		
	/**
	 * Execute Create Instance
	 *
	 * @param	string	$client	(admin | site | jcc)
	 * @param	array	$config	Configuration
	 */
	public static function execute($client, $config = array())
	{
		$client = str_replace(".", DS, $client);
		$component = JModelLegacy::getInstance("Component", "JCCDevModel")->getItem($config["item_id"]);
		
		// Create folders
		JFolder::create($component->createDir . "/admin/controllers");
		JFolder::create($component->createDir . "/admin/models");
		JFolder::create($component->createDir . "/admin/models/forms");
		JFolder::create($component->createDir . "/admin/tables");
		JFolder::create($component->createDir . "/admin/views");
		
		if ($component->site)
		{
			JFolder::create($component->createDir . "/site/controllers");
			JFolder::create($component->createDir . "/site/models");
			JFolder::create($component->createDir . "/site/views");
			JFolder::create($component->createDir . "/site/helpers");
//   		if ($component->twig) {
	 		  JFolder::create($component->createDir . "/site/twig");
//		  }
		}
		// Get each file from this folder and get instance of create class
		foreach (JFolder::files(JCCDevCREATE . "/component/$client", "php$") as $file)
		{
			$class = JCCDevCreate::getInstance("component.$client." . JFile::stripExt($file), $config);
			if (!$class->create())
			{
				$errors = $class->getErrors();
				if (!empty($errors)) throw new JCCDevException($errors);
			}
		}
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultAreas()
	{
		$areas = array(
			'admin'				=> preg_match('/^admin/', $this->filePath),
			'site'				=> preg_match('/^site/', $this->filePath),
			'header' 			=> false,
		);
		
		// Two cases : no tables | tables
		if (empty($this->tables))
		{
			$fields = $this->getModel('table')->getForm()->getGroup('jfields');
			foreach ($fields as $field) $areas[$field->fieldname] = false;
		}		
		else
		{
			foreach ($this->tables as $table)
			{
				foreach ($table->jfields as $field => $val)
				{
					if (isset($areas[$field]) && $areas[$field] == true) 
						continue;
					else
						$areas[$field] = (bool) $val;
				}
			}
		}
		
		return array_merge($areas, parent::getDefaultAreas());
	}

	/**
	 * @see	JCCDevCreate
	 */
	protected function getDefaultPlaceholders()
	{
		$placeholders = array(
			'component'		=> $this->component->name,
			'defaultview'	=> (isset($this->tables[0])) ? $this->tables[0]->plural : $this->component->name,
			'header' 		=> self::$templateHeader
		);
		
		return array_merge($placeholders, parent::getDefaultPlaceholders());
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function getLanguage($name = "")
	{
		$_name = "com_" . $this->component->name;

		if (!empty($name))
		{
			$_name .= "_" . $name;
		}

		return JCCDevLanguage::getStaticInstance($_name, strtoupper("COM_" . $this->component->name));
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function getTemplate()
	{
		$template = parent::getTemplate();
		
		if ($template === false)
		{
			// If any errors exist throw Exception
			$errors = $this->getErrors();
			if (!empty($errors))
			{
				throw new JCCDevException($errors);
			}
			
			// Look for template with same name in admin folder
			$this->templateFile = preg_replace('/^site/', 'admin', $this->templateFile);
			$template = parent::getTemplate();
			$this->templateFile = preg_replace('/^admin/', 'site', $this->templateFile);

			if ($template === false)
			{
				$this->setError($this->_name . ": No template found");
				throw new JCCDevException($this->getErrors());
			}
			
			$this->template = $template;
			return $this->template;
		}
		
		$this->template = $template;
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
	
	/**
	 * @see	JCCDevCreate
	 */
	protected function loadOverride($type = "", $item_id = "", $name = "")
	{
		if (empty($type))
		{
			$type = "component.";
			$type .= preg_match("/^JCCDevCreateComponentAdmin/", $this->_name) ? "admin" : "site";
		}
		
		$item_id = $item_id == "" ? $this->component->id : $item_id;
		$name = $name == "" ? $this->templateFile : $name;
		
		return parent::loadOverride($type, $item_id, $name);
	}

	/**
	 * @see	JCCDevCreate
	 */
	public function loadSubtemplate($filename, $sep = '.')
	{
		$sub = parent::loadSubtemplate($filename);
		
		if ($sub === false)
		{
			$templateFile = JFile::stripExt($this->templateFile) . $sep . $filename;
			$templateFile = preg_replace("/^site/", "admin", $templateFile);
			
			foreach ($this->templateDirs as $templateDir)
			{
				$dir = JCCDevPath::dots2ds($templateDir . "/" . $templateFile);
				
				if ($dir !== false)
				{
					return new JCCDevTemplate($dir);
				}
			}

			$this->setError($this->_name . ": Subtemplate <i>'$basepath.$filename'</i> not found");
			throw new JCCDevException($this->getErrors());
		}
		
		return $sub;
	}
	
	/**
	 * @see	JCCDevCreate
	 */
	public function write($path = '')
	{
		if ($path == '') 
		{
			$path = $this->filePath;
			$path = str_replace(".", DIRECTORY_SEPARATOR, JFile::stripExt($path)) . "." . JFile::getExt($path);
			$path = str_replace('component', $this->component->name, $path);
			$path = str_replace('install' . DS . 'mysql' . DS . 'utf8', 'install.mysql.utf8', $path);
			$path = str_replace('uninstall' . DS . 'mysql' . DS . 'utf8', 'uninstall.mysql.utf8', $path);
			$path = str_replace('view' . DS . 'feed', 'view.feed', $path);
			$path = str_replace('view' . DS . 'html', 'view.html', $path);
			$path = str_replace('version', $this->component->version, $path);

			$path = str_replace('jcc', 'site', $path); // jcc + site = site

			$path = $this->createDir . "/" . $path;
		}
		
		return parent::write(strtolower($path));
	}
}