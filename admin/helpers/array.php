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
 * JCCDev Array Helper
 *
 * @package     JCCDev
 * @subpackage  Helpers
 */
class JCCDevHelperArray
{
	public static function toXml($array)
	{
		$str = "";
		
		foreach ($array as $key => $value)
		{
			if (is_int($key)) $key = $value;
			$value = (is_array($value)) ? self::toXml($value) : $value;
			$str .= "<" . $key . ">" . $value . "</" . $key . ">";
		}
		
		return $str;
	}
}