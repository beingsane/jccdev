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
 * JCCDev Table Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerTable extends JControllerForm
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
		$model = $this->getModel('Table', 'JCCDevModel');

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jccdev&view=tables', false));

		return parent::batch($model);
	}
	
	/**
	 * Import table from dtabase
	 * 
	 * @TODO
	 */
	public function importFromDatabase()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$db = JFactory::getDbo();
		
		$component = $input->get("component", 0, "int");
		$name = $input->get("table", "", "string");
		$tablelist = $db->getTableList();
		
		// Check for valid data
		if (empty($component)) {
			$this->setMessage("COM_JCCDEV_TABLE_MESAGE_NO_COMPONENT_ID_GIVEN", "error");
			return;
		}
		elseif (empty($name)) {
			$this->setMessage("COM_JCCDEV_TABLE_MESAGE_NO_TABLE_NAME_GIVEN", "error");
			return;
		}
		elseif (!in_array($name, $arraylist)) {
			$this->setMessage("COM_JCCDEV_TABLE_MESAGE_TABLE_DOES_NOT_EXIST", "error");
			return;
		}		
	}

	/**
	 * Import field from database
	 */
	public function importFieldFromDatabase()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$table_id = $input->get("id", 0, "int");
		$column_name = $input->get("column", "", "string");
		$table = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($table_id);
		
		$model_field = JModelLegacy::getInstance("Field", "JCCDevModel");
		$model_import = JModelLegacy::getInstance("ImportDb", "JCCDevModel");
		
		$fields = $model_import->getFields($table->dbname, $table->id);
		
		foreach ($fields as $field) {
			if ($field["name"] == $column_name) {
				$model_field->save($field);
				$this->setMessage(JText::_("COM_JCCDEV_TABLE_MESSAGE_FIELD_IMPORTED"));
				$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=component&id=" . $table->component . "&active=tables." . $table->id, false));
				return;
			}
		}
		
		$this->setMessage(JText::_("COM_JCCDEV_TABLE_MESSAGE_FIELD_NOT_FOUND"), "error");
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=cmponent&id=" . $table->component . "&active=tables." . $table->id, false));
	}
	
	/**
	 * Install table
	 */
	public function install()
	{
		$user = JFactory::getUser();
				
		// Check if action is allowed
		if (!$user->authorise('components.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Get table data
		$app = JFactory::getApplication();
		$id = $app->input->get('id', 0, 'int');
		
		$model = JModelLegacy::getInstance("Table", "JCCDevModel");
		$item = $model->getItem($id);
		$component = JModelLegacy::getInstance("Component", "JCCDevModel")->getItem($item->component);
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=component&id=" . $component->id, false));

		// If component is installed, install table
		if (JCCDevInstall::isInstalled('component', 'com_' . $component->name))
		{
			// If table is already installed, the procedure is finished here
			if ($model->isInstalled($item->id))
				return;
			
			// Get SQL code of table and send SQL install query to database
			$sql = JCCDevCreate::getInstance("table.admin.sql", array("item_id" => $item->id))->getBuffer();
			
			$db = JFactory::getDbo();
			$db->setQuery($sql)->execute();
			
			$this->setMessage("COM_JCCDEV_TABLE_MESSAGE_TABLE_INSTALLED", "error");
		}
		else
		{
			$this->setMessage("COM_JCCDEV_TABLE_MESSAGE_COMPONENT_NOT_INSTALLED", "error");
		}
	}
	
	/**
	 * Uninstall table
	 */
	public function uninstall()
	{
		$user = JFactory::getUser();
				
		// Check if action is allowed
		if (!$user->authorise('components.install', 'com_jccdev'))
		{
			$this->setMessage(JText::_('COM_JCCDEV_ACTION_NOT_ALLOWED'), 'warning');
			return;
		}
		
		// Get table data
		$app = JFactory::getApplication();
		$id = $app->input->get('id', 0, 'int');
		$model = JModelLegacy::getInstance("Table", "JCCDevModel");
		$item = $model->getItem($id);
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=component&id=" . $item->component, false));

		// If table is not installed, the procedure is finished here
		if (!$model->isInstalled($item->id))
			return;
		
		$db = JFactory::getDbo();
		$sql = "DROP TABLE " . $db->getPrefix() . $item->dbname;
		$db->setQuery($sql)->execute();

		$this->setMessage("COM_JCCDEV_TABLE_MESSAGE_TABLE_UNINSTALLED");
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
		
		$id = $this->input->get('id', 0, 'int');
		if ($id)
		{
			$append .= "&id=" . $id;
		}
		
		$component = $this->input->get('component', 0, 'int');
		if ($component)
		{
			$append .= "&component=" . $component;
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
		
		return $append;
	}		
}