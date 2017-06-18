<?php
/**
 * JCCDev execute script
 *
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'import.php';

JPluginHelper::importPlugin('jccdev');

JLoader::register('JCCDevHelper', __DIR__ . '/helpers/jccdev.php');
$controller	= JControllerLegacy::getInstance('JCCDev');

// Check access rights
if (!JFactory::getUser()->authorise('core.manage', 'com_jccdev'))
{
	$controller->setRedirect(JRoute::_('index.php', false), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
}

// Execute task
try {
	$controller->execute(JFactory::getApplication()->input->get('task'));
} catch (Exception $e) {
	$controller->setRedirect(JRoute::_('index.php?option=com_jccdev&view=components', false), $e->getMessage(), 'error');
}

$controller->redirect();