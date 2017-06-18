<?php
/**
 * @package     JCCDev
 * @subpackage  Helpers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Helper
 *
 * @package     JCCDev
 * @subpackage  Helpers
 */
class JCCDevHelper
{
	/**
	 * @var    JObject  A cache for the available actions.
	 * @since  1.6
	 */
	protected static $actions;

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_JCCDEV'),
			'index.php?option=com_jccdev&view=jccdev',
			$vName == 'jccdev'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_COMPONENTS'),
			'index.php?option=com_jccdev&view=components',
			$vName == 'components'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_TABLES'),
			'index.php?option=com_jccdev&view=tables',
			$vName == 'tables'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_FIELDS'),
			'index.php?option=com_jccdev&view=fields',
			$vName == 'fields'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_FORMFIELDS'),
			'index.php?option=com_jccdev&view=formfields',
			$vName == 'formfields'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_FORMRULES'),
			'index.php?option=com_jccdev&view=formrules',
			$vName == 'formrules'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_MODULES'),
			'index.php?option=com_jccdev&view=modules',
			$vName == 'modules'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_TEMPLATES'),
			'index.php?option=com_jccdev&view=templates',
			$vName == 'templates'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_PLUGINS'),
			'index.php?option=com_jccdev&view=plugins',
			$vName == 'plugins'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_PACKAGES'),
			'index.php?option=com_jccdev&view=packages',
			$vName == 'packages'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_EXTENSIONS'),
			'index.php?option=com_jccdev&view=extensions',
			$vName == 'extensions'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_JCCDEV_SUBMENU_IMPORT'),
			'index.php?option=com_jccdev&view=import',
			$vName == 'import'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since   1.6
	 * @todo    Refactor to work with notes
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			$user = JFactory::getUser();
			self::$actions = new JObject;

			$actions = JAccess::getActions('com_jccdev');

			foreach ($actions as $action)
			{
				self::$actions->set($action->name, $user->authorise($action->name, 'com_jccdev'));
			}
		}

		return self::$actions;
	}
	
	/**
	 * Get Update information
	 */
	public static function getUpdate()
	{
		require_once JPATH_BASE . "/components/com_installer/models/update.php";

		$model = new InstallerModelUpdate();
		$items = $model->getItems();

		foreach ($items as $item)
		{
			if ($item->name == "JCCDev")
				return $item;
		}
		
		return null;
	}
}