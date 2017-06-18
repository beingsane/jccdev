<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Table
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("table", JCCDevCREATE);;

/**
 * Table Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Table
 */
class JCCDevCreateTableSiteTmplSingularDefault extends JCCDevCreateTable
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "site.views.singular.tmpl.default.php";

	/**
	 * Check whether file should be created or not
	 *
	 * @return	boolean
	 */
	protected function condition()
	{
		return $this->table->params['frontend_details'];
	}
	
	protected function initialize()
	{
		$this->template->addPlaceHolders(
			array(
			'table_body' => $this->tableBody()
			)
		);
		
		return parent::initialize();
	}
	
	private function tableBody()
	{
		$template = $this->loadSubtemplate('tablebody.txt');
		$buffer = '';
		
		foreach ($this->fields as $field)
		{
			if ( ! (int) $field->get('frontend_item', 0) ) continue;
			$template->addPlaceholders( array('field' => $field->name), true );
			$buffer .= $template->getBuffer();
		}
		
		return (!empty($buffer)) ? $buffer : '';
	}
}