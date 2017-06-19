<?php
/**
 * @author		
 * @copyright	
 * @license		
 */

defined("_JEXEC") or die("Restricted access");

/**
 * List Model for ##plural##.
 *
 * @package     ##Component##
 * @subpackage  Models
 */
class ##Component##ModelList extends JModelList
{

  protected function getListQuery() {
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    // Select some fields
    $query->select('*');
    $query->from('#__##singular##');
    return $query;
  }

}
?>