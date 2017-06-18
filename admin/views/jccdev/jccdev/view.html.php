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
 * JCCDev View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewJCCDev extends JViewLegacy
{	
	protected $items;
	
	public function display($tpl = null)
	{
		$this->archives = JModelLegacy::getInstance("Archive", "JCCDevModel")->getItems();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JCCDevHelper::addSubmenu('jccdev');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "jccdev"), JCCDevLAYOUTS);
		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JCCDEV'));
		JToolBarHelper::preferences('com_jccdev');
	}
}