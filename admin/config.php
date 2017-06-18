<?php
/**
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev config class.
 *
 * @package     JCCDev
 * @subpackage  JCCDev
 */
class JCCDevConfig
{	
	/**
	 * Load content from a config file and decode it
	 * 
	 * @param	string	$name	The configuration file name
	 * @return	mixed	either an array or an object
	 */
	public static function getConfig($name) {
		$file = JCCDevCONFIG . "/" . $name . ".json";
		$json = JFile::read($file);
		return json_decode($json);
	}
}