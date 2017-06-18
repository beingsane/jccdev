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
 * JCCDev Formrule Table
 *
 * @package     JCCDev
 * @subpackage  Tables
 */
class JCCDevTableFormrule extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__jccdev_formrules', 'id', $db);
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