<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Template
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("template", JCCDevCREATE);

/**
 * Template Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Template
 */
class JCCDevCreateTemplateManifest extends JCCDevCreateTemplate
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "templateDetails.xml";

	protected function initialize()
	{
		$this->template->addPlaceHolders(
			array( 
			'author' 			=> $this->item->get('author'),
			'author_email' 		=> $this->item->get('author_email'),
			'author_url' 		=> $this->item->get('author_url'),
			'copyright' 		=> $this->item->get('copyright'),
			'creationdate' 		=> date("M Y"),
			'licence'	 		=> $this->item->get('licence'),
			'version'	 		=> $this->item->get('version'),
			'languages'	 		=> $this->lang()
			)
		);
		
		return parent::initialize();
	}

	private function lang()
	{
		$buffer = '';
		$tname = $this->item->name;
		
		foreach ($this->item->params['languages'] as $lang)
		{
			$buffer .= "\n\t\t<language tag=\"$lang\">$lang.tpl_$tname.ini</language>";
			$buffer .= "\n\t\t<language tag=\"$lang\">$lang.tpl_$tname.sys.ini</language>";
		}

		return $buffer;
	}
}