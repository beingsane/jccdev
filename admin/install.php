<?php
/**
 * JCCDev install script
 *
 * @package     JCCDev
 * @subpackage  JCCDev
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die('Restricted access');
jimport("joomla.filesystem.file");
jimport("joomla.filesystem.folder");

class com_jccdevInstallerScript
{
    /**
	 * Extension type
	 */
	protected $extension = 'com_jccdev';

	/**
	 * Custom install method for JCCDev
	 *
	 * @param	object	$parent		The installer adapter
	 *
	 * @return  void
	 */
	public function install($parent)			
	{	
		$src = $parent->get("parent")->getPath('source');
		include($src . "/install/template_install.php");
	}

	/**
	 * Custom update method for JCCDev
	 *
	 * @param	object	$parent		The installer adapter
	 *
	 * @return  void
	 */
	public function update($parent)
	{
		// Compare directories, delete files and folders which don`t exists in the new version
		$src = $parent->get("parent")->getPath('source');
		$dest = $parent->get("parent")->getPath('extension_administrator');
		$dirs = array("assets", "controllers", "create", "helpers", "layouts", "models", "tables", "templates", "views");

		// Delete folders that don`t exists in the new version any more
		foreach ($dirs as $dir)
		{
			foreach (JFolder::folders($dest . "/" . $dir, ".", true, true) as $folder)
			{
				if (!JFolder::exists($src . "/admin" . str_replace($dest, "", $folder)))
				{
					if (JFolder::exists($folder)) JFolder::delete($folder);
				}
			}
		}

		// Delete files that don`t exists in the new version any more
		foreach ($dirs as $dir)
		{
			foreach (JFolder::files($dest . "/" . $dir, ".", true, true) as $file)
			{
				if (!JFile::exists($src . "/admin" . str_replace($dest, "", $file)) && $file != "/install.php")
				{
					if (JFile::exists($file)) JFile::delete($file);
				}
			}
		}

		foreach (JFolder::files($dest, '.', false, true) as $file)
		{
			if (!JFile::exists($src . "/admin" . str_replace($dest, "", $file)) && $file != "/install.php")
			{
				if (JFile::exists($file)) JFile::delete($file);
			}
		}

		include($src . "/install/template_update.php");
	}
}