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
 * JCCDev Packages View
 *
 * @package     JCCDev
 * @subpackage  Views
 */
class JCCDevViewPackages extends JViewLegacy
{
	protected $items;
	
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JCCDevHelper::addSubmenu('packages');
		
		$this->addToolbar();
		$this->sidebar = JLayoutHelper::render("sidebar", array("active" => "packages"), JCCDevLAYOUTS);
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JCCDEV_PACKAGES'));
		JToolBarHelper::addNew('package.add');
		JToolBarHelper::publish('package.install', 'JTOOLBAR_INSTALL');
		JToolBarHelper::deleteList('', 'package.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::preferences('com_jccdev');
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'name' => JText::_('COM_JCCDEV_TABLE_FIELD_NAME_LABEL'),
			'component' => JText::_('COM_JCCDEV_TABLE_FIELD_COMPONENT_LABEL'),
			'id' => JText::_('COM_JCCDEV_TABLE_FIELD_ID_LABEL'),
		);
	}
}