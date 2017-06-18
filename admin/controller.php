<?php
/**
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev master controller.
 *
 * @package     JCCDev
 * @subpackage  JCCDev
 */
class JCCDevController extends JControllerLegacy
{
	/**
	 * The name of the default view
	 *
	 * @var	string
	 */
	protected $default_view = "jccdev";
	
	/**
	 * Checks whether a user can see this view.
	 *
	 * @param   string	$view	The view name.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function canView($view)
	{
		$canDo	= JCCDevHelper::getActions();
		return $canDo->get('core.admin');
	}
	
	/**
	 * Test function
	 *
	 * @note	Only important for developer of this component
	 */
	public function test()
	{
	}

	/**
	 * Fill every folder of this component with HTML file
	 *
	 * @note	Only important for developer of this component
	 */
	public function html()
	{
		require_once JCCDevLIB . "/archive.php";
		JCCDevArchive::html(JPATH_COMPONENT);
		echo "HTML files created";
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{		
		JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . "/helpers/html");
		
		$view   = $this->input->get('view', 'components');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		$doc	= JFactory::getDocument();
		
		$doc->addScript(JURI::base()."components/com_jccdev/assets/js/jccdev.js");
		$doc->addStyleSheet(JURI::base()."components/com_jccdev/assets/css/jccdev.css");

		if (!$this->canView($view))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}

		return parent::display();
	}
}