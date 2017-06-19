<?php
/** 
 * @package     JCCDev
 * @subpackage  Create.Component
 * 
 * @copyright  	Copyright (C) 2017, Galicea. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("component", JCCDevCREATE);

class JccCreate extends JCCDevCreateComponent {
	
  protected function initialize() 	{
 
     $ret=parent::initialize();
  
     $this->table = $this->getModel('table')->getItem($this->config['item_id']);
     $this->component = $this->getModel('component')->getItem($this->table->component);
     $this->fields = $this->getModel('fields')->getTableFields($this->table->id);

		$this->template->addPlaceHolders(
			array(
			'field_list' 	=> $this->twig_field_list(),
      'header' => self::$templateHeader,      
      'component' => $this->component->name,      
      'mainfield' => (isset($this->fields[0])) ? $this->fields[0]->name : $this->table->pk,      
      'table'  => $this->table->name,      
      'table_db'  => $this->table->dbname,      
      'plural'  => $this->table->plural,      
      'pk'      => $this->table->pk,      
      'singular'  => $this->table->singular      
      ), true );
     return $ret;
	}  
	
	private function twig_field_list() {
    $buffer = '';
    if (isset($this->template)) {
    	foreach ($this->fields as $field)	{
	  		$this->template->addPlaceholders( array('field' => $field->name), true );
		  	$buffer .= '{{ '.$this->template->getBuffer().' }}';
		  }
		}
		return $buffer;
  }
  

}
