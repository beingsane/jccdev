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
 * JCCDev Fields View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewFields extends JViewLegacy
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
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "fields"), JCCDevLAYOUTS);
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_JCCDEV_FIELDS'));
		JToolBarHelper::addNew('field.add');
		JToolBarHelper::editList('field.edit');
		JToolBarHelper::deleteList('', 'fields.delete', 'JTOOLBAR_DELETE');

		JHtml::_('bootstrap.modal', 'collapseModal');

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');
		$dhtml = $layout->render(array('title' => JText::_('JTOOLBAR_BATCH')));
		$bar->appendButton('Custom', $dhtml, 'batch');

		JToolBarHelper::preferences('com_jccdev');
	}
}