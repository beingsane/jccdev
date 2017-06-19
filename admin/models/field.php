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
 * JCCDev Field Model
 *
 * @package     JCCDev
 * @subpackage  Models
 */
class JCCDevModelField extends JModelAdmin
{
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
	public function getTable($type = 'Field', $prefix = 'JCCDevTable', $config = array())
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
		$options = array('control' => 'jform', 'load_data' => $loadData);
		$form = $this->loadForm('field', 'field', $options);
		
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
		$data = $app->getUserState('com_jccdev.edit.field.data', array());
		
		if(empty($data)) {
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
		$item = parent::getItem($pk);
		$db = JFactory::getDbo();
		$model_table = JModelLegacy::getInstance("Table", "JCCDevModel");

		if ($item->table)
		{
			$table = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($item->table);
			$item->component_name = "com_" . JModelLegacy::getInstance("Component", "JCCDevModel")->getItem($table->component)->name;
		}
		
		// Exists field in database?
		if ($model_table->isInstalled($item->table))
			$item->isInstalled = $this->dbColumnExists($item, "", true);
		else
			$item->isInstalled = false;

		// Add formfield id
		$query = $db->getQuery(true)->select("id")->from("#__jccdev_formfields as ff")->where("ff.name = " . $db->quote($item->type));
		$item->formfield_id = (int) $db->setQuery($query)->loadResult();
		
		// Add formrule id
		$query = $db->getQuery(true)->select("id")->from("#__jccdev_formrules as fr")->where("fr.name = " . $db->quote($item->rule));
		$item->formrule_id = (int) $db->setQuery($query)->loadResult();
		
		// Load options
		$registry = new JRegistry();
		if (!isset($item->options)) $item->options='';
  	$registry->loadString($item->options);
	  $item->options = $registry->toArray();
	  
		
		return $item;
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
	 * Batch site changes for a group of rows.
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
	
	/**
	 * Check if a database table contains a field like this
	 * 
	 * @param	object	$field		The field object
	 * @param	string	$tablename	The table in which to look for the field
	 * @param	boolean $onlyName	Only check for equal names, otherwise check also length and type
	 * 
	 * @return boolean	true if table contains field like this, false if not
	 */
	public function dbColumnExists($field, $tablename, $onlyName = false)
	{
		$db = JFactory::getDbo();
		
		if ($tablename == "") {
			$tablename = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($field->table)->dbname;
		}
		
		$db->setQuery("SHOW COLUMNS FROM " . $db->getPrefix() . $tablename);
		$columns = $db->loadAssocList();
		
		foreach ($columns as $column)
			if ($column["Field"] == $field->name)
				return true;
		
		return false;	
	}
	
	/**
	 * Export field to database
	 * 
	 * @param object	$field
	 * @param string	$tablename
	 */
	public function exportToDatabase($field, $tablename = "")
	{
		$db = JFactory::getDbo();
		
		if ($tablename == "") {
			$tablename = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($field->table)->dbname;
		}
		
		$table = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($field->table);
		$sql = "ALTER TABLE " . $db->getPrefix() . $table->dbname . " ADD " . $this->toSQL($field);
		
		$db->setQuery($sql)->execute();
	}
	
	/**
	 * Import table column from database
	 * 
	 * @param string	$tablename		The name of the table where to look for the column
	 * @param string	$columnname		The column's name
	 * 
	 * @return	mixed	column object on success, false if not found
	 */
	public function getColumnFromDatabase($tablename, $columnname)
	{
		$db = JFactory::getDbo();
		$columns = $db->getTableColumns($db->getPrefix() . $tablename, false);
				
		foreach ($columns as $column) {
			if ($column->Field == $columnname) {
				return $column;
			}
		}
		
		return false;
	}
	
	/**
	 * Get field SQL syntax
	 *
	 * @param	object	The item
	 *
	 * @return	string	The field SQL syntax
	 */
	public function toSQL($item)
	{
		$sql = "`" . $item->name . "` " . $item->dbtype;
		$sql .= (preg_match('/BINARY|INT|CHAR|DECIMAL|NUMERIC/i', $item->dbtype)) ? "(" . $item->maxlength . ")" : "";
		$sql .= " NOT NULL";
		$sql .= (!empty($item->default)) ? " DEFAULT '" . $item->default . "'" : "";				
		$sql .= (!empty($item->description)) ? " COMMENT '" . $item->description . "'" : "";
		
		return $sql;
	}

	/**
	 * Is this field name a joomla core field name?
	 *
	 * @param	string	$name	The field name
	 *
	 * @return	boolean	True if it is a joomla core fied, false if not
	 */
	public function isCoreField($name)
	{
		return in_array($name, array('access', 'alias', 'asset_id', 'catid', 'checked_out', 'checked_out_time', 'created', 'created_by', 'created_by_alias', 'hits',
			'images', 'language', 'metadata', 'metadesc', 'metakey', 'modified', 'modified_by', 'ordering', 'params', 'publish_up', 'publish_down', 'published', 'version'));
	}
}