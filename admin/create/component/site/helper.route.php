<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Component
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("component", JCCDevCREATE);

/**
 * Component Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentSiteHelperRoute extends JCCDevCreateComponent
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "site.helpers.route.php";

	protected function initialize()
	{		
		$this->template->addPlaceHolders(array( 
			'items' => $this->getItemRoute()
		));
		
		return parent::initialize();
	}
	
	private function getItemRoute()
	{
		$buffer = "";
		
		foreach ($this->tables as $table)
		{
			$template = $this->loadSubtemplate('item.txt');

			$template->addPlaceholders(array(
				"component"	=> $this->component->name,
				"pk"		=> $table->pk,
				"singular"	=> $table->singular
			), true );

			$template->addAreas(array(
				"catid"		=> $table->jfields["catid"],
				"language"	=> $table->jfields["language"],
			));

			$buffer .= $template->getBuffer();
		}

		return $buffer;
	}
}