<?php
/**
 * @package     JCCDev
 * @subpackage  Models
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Table Model
 *
 * @package     JCCDev
 * @subpackage  Models
 */
class JCCDevModelTable extends JModelAdmin
{
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		// Delete fields which belong to the table
		if ( (int) JComponentHelper::getParams('com_jccdev')->get('delete_fields') );
		{
			$model = JModelLegacy::getInstance('Field', 'JCCDevModel');
			
			foreach ($pks as $pk)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$db->setQuery($query->select('f.id')->from('#__jccdev_fields AS f')->where('f.table = ' . $db->quote((int) $pk)));
				$ids = $db->loadRowList();
				$keys = array();
				foreach ($ids as $id) $keys[] = $id[0];
				$model->delete($keys);
				
				if (JFile::exists(JCCDARCHIVE . "/tables/table_$pk.xml"))
				 JFile::delete(JCCDARCHIVE . "/tables/table_$pk.xml");
			}
		}
		
		return parent::delete($pks);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($type = 'Table', $prefix = 'JCCDevTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
		
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   12.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$options = array('control' => 'jform', 'load_data' =>$loadData);
		$form = $this->loadForm('tables', 'table', $options);
		
		if(empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   12.2
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_jccdev.edit.table.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItem($pk = null)
	{
		JCCDevLoader::import("create");

		if (false === $item = parent::getItem($pk))
		{
			return false;
		}
		
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_jccdev');
		
		if (!empty($item->component)) {
			$component = JModelLegacy::getInstance('Component', 'JCCDevModel')->getItem($item->component);
			
			// Only add component name to table name if it is not added already
			if (!preg_match("/^". $component->name ."/", $item->name)) {
				$item->dbname = $params->get('add_component_name_to_table_name') ? $component->name . '_' . $item->name : $item->name;
			}
			else {
				$item->dbname = $item->name;
			}
		}
		else {
			$item->dbname = $item->name;
		}
				
		$db->setQuery("SELECT f.name FROM #__jccdev_fields AS f WHERE f.table = " . $db->quote($item->id) . " ORDER BY f.ordering");
		$item->mainfield = $db->loadResult();

		$registry = new JRegistry();
		$registry->loadString($item->jfields);
		$item->jfields = $registry->toArray();
		
		return $item;
	}

	/**
	 * Check if table exists in database
	 * 
	 * @param unknown $pk	id of table
	 * 
	 * @return	true if table exists, false otherwise
	 */
	public function isInstalled($pk)
	{
		$db = JFactory::getDbo();
		$list = $db->getTableList();

		if (in_array($db->getPrefix() . $this->getItem($pk)->dbname, $list))
			return true;
		
		return false;
	}
	
	/**
	 * Compare JCCDev table to database table and get columns that don't exist in JCCDev table
	 * 
	 * @param	int				$id			JCCDev table id
	 * @param	string			$dbtable	Name of the table in the database
	 * @param	array<string>	$ignore		Ignore fields with this name
	 *
	 * @return	array<object>	Columns that don't exist in the JCCDev table 	
	 */
	public function compareToDatabaseTable($id, $dbtable, $ignore = array())
	{
		$db = JFactory::getDbo();
		$columns = $db->getTableColumns($db->getPrefix() . $dbtable, false);
		$table = $this->getItem($id);
		$fields = JModelLegacy::getInstance("Fields", "JCCDevModel")->getTableFields($id);
		$add = array();

		foreach ($columns as $column) {
			$match = false;

			// Look if there is a field with similar name in the table's fields
			foreach ($fields as $field) {			
				// Exclude relation columns from search
				if (isset($table->params["relations"])) {
					foreach ($table->params["relations"] as $relation) {
						$relation = $this->getItem($relation)->singular;
						if (strtolower($column->Field) == strtolower($relation)) {
							$match = true;
							break;
						}
					}
				}

				// Exclude ignored columns from search
				if (strtolower($column->Field) == strtolower($field->name) || in_array($column->Field, $ignore)) {
					$match = true;
					break;
				}
			}
			
			// Column hasn't been found in the table's fields
			if (!$match)
				$add[] = $column;
		}
		
		return $add;
	}
	
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{		
		// Set some needed variables.
		$this->user = JFactory::getUser();
		$this->table = $this->getTable();
		$this->tableClassName = get_class($this->table);
		$this->contentType = new JUcmType;
		$this->type = $this->contentType->getTypeByTable($this->tableClassName);
		$this->batchSet = true;

		foreach ($commands as $field => $value)
		{
			if ($value != "")
			{
				if (!$this->batchCustom($field, $value, $pks, $contexts))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Batch changes for a group of rows.
	 *
	 * @param   string  $field     The field.
	 * @param   string  $value     The new value for field site.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   11.3
	 */
	protected function batchCustom($field, $value, $pks, $contexts)
	{
		if (!$this->batchSet)
		{
			// Set some needed variables.
			$this->user = JFactory::getUser();
			$this->table = $this->getTable();
			$this->tableClassName = get_class($this->table);
			$this->contentType = new JUcmType;
			$this->type = $this->contentType->getTypeByTable($this->tableClassName);
			$this->batchSet = true;
		}

		foreach ($pks as $pk)
		{
			if ($this->user->authorise('core.edit', 'com_jccdev'))
			{				
				$this->table->reset();
				$this->table->load($pk);

				if (property_exists($this->table, $field))
				{
					$this->table->set($field, $value);
				}
				else
				{
					$registry = new JRegistry();
					$registry->loadString($this->table->params);
					$registry->set($field, $value);
					$this->table->set('params', $registry->toString());
				}

				static::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

				if (!$this->table->store())
				{
					$this->setError($this->table->getError());
					return false;
				}
			}
			else
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
}