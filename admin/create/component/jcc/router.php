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
 * Component Create Class
 *
 * @package     JCCDev
 * @subpackage  Create.Component
 */
class JCCDevCreateComponentJccRouter extends JCCDevCreateComponent {		
  /**
   * The template file
   *
   * @var	string
   */
  protected $templateFile = "jcc.router.php";
}