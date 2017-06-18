<?php
/**
 * @package     JCCDEV
 * @subpackage  Views
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDEV View
 *
 * @package     JCCDEV
 * @subpackage  Views
 */
class JCCDEVViewJCCDEV extends JViewLegacy
{	
	protected $items;
	
	public function display($tpl = null)
	{
		$this->archives = JModelLegacy::getInstance("Archive", "JCCDEVModel")->getItems();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JCCDEVHelper::addSubmenu('JCCDEV');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "JCCDEV"), JCCDEVLAYOUTS);
		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JCCDEV_JCCDEV'));
		JToolBarHelper::preferences('COM_JCCDEV_');
	}
}