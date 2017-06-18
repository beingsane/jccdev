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
 * JCCDev Formfields View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewFormfields extends JViewLegacy
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
		
		JCCDevHelper::addSubmenu('formfields');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "formfields"), JCCDevLAYOUTS);
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_JCCDEV_FORMFIELDS'));
		JToolBarHelper::addNew('formfield.add');
		JToolBarHelper::editList('formfield.edit');
		JToolBarHelper::deleteList('', 'formfields.delete', 'JTOOLBAR_DELETE');

		JHtml::_('bootstrap.modal', 'collapseModal');

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');
		$dhtml = $layout->render(array('title' => JText::_('JTOOLBAR_BATCH')));
		$bar->appendButton('Custom', $dhtml, 'batch');

		JToolBarHelper::preferences('com_jccdev');
	}
}