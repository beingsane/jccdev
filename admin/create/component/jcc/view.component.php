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

/**
 * Table Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentJccViewComponent extends JCCDevCreateComponent {
  /**
   * The template file
   *
   * @var	string
   */
  protected $templateFile = "jcc.views.component.view.html.php";

  /**
   * Check whether file should be created or not
   *
   * @return	boolean
   */
  protected function condition() {
    return empty($this->tables);
  }
}