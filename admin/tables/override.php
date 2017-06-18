<?php
/**
 * @package     JCCDev
 * @subpackage  Tables
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Override Table
 *
 * @package     JCCDev
 * @subpackage  Tables
 */
class JCCDevTableOverride extends JTable
{	
	public function __construct($db)
	{
		parent::__construct('#__jccdev_overrides', 'id', $db);
	}
	
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}
}