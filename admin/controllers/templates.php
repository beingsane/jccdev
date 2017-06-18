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
 * JCCDev Templates Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerTemplates extends JControllerAdmin
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
	public function getModel($name = 'Template', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Create ZIP file of template
	 *
	 * @param	array	$ids	The primary keys of the items
	 */
	public function create($ids = array())
	{
		// Initialize
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=template', false));
		if (empty($ids)) $ids = $app->input->get('cid', array(), 'array');
		
		// Check access
		if (!$user->authorise('templates.create', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		// Load classes
		JCCDevLoader::import('archive');
		JCCDevLoader::import('template');
		JCCDevLoader::import('template', JCCDevCREATE);
		jimport('joomla.filesystem.folder');

		// Create template for each id
		foreach ($ids as $id)
		{
			$template = $this->getModel()->getItem($id);
			$path = $template->createDir;
			
			// Delete old archive if exists
			(JFile::exists($path.'.zip')) ? JFile::delete($path.'.zip') : null;
			
			// Create template
			try {
				JCCDevCreateTemplate::execute(array("item_id" => $id));
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

		$this->setMessage(JText::sprintf('COM_JCCDEV_TEMPLATE_MESSAGE_ZIP_CREATED', count($ids)));
	}	

	/**
	 * Delete ZIP files of template
	 */
	public function deletezip()
	{
		require_once JCCDevLIB . "/archive.php";
		
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=template', false));
		
		if (!$user->authorise('templates.deletezip', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}

		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		foreach ($ids as $id)
		{
			$template = $this->getModel()->getItem($id);
			$files = JCCDevArchive::getVersions("tpl_", $template->name);
			
			foreach ($files as $file)
			{
				JFile::delete(JCCDevArchive::getArchiveDir() . "/" . $file);
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_TEMPLATE_MESSAGE_ZIP_DELETED', count($ids)));
	}

	/**
	 * Install templates
	 */
	public function install()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=template', false));
		
		if (!$user->authorise('templates.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		jimport('joomla.filesystem.folder');
		require_once JCCDevLIB . '/install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Install templates
		foreach ($ids as $id)
		{
			$template = $this->getModel()->getItem($id);
			$path = $template->createDir . ".zip";
			$this->create(array($id));
			
			if (JCCDevInstall::isInstalled('template', $template->name))
			{
				JCCDevInstall::install($path, true);
			}
			else
			{
				JCCDevInstall::install($path);
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_TEMPLATE_MESSAGE_INSTALLED', count($ids)));
	}

	/**
	 * Uninstall templates
	 */
	public function uninstall()
	{
		// Check access
		$user = JFactory::getUser();
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=template', false));
		
		if (!$user->authorise('templates.uninstall', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Initialize
		require_once JCCDevLIB.DS. 'install.php';
		
		$app = JFactory::getApplication();
		$ids = $app->input->get('cid', array(), 'array');
		
		// Uninstall templates
		foreach ($ids as $id)
		{
			$template = $this->getModel()->getItem($id);
			JCCDevInstall::uninstall("template", $template->name);
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_TEMPLATE_MESSAGE_UNINSTALLED', count($ids)));
	}
}