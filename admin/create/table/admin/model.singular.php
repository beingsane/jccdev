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
class JCCDevCreateTableAdminModelSingular extends JCCDevCreateTable
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "admin.models.singular.php";

	protected function initialize()
	{
		$this->template->addPlaceHolders(array(
			'getItem' => $this->_getItem()
		));

		return parent::initialize();
	}

	private function _getItem()
	{		
		$template = $this->loadSubtemplate('getitem_default.txt');
		$template->addAreas($this->getDefaultAreas());
		$template->addPlaceholders($this->getDefaultPlaceholders());
		return $template->getBuffer();
	}
}