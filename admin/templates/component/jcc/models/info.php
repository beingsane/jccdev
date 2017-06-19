<?php
defined("_JEXEC") or die("Restricted access");


class ##Component##ModelInfo extends JModelAdmin {
  
  public function getItem($pk = null) {
    if (!isset($pk)) {
       $this->_item = array();
       return [];
    } else {
      if (!isset($this->_item)) {
        $this->_item = array();
      }
      if (!isset($this->_item[$pk])) {
        try {
          $db = $this->getDbo();
          $query = $db->getQuery(true)
                          ->select('a.*')
                          ->from('#__##singular## AS a');
          $db->setQuery($query);
          $data = $db->loadObject();
          if (empty($data)) {
             JError::raiseError(404, JText::_('COM_##COMPONENT##_ERROR_NOT_FOUND'));
          }
          $this->_item[$pk] = $data;
        } catch (Exception $e) {
          $this->setError($e);
          $this->_item[$pk] = false;
        }
      }
      return $this->_item[$pk];
    }
  }
  
  public function getForm($data = array(), $loadData = true) {
    // empty for view only
			return false;
	}

  
  protected function canDelete($record) {
    if (!empty($record->id)) {
      if ($record->published != -2) {
        return false;
      }
      $user = JFactory::getUser();
      return $user->authorise('core.delete', $this->typeAlias . '.' . (int) $record->id);
    }
  }
  
  
  
}
?>