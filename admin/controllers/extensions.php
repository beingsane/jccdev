<?php
/**
 * @package     JCCDev
 * @subpackage  Controllers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
require_once JCCDevLIB.DS.'language.php';

/**
 * JCCDev Extensions Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerExtensions extends JControllerAdmin
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
	public function getModel($name = 'Extension', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Create ZIP file of existing extension
	 */
	public function zip()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		$model = JModelLegacy::getInstance("Extension", "JCCDevModel");
		
		foreach ($ids as $id)
		{
			$extension = $model->getItem($id);
			
			switch ($extension->type)
			{
				case 'component' :
					$this->component($id);
					break;
				case 'module' :
					$this->module($id);
					break;
				case 'template' :
					$this->template($id);
					break;
				case 'plugin' :
					$this->plugin($id);
					break;
				default:
					break;
			}
		}
		
		$this->setMessage(JText::sprintf('COM_JCCDEV_EXTENSIONS_CREATED', count($ids)));
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=extensions', false));
	}

	/**
	 * Create ZIP file of component
	 *
	 * @param	int		$id		The extension id
	 */
	private function component($id)
	{
		$extension = JModelLegacy::getInstance("Extension", "JCCDevModel")->getItem($id);
		$admin = false;
		$site = false;
		$PATH_ADMIN = JCCDevLIVE . "/" . $extension->element . '/admin';
		$PATH_SITE = JCCDevLIVE . "/" . $extension->element . '/site';
				
		if (JFile::exists(JCCDevLIVE . "/" .  $extension->element . '.zip')) return;

		// Copy admin files
		if (JFolder::exists(JPATH_ADMINISTRATOR . "/components/" . $extension->element))
		{
			if (!JFolder::copy(JPATH_ADMINISTRATOR . '/components/' . $extension->element, $PATH_ADMIN, '', true, true))
				throw new JCCDevException("Creating component ($id) admin files failed");
			
			$admin = true;
			JCCDevArchive::copyLanguageToArchive($extension->element, 'admin/language', 'admin');
		}
		
		// Copy site files
		if (JFolder::exists(JPATH_SITE . '/components/' . $extension->element))
		{
			if (!JFolder::copy(JPATH_SITE.DS.'components'.DS.$extension->element, $PATH_SITE, '', true, true))
				throw new JCCDevException("Creating component ($id) site files failed");
			
			$site = true;
			JCCDevArchive::copyLanguageToArchive($extension->element, 'site/language', 'site');
		}
		
		// Copy manifest file
		$xmlpath = $admin ? $PATH_ADMIN : $PATH_SITE;
		$xmlfiles = JFolder::files($xmlpath, "\.xml$");
		
		foreach ($xmlfiles as $file)
		{
			$xml = new SimpleXMLElement($xmlpath.DS.$file, null, true);
			
			if ($xml->getName() == 'extension')
			{
				JFile::move($xmlpath . "/" . $file, JCCDevLIVE . "/" . $extension->element . "/" . $file);
			}
		}
				
		// Create HTML files for each folder, zip the folder and delete the folder
		JCCDevArchive::html($PATH_ADMIN);
		if ($site) JCCDevArchive::html($PATH_SITE);

		// Create ZIP file for component and delete folder
		JCCDevArchive::zip(JCCDevLIVE . "/" . $extension->element);
		JFolder::delete(JCCDevLIVE . "/" . $extension->element);
	}
	
	private function module($id)
	{
		$extension = JModelLegacy::getInstance("Extension", "JCCDevModel")->getItem($id);
		
		if (JFile::exists(JCCDevLIVE.DS. $extension->element . '.zip')) return;
		
		if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'modules'.DS.$extension->element))
		{
			$PATH_MODULE = JPATH_ADMINISTRATOR.DS.'modules'.DS.$extension->element;
			$client = 'admin';
		}
		
		if (JFolder::exists(JPATH_SITE.DS.'modules'.DS.$extension->element))
		{
			$PATH_MODULE = JPATH_SITE.DS.'modules'.DS.$extension->element;
			$client = 'site';
		}
		
		if (!JFolder::copy($PATH_MODULE, JCCDevLIVE.DS.$extension->element, '', true, true))
			throw new JCCDevException("Copy module ($id) files failed");
			
		JCCDevArchive::copyLanguageToArchive($extension->element, 'language', $client);
		JCCDevArchive::html(JCCDevLIVE.DS.$extension->element);
		JCCDevArchive::zip(JCCDevLIVE.DS.$extension->element);
		JFolder::delete(JCCDevLIVE.DS.$extension->element);
	}
	
	private function template($id)
	{
		$extension = JModelLegacy::getInstance("Extension", "JCCDevModel")->getItem($id);

		if (JFile::exists(JCCDevLIVE.DS. $extension->element . '.zip')) return;

		if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'templates'.DS.$extension->name))
		{
			$PATH_TPL = JPATH_ADMINISTRATOR.DS.'templates'.DS.$extension->name;
			$client = 'admin';
		}
		
		if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$extension->name))
		{
			$PATH_TPL = JPATH_SITE.DS.'templates'.DS.$extension->name;
			$client = 'site';
		}
		
		if (!JFolder::copy($PATH_TPL, JCCDevLIVE.DS.$extension->filename, '', true, true))
			throw new JCCDevException("Copy template ($id) files failed");

		JCCDevArchive::copyLanguageToArchive($extension->filename, 'language', $client);
		JCCDevArchive::html(JCCDevLIVE.DS.$extension->filename);
		JCCDevArchive::zip(JCCDevLIVE.DS.$extension->filename);
		JFolder::delete(JCCDevLIVE.DS.$extension->filename);		
	}
	
	private function plugin($id)
	{
		$extension = JModelLegacy::getInstance("Extension", "JCCDevModel")->getItem($id);
		$extension_file = 'plg_'. $extension->folder .'_'. $extension->element;
		
		if (!JFolder::exists(JPATH_PLUGINS.DS.$extension->folder.DS.$extension->element)) {
			throw new Exception(JText::sprintf('COM_JCCDEV_LIVE_EXTENSION_NOT_FOUND', JText::_('COM_JCCDEV_LIVE_FIELD_TYPE_PLUGIN'), $extension->name));
		}

		$PATH_PLG = JPATH_PLUGINS.DS.$extension->folder.DS.$extension->element;
		
		if (!JFolder::copy($PATH_PLG, JCCDevLIVE.DS.$extension_file, '', true, true))
			throw new Exception(JText::sprintf('COM_JCCDEV_LIVE_COPY_FAILED', JText::_('COM_JCCDEV_LIVE_FIELD_TYPE_PLUGIN'), $extension->name));

		JCCDevArchive::copyLanguageToArchive($extension_file, 'language', 'admin');
		JCCDevArchive::html(JCCDevLIVE.DS.$extension_file);
		JCCDevArchive::zip(JCCDevLIVE.DS.$extension_file);
		JFolder::delete(JCCDevLIVE.DS.$extension_file);
	}
	
	public function deletezip()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		$model = JModelLegacy::getInstance("Extension", "JCCDevModel");

		$this->setMessage(JText::sprintf('COM_JCCDEV_EXTENSIONS_DELETED', count($ids)));
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=extensions', false));

		foreach ($ids as $id)
		{
			$item = $model->getItem($id);
			$file = JCCDevLIVE . "/" . $item->filename . ".zip";

			if (JFile::exists($file)) JFile::delete($file);
		}
	}
}