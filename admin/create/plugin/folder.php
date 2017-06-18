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
class JCCDevCreatePluginFolder extends JCCDevCreatePlugin
{		
	/**
	 * Get the template object
	 *
	 * @return	JCCDevTemplate
	 */
	protected function getTemplate()
	{
		$this->templateFile = $this->item->folder . ".php";
		return parent::getTemplate();
	}

	/**
	 * @see JCCDevCreate
	 */
	public function write($path = "")
	{
		$path = $this->createDir . "/" . $this->item->name . ".php";
		return parent::write($path);
	}
}