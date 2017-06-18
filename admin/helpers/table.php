<?php
/**
 * @package     JCCDev
 * @subpackage  Helpers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Table Helper
 *
 * @package     JCCDev
 * @subpackage  Helpers
 */
class JCCDevHelperTable
{
	/**
	 * Compare table fields to database table columns and return the missing columns in the database table
	 *
	 * @param	int		$table_id		The JCCDev table id
	 * @param	string	$table_db_name	The database table name
	 *
	 * @return	array	The missing columns in the database table
	 */
	public static function missingExtern($table_id, $table_db_name)
	{
		$db = JFactory::getDbo();
		$model = JModelLegacy::getInstance("Fields", "JCCDevModel");
		$model_field = JModelLegacy::getInstance("Field", "JCCDevModel");
		$columns = $db->getTableColumns($table_db_name, false);
		$fields = $model->getTableFields($table_id);
		$missing = array();

		foreach ($fields as $key => $field)
		{
			if ($model_field->isCoreField($field->name) || array_key_exists($field->name, $columns))
			{
				unset($fields[$key]);
			}
		}
		
		return $fields;
	}

	/**
	 * Compare table fields to database table columns and return the missing columns in the database table
	 *
	 * @param	int		$table_id		The JCCDev table id
	 * @param	string	$table_db_name	The database table name
	 *
	 * @return	array	The missing columns in the database table
	 */
	public static function missingIntern($table_id, $table_db_name)
	{
		$db = JFactory::getDbo();
		$model = JModelLegacy::getInstance("Fields", "JCCDevModel");
		$model_field = JModelLegacy::getInstance("Field", "JCCDevModel");
		$table = JModelLegacy::getInstance("Table", "JCCDevModel")->getItem($table_id);
		$jtable = JTable::getInstance("Field", "JCCDevTable");
		$columns = $db->getTableColumns($table_db_name, false);
		$fields = $model->getTableFields($table_id);
		$missing = array();

		foreach ($columns as $key => $column)
		{
			if ($jtable->load(array("name" => $column->Field, "table" => $table_id)) || $model_field->isCoreField($key) || $key = $table->pk)
			{
				unset($columns[$key]);
			}
		}

		return $columns;
	}
}