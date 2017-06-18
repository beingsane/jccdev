<?php
/**
 * @package     JCCDev
 * @subpackage  Views
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JCCDevLoader::import("create");

$input = JFactory::getApplication()->input;
$id = $input->get("id", 0, "int");

if ($id)
{
	echo JHtml::_("code.form", JCCDevCreate::getInstance("table.admin.form.singular", array("item_id" => $id))->getBuffer());
}