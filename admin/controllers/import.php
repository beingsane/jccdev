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
 * JCCDev Import Controller
 *
 * @package     JCCDev
 * @subpackage  Controllers
 */
class JCCDevControllerImport extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = '', $prefix='JCCDevModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Import component from xml file
	 */
	public function componentFromManifest()
	{		
		JCCDevLoader::import("install");
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=import&active=component", false));
		
		$files = $this->input->files->get("jform", array(), "files");
		$manifest = JFile::upload($files["manifest"]["tmp_name"], JCCDevINSTALL . "/import_component.xml", false);
		$xml = new SimpleXMLElement(JCCDevINSTALL . "/import_component.xml", null, true);
		
		$model = $this->getModel("ImportXml");
		if (false === $component = $model->getComponent($xml))
		{
			$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_COMPONENT_ERROR") . ": " . JText::_("COM_JCCDEV_IMPORT_ERROR_NO_MANIFEST_FILE"), "error");
			JCCDevInstall::cleanInstallDir();
			return;
		}
		
		$model = $this->getModel("Component");
		$model->save($component);
		
		JCCDevInstall::cleanInstallDir();
		$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_COMPONENT_SUCCESS"));
	}
	
	/**
	 * Import fields from xml file
	 */
	public function fieldsFromForm()
	{		
		JCCDevLoader::import("install");
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=import&active=fields", false));
		$jform = $this->input->post->get("jform", array(), "array");

		if (empty($jform["table"]))
		{
			$this->setMessage(JText::_("COM_JCCDEV_IMPORT_ERROR_NO_TABLE_GIVEN"), "error");
			return;
		}

		// Get data
		$files = $this->input->files->get("jform", array(), "files");
		$manifest = JFile::upload($files["formfile"]["tmp_name"], JCCDevINSTALL . "/import_fields.xml", false);
		$xml = new SimpleXMLElement(JCCDevINSTALL . "/import_fields.xml", null, true);

		// Get fields from xml file
		$model = $this->getModel("ImportXml");
		if (false === $fields = $model->getFields($xml, $jform["table"]))
		{
			$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_FIELDS_ERROR") . ": " . JText::_("COM_JCCDEV_IMPORT_ERROR_NO_FORM_FILE"), "error");
			JCCDevInstall::cleanInstallDir();
			return;
		}

		// Save fields
		$model = $this->getModel("Field");
		foreach ($fields as $field)
		{
			if ($field["name"] == "id") continue;
			$model->save($field);
		}
		
		JCCDevInstall::cleanInstallDir();
		$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_FIELDS_SUCCESS"));
	}
	
	/**
	 * Import table from database
	 */
	public function tableFromDb()
	{
		$this->setRedirect(JRoute::_("index.php?option=com_jccdev&view=import&active=table", false));
		$jform = $this->input->post->get("jform", array(), "array");
		
		if (empty($jform["component"]) || empty($jform["dbtable"]))
		{
			$msg = array();
			(empty($jform["component"])) ? $msg[] = JText::_("COM_JCCDEV_IMPORT_ERROR_NO_COMPONENT_GIVEN") : null;
			(empty($jform["dbtable"])) ? $msg[] = JText::_("COM_JCCDEV_IMPORT_ERROR_NO_TABLE_GIVEN") : null;
			
			$this->setMessage(implode("<br>\n", $msg), "error");
			return;
		}

		// Get table from database
		$model = $this->getModel("ImportDb");
		if (false === $table = $model->getDbTable($jform["dbtable"], $jform["component"]))
		{
			$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_TABLE_ERROR"), "error");
			return;
		}

		// Get fields from database
		if (false === $fields = $model->getFields($jform["dbtable"], $jform["component"]))
		{
			$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_TABLE_ERROR"), "error");
			return;
		}
		(!empty($jform["table_plural"])) ? $table["plural"] = $jform["table_plural"] : null;
		(!empty($jform["table_singular"])) ? $table["singular"] = $jform["table_singular"] : null;

		// Save table
		$model = $this->getModel("Table");
		
		if (!$model->save($table))
		{
			$this->setMessage(implode("<br>", $model->getErrors()));
		}
		
		$_table = JTable::getInstance("Table", "JCCDevTable");
		$_table->load(array("name" => $table["name"]));

		// Save fields
		$model = $this->getModel("Field");
		foreach ($fields as $field)
		{
			$field["table"] = $_table->id;

			if (!$model->save($field))
			{
				$this->setMessage(implode("<br>", $model->getErrors()));
			}
		}
		
		$this->setMessage(JText::_("COM_JCCDEV_IMPORT_MESSAGE_TABLE_SUCCESS"));
	}
}