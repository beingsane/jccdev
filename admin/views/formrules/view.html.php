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
 * JCCDev Formrules View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewFormrules extends JViewLegacy
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
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JCCDevHelper::addSubmenu('formrules');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "formrules"), JCCDevLAYOUTS);
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_JCCDEV_FORMRULES'));
		JToolBarHelper::addNew('formrule.add');
		JToolBarHelper::editList('formrule.edit');
		JToolBarHelper::deleteList('', 'formrules.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::preferences('com_jccdev');
	}
}