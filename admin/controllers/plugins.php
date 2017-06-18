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
 * JCCDev Plugins Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerPlugins extends JControllerAdmin
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
	public function getModel($name = 'Plugin', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Create ZIP file of plugins
	 *
	 * @param	array	$ids	The primary keys of the items
	 */
	public function create($ids = array())
	{
		// Initialize
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=plugin', false));
		if (empty($ids)) $ids = $app->input->get('cid', array(), 'array');
		
		// Check access
		if (!$user->authorise('plugins.create', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		// Load classes
		JCCDevLoader::import('archive');
		JCCDevLoader::import('template');
		JCCDevLoader::import('plugin', JCCDevCREATE);
		jimport('joomla.filesystem.folder');

		// Create Plugin for each id
		foreach ($ids as $id)
		{
			$plugin = $this->getModel()->getItem($id);
			$path = $plugin->createDir;
			
			// Delete old archive if exists
			(JFile::exists($path.'.zip')) ? JFile::delete($path.'.zip') : null;
			
			// Create Plugin
			try {
				JCCDevCreatePlugin::execute(array("item_id" => $id));
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

		$this->setMessage(JText::sprintf('COM_JCCDEV_PLUGIN_MESSAGE_ZIP_CREATED', count($ids)));
	}	

	/**
	 * Delete ZIP files of plugin
	 */
	public function deletezip()
	{
		require_once JCCDevLIB . "/archive.php";
		
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=plugin', false));
		
		if (!$user->authorise('plugins.deletezip', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		foreach ($ids as $id)
		{
			$plugin = $this->getModel()->getItem($id);
			$files = JCCDevArchive::getVersions("plg_", $plugin->name);
			
			foreach ($files as $file)
			{
				JFile::delete($plugin->createDir . ".zip");
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_PLUGIN_MESSAGE_ZIP_DELETED', count($ids)));
	}

	/**
	 * Install plugins
	 */
	public function install()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=plugin', false));
		
		if (!$user->authorise('plugins.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		jimport('joomla.filesystem.folder');
		require_once JCCDevLIB . '/install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Install plugins
		foreach ($ids as $id)
		{
			$plugin = $this->getModel()->getItem($id);
			$path = $plugin->createDir . '.zip';
			$this->create(array($id));
			
			if (JCCDevInstall::isInstalled('plugin', 'plg_' . $plugin->name, $plugin->folder))
			{
				JCCDevInstall::install($path, true);
			}
			else
			{
				JCCDevInstall::install($path);
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_PLUGIN_MESSAGE_INSTALLED', count($ids)));
	}

	/**
	 * Uninstall plugins
	 */
	public function uninstall()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=plugin', false));
		
		if (!$user->authorise('plugins.uninstall', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		require_once JCCDevLIB.DS. 'install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Uninstall plugins
		foreach ($ids as $id)
		{
			$plugin = $this->getModel()->getItem($id);
			JCCDevInstall::uninstall("plugin", $plugin->name, $plugin->folder);
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_PLUGIN_MESSAGE_UNINSTALLED', count($ids)));
	}
}