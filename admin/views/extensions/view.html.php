<?php
/**
 * @package     JCCDev
 * @subpackage  Views
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Extensions Model
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewExtensions extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
			return false;
		}

		JCCDevHelper::addSubmenu('extensions');

		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "extensions"), JCCDevLAYOUTS);
		
		parent::display($tpl);
	}
	
	/**
	 *	Method to add a toolbar
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= JCCDevHelper::getActions();
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title(JText::_('COM_JCCDEV_EXTENSIONS'));
		
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jccdev');
		}
		
		if ($canDo->get('extensions.zip'))
		{
			JToolBarHelper::publish('extensions.zip', 'JTOOLBAR_CREATE_ZIP');
		}

		if ($canDo->get('extensions.deletezip'))
		{
			JToolBarHelper::unpublish('extensions.deletezip', 'JTOOLBAR_DELETE_ZIP');
		}
	}
}
?>