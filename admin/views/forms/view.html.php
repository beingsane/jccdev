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
 * JCCDev Forms View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewForms extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->authors = $this->get('Authors');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
			return false;
		}
		
		JCCDevHelper::addSubmenu('forms');
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "forms"), JCCDevLAYOUTS);
		}
		
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
		
		JToolBarHelper::title(JText::_('COM_JCCDEV_FORMS_VIEW_FORMS_TITLE'));
		
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('form.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit') || $canDo->get('core.edit.own')) && isset($this->items[0]))
		{
			JToolBarHelper::editList('form.edit','JTOOLBAR_EDIT');
		}
		
		if ($canDo->get('core.delete') && isset($this->items[0]))
		{
            JToolBarHelper::deleteList('', 'forms.delete','JTOOLBAR_DELETE');
		}
		
		// Add a batch button
		if (isset($this->items[0]) && $user->authorise('core.create', 'com_contacts') && $user->authorise('core.edit', 'com_contacts'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jccdev');
		}
	}
}
?>