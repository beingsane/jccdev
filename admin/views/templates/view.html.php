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
 * JCCDev Templates View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewTemplates extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JCCDevHelper::addSubmenu('templates');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "templates"), JCCDevLAYOUTS);
		
		parent::display($tpl);
		if (JFactory::getApplication()->input->get('ajax') == "1") exit;
	}
	
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_JCCDEV_TEMPLATES'));
		JToolBarHelper::addNew('template.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('template.edit', 'JTOOLBAR_EDIT');

		JToolBarHelper::publish('templates.create', 'JTOOLBAR_CREATE_ZIP');
		JToolBarHelper::publish('templates.install', 'JTOOLBAR_INSTALL');
		JToolBarHelper::unpublish('templates.uninstall', 'JTOOLBAR_UNINSTALL');
		JToolBarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::deleteList('', 'templates.deletezip', 'JTOOLBAR_DELETE_ZIP');
				
		JHtml::_('bootstrap.modal', 'collapseModal');

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');
		$dhtml = $layout->render(array('title' => JText::_('JTOOLBAR_BATCH')));
		$bar->appendButton('Custom', $dhtml, 'batch');
		
		JToolBarHelper::preferences('com_jccdev');
	}
	
	private function custom(&$bar, $task, $text, $class = '')
	{
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('text' => $text, 'doTask' => 'doTask(\'' . $task . '\')', 'btnClass' => 'btn btn-small', 'class' => $class));
		$bar->appendButton('Custom', $dhtml, 'batch');		
	}
}