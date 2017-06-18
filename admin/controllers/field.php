<?php
/**
 * @package     JCCDev
 * @subpackage  Controllers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Field Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerField extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   12.2
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if ($this->input->get("component", 0, "int"))
		{
			$this->view_list = "component";
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Field', 'JCCDevModel');

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=fields', false));

		return parent::batch($model);
	}
	
	/**
	 * Export field to database
	 */
	public function install()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$db = JFactory::getDbo();
		$model = JModelLegacy::getInstance("Field", "JCCDevModel");
		$user = JFactory::getUser();
		
		// Check if action is allowed
		if (!$user->authorise('components.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		$id = $input->get("id", 0, "int");
		$item = $model->getItem($id);
		$model->exportToDatabase($item);
		
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=component", false));
		$this->setMessage("COM_JCCDEV_FIELD_MESSAGE_FIELD_EXPORTED");
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId = null, $urlVar = 'id');
		$append .= "&id=" . $this->input->get("id", 0, "int");
		
		$component = $this->input->get('component', 0, 'int');
		if ($component)
		{
			$append .= "&component=" . $component;
		}
		
		$table = $this->input->get('table', 0, 'int');
		if ($table)
		{
			$append .= "&table=" . $table;
		}
		
		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();

		$component = $this->input->get('component', 0, 'int');
		if ($component)
		{
			$append .= "&id=" . $component;
		}
		
		$table = $this->input->get('table', 0, 'int');
		if ($table)
		{
			$append .= "&table=" . $table;
		}
		
		return $append;
	}		
}