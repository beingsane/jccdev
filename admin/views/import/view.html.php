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
 * JCCDev Import View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewImport extends JViewLegacy
{
	protected $form;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->form = $this->get('form');
				
		JCCDevHelper::addSubmenu('import');

		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "import"), JCCDevLAYOUTS);
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JCCDEV_IMPORT'));
	}
}