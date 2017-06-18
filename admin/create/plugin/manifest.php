<?php
/**
 * @package     JCCDev
 * @subpackage  Create.Plugin
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("plugin", JCCDevCREATE);

/**
 * Plugin Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Plugin
 */
class JCCDevCreatePluginManifest extends JCCDevCreatePlugin
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "manifest.xml";

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
			'languages'	 		=> $this->lang(),
			'version'	 		=> $this->item->get('version'),
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
			$buffer .= "\n\t\t<language tag=\"$lang\">$lang." . $this->type . ".ini</language>";
			$buffer .= "\n\t\t<language tag=\"$lang\">$lang." . $this->type . ".sys.ini</language>";
		}

		return $buffer;
	}
}