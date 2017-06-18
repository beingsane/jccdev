<?php
/**
 * @package     JCCDev
 * @subpackage  Controllers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Components Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerComponents extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Component', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Install components
	 */
	public function install()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=component', false));

		// Check if action is allowed
		if (!$user->authorise('components.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		jimport('joomla.filesystem.folder');
		require_once JCCDevLIB.DS. 'install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
				
		// Install components
		foreach ($ids as $id)
		{
			$component = $this->getModel()->getItem($id);
			$path = $component->createDir . '.zip';
			$this->create(array($id));
			
			if (JCCDevInstall::isInstalled('component', 'com_' . $component->name))
			{
				JCCDevInstall::install($path, true);
			}
			else
			{
				JCCDevInstall::install($path);
			}
		}
				
		$this->setMessage(JText::sprintf('COM_JCCDEV_COMPONENT_INSTALLED', count($ids)));
	}
	
	/**
	 * Uninstall components
	 */
	public function uninstall()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=component', false));
		
		if (!$user->authorise('components.uninstall', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		require_once JCCDevLIB.DS. 'install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		foreach ($ids as $id)
		{
			$component = $this->getModel()->getItem($id);
			JCCDevInstall::uninstall("component", "com_" . $component->name);
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_COMPONENT_UNINSTALLED', count($ids)));
	}
	
	/**
	 * Create component ids
	 *
	 * @param	array	$ids	The component 
	 */
	public function create($ids = array())
	{
		// Initialize
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=component', false));

		if (empty($ids))
		{
			$ids = $app->input->get('cid', array(), 'array');
		}
		
		// Check access
		if (!$user->authorise('components.create', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		// Load classes
		JCCDevLoader::import('archive');
		JCCDevLoader::import('template');
		JCCDevLoader::import('component', JCCDevCREATE);
		JCCDevLoader::import('table', JCCDevCREATE);
		jimport('joomla.filesystem.folder');

		// Create component for each id
		foreach ($ids as $id)
		{
			$component = $this->getModel()->getItem($id);
			$path = $component->createDir;

			// Delete old archive if exists
			JFile::exists($path . '.zip') ? JFile::delete($path . '.zip') : null;
			
			// Create component
			JCCDevCreateComponent::execute("admin", array("item_id" => $id));
			JCCDevCreateTable::execute("admin", array("item_id" => $id));
			
			// Create component for frontend
			if ($component->get('site', 0))
			{
				JCCDevCreateComponent::execute("site", array("item_id" => $id));
				JCCDevCreateTable::execute("site", array("item_id" => $id));
			}
			
			// Get language files content
			$buffer = JCCDevLanguage::getStaticInstance("com_" . $component->name)->getINI();
			$buffer_sys = JCCDevLanguage::getStaticInstance("com_" . $component->name . "_sys")->getINI();

			// Write language files
			foreach ($component->params["languages"] as $lang)
			{
				JFile::write($component->createDir . "/admin/language/$lang.com_" . strtolower($component->name) . ".ini", $buffer);
				JFile::write($component->createDir . "/admin/language/$lang.com_" . strtolower($component->name) . ".sys.ini", $buffer_sys);
				$component->site ? JFile::write($component->createDir . "/site/language/$lang.com_" . strtolower($component->name) . ".ini", $buffer) : null;
			}
			
			// Create HTML files for each folder
			JCCDevArchive::html($path . '/admin');
			((int) $component->get('site', 0)) ? JCCDevArchive::html($path . '/site') : null;
			
			// Create ZIP archive and delete folder
			JCCDevArchive::zip($path);
			JFolder::delete($path);
		}

		$this->setMessage(JText::sprintf('COM_JCCDEV_COMPONENT_CREATED', count($ids)));
	}
	
	/**
	 * Delete ZIP files of component
	 */
	public function deletezip()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=component', false));
		
		if (!$user->authorise('components.deletezip', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		foreach ($ids as $id)
		{
			$component = $this->getModel()->getItem($id);
			$files = JCCDevArchive::getVersions("com_", $component->name);
			
			foreach ($files as $file)
			{
				JFile::delete(JCCDevArchive::getArchiveDir() . "/" . $file);
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_COMPONENT_ZIP_DELETED', count($ids)));
	}
}