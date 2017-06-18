<?php
/**
 * @package     JCCDev
 * @subpackage  Library
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Exception
 *
 * @package     JCCDev
 * @subpackage  Library
 */
class JCCDevException extends Exception
{
	private $redirect = null;
	private $type = 'error';
	
	public function __construct($message)
	{
		$message .= "<br><i>" . $this->getFile() . " (line " . $this->getLine() . ")</i>";
		
		if (empty($this->redirect))
		{
			$this->set('redirect', JRoute::_('index.php?option=com_jccdev'));
		}
		
		parent::__construct($message);
    }
	
	public function get($property)
	{
		if (property_exists($this, $property))
		{
			return $this->$property;
		}
		
		return null;
	}
	
	public function set($property, $value = null)
	{
		if (property_exists($this, $property))
		{
			$this->$property = $value;
		}
		
		return $this;
	}
}