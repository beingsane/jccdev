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
 * JCCDev Package View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewPackage extends JViewLegacy
{
	protected $item;
	protected $form;
	
	public function display($tpl = null)
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		
		$options = array('control' => 'jform', 'load_data' => true);
		$this->form = JForm::getInstance('jccdev.package', 'package', $options);
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{		
		JToolBarHelper::title(JText::_('COM_JCCDEV_PACKAGE'));
		JToolBarHelper::save('package.create');
		JToolBarHelper::cancel('package.cancel', 'JTOOLBAR_CANCEL');
	}
}