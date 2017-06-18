<?php
/**
 * @package     JCCDev
 * @subpackage  Models
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JCCDev Packages Model
 *
 * @package     JCCDev
 * @subpackage  Models
 */
class JCCDevModelPackages extends JModelList
{
	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getStoreID($id = '')
	{
		return parent::getStoreId($id);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  array  An array of data items
	 *
	 * @since   12.2
	 */
	public function getItems()
	{		
		jimport('joomla.filesystem.folder');
		$items = array();
		$files = JFolder::files(JCCDevArchive::getArchiveDir());
		$store = $this->getStoreId('getItems');
		
		foreach ($files as $file)
		{
			if (preg_match('/(^pkg_).*(.zip$)/', $file))
			{
				$item = new JObject();
				$item->set('id', $file);
				$item->set('name', $file);
				$item->set('created', date("Y M d - H:i:s", filemtime(JCCDevArchive::getArchiveDir().DS.$file)));
				$item->createDir = JCCDevArchive::getArchiveDir() . "/" . JCCDevArchive::getArchiveName("pkg_", $item->name, $item->get("version", "1.0.0"));
				$content = array();

				if (!$zip = zip_open(JCCDevArchive::getArchiveDir().DS.$file)) {
					throw new Exception("Failed to open $file");
				}
				
				while ($zip_entry = zip_read($zip)) 
				{
					if (preg_match('/.zip$/', zip_entry_name($zip_entry))) {
						$content[] = zip_entry_name($zip_entry);
					}
				}
				
				$item->set('content', implode('<br>', $content));
				$items[] = $item;
			}
		}
		
		// Add the items to the internal cache.
		$this->cache['packages'] = $items;
		return $this->cache['packages'];
	}
}