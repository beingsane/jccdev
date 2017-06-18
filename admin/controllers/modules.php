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
 * JCCDev Modules Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerModules extends JControllerAdmin
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
	public function getModel($name = 'Module', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Create ZIP file of modules
	 *
	 * @param	array	$ids	The primary keys of the items
	 */
	public function create($ids = array())
	{
		// Initialize
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=module', false));

		empty($ids) ? $ids = $app->input->get('cid', array(), 'array') : null;
		
		// Check access
		if (!$user->authorise('modules.create', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		// Load classes
		JCCDevLoader::import('archive');
		JCCDevLoader::import('template');
		JCCDevLoader::import('module', JCCDevCREATE);
		jimport('joomla.filesystem.folder');

		// Create module for each id
		foreach ($ids as $id)
		{
			$module = $this->getModel()->getItem($id);
			$path = $module->createDir;
			
			// Delete old archive if exists
			(JFile::exists($path.'.zip')) ? JFile::delete($path.'.zip') : null;
			
			// Create module
			try {
				JCCDevCreateModule::execute(array("item_id" => $id));
			}
			catch (JCCDevException $e) {
				$this->setMessage($e->getMessage(), "error");
				return;
			}
						
			// Create HTML files for each folder, zip the folder and delete the folder
			JCCDevArchive::html($path);
			
			// Create ZIP archive and delete folder
			JCCDevArchive::zip($path);
			JFolder::delete($path);
		}

		$this->setMessage(JText::sprintf('COM_JCCDEV_MODULE_MESSAGE_ZIP_CREATED', count($ids)));
	}	

	/**
	 * Delete ZIP files of modules
	 */
	public function deletezip()
	{
		require_once JCCDevLIB . "/archive.php";
		
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=module', false));
		
		if (!$user->authorise('modules.deletezip', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		foreach ($ids as $id)
		{
			$module = $this->getModel()->getItem($id);
			$files = JCCDevArchive::getVersions("mod_", $module->name);
			
			foreach ($files as $file)
			{
				JFile::delete($module->createDir . ".zip");
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_MODULE_MESSAGE_ZIP_DELETED', count($ids)));
	}

	/**
	 * Install modules
	 */
	public function install()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=module', false));
		
		if (!$user->authorise('modules.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		jimport('joomla.filesystem.folder');
		require_once JCCDevLIB . '/install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Install modules
		foreach ($ids as $id)
		{
			$module = $this->getModel()->getItem($id);
			$path = $module->createDir . '.zip';
			$this->create(array($id));
			
			if (JCCDevInstall::isInstalled('module', 'mod_' . $module->name))
			{
				JCCDevInstall::install($path, true);
			}
			else
			{
				JCCDevInstall::install($path);
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_MODULE_MESSAGE_INSTALLED', count($ids)));
	}

	/**
	 * Uninstall modules
	 */
	public function uninstall()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=module', false));
		
		if (!$user->authorise('modules.uninstall', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		require_once JCCDevLIB.DS. 'install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Uninstall modules
		foreach ($ids as $id)
		{
			$module = $this->getModel()->getItem($id);
			JCCDevInstall::uninstall("module", "mod_" . $module->name);
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_MODULE_MESSAGE_UNINSTALLED', count($ids)));
	}
}