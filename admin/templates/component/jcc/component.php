<?php##{start_header}##
/**
 * @package     JCCDev
 * @subpackage  Templates.Component
 *
 * @copyright   
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;##{end_header}##

$load_jcc=JPATH_LIBRARIES . '/jcc/load.php';
if (!file_exists ( $load_jcc )) {
  print JCC_NOT_INSTALLED;
} else {
  include_once($load_jcc);
  include_once('controller.php');
  try {
    $controller = new ##Component##Controller();
    $controller->execute();
  } catch (Exception $e) {
    $controller->redirection(JURI::base(), $e->getMessage(), 'error');
  }
}