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
 * JCCDev Module View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewModule extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;
	
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$this->_layout == "edit" ? $input->set('hidemainmenu', true) : null;
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		if ($this->_layout == "default")
		{
			$model = JModelLegacy::getInstance("Overrides", "JCCDevModel");
			$this->overrides = $model->getOverrides("module", $this->item->id);
			
			$model = JModelLegacy::getInstance("Modules", "JCCDevModel");
			$this->items = $model->getItems();
		}
				
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "modules"), JCCDevLAYOUTS);
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		if ($this->_layout == "default")
		{
			JToolBarHelper::title(JText::_('COM_JCCDEV_MODULE'));
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_JCCDEV_MODULE'));
			JToolBarHelper::apply('module.apply');
			JToolBarHelper::save('module.save');
			JToolBarHelper::save2copy('module.save2copy');
			JToolBarHelper::save2new('module.save2new');
			JToolBarHelper::cancel('module.cancel', 'JTOOLBAR_CANCEL');
		}
	}
}