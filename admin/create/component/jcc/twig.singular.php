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


include_once(__DIR__.'/../JccCreate.php');
class JCCDevCreateComponentJccTwigSingular extends JccCreate {
	
  protected $templateFile="jcc.twig.view.twig";

	public function create() {
		if ($this->condition()) {
      $writer=$this->initialize(); // ????????????
      $this->templateFile ="jcc.twig.view.twig";
      $this->filePath=$this->templateFile;
      if (!$writer->write()) {
				$this->setError($this->_name . " : Could not create file: ".$this->filePath);
				return false;
      }
			if ($this->getLangKeys) {
				$this->addLanguageKeys($this->template->getLanguageKeys(array("COM_" . strtoupper($this->component->name) . "_[A-Z0-9_]*")));
			}
		}
		return true;
	}
	
}
