<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once("../classes/Validate.php");

class LanguageManager
{
	private $url_xml;
	private $lang;
	private $xml_file;
	
	function __construct ($url_xml)
	{
		$this->loadXML($url_xml);
		$this->setLanguage();
		$this->getIncludeTradFilename();
	}

	private function loadXML($url_xml)
	{
		global $errors;
		if (!$this->xml_file = simplexml_load_file($url_xml))
			$errors = 'Error when loading XML language file : '.$url_xml;
	}
	
	public function getIdSelectedLang()
	{
		return $this->lang['id'];
	}
	
	public function getIsoCodeSelectedLang()
	{
		return $this->lang->idLangPS;
	}
	
	public function countLangs()
	{
		return sizeof($this->xml_file);
	}
	
	public function getAvailableLangs()
	{
		return $this->xml_file;
	}
	
	public function getSelectedLang()
	{
		return $this->lang;
	}
	
	private function getIdByHAL(){
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$FirstHAL = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$iso = $FirstHAL[0];
			if ($iso != "en-us")
				foreach ($this->xml_file as $lang)
					foreach ($lang->isos->iso as $anIso)
						if ($anIso == $iso)
							return $lang['id'];
		}
		else
			return 0;
		
	}
	
	private function setLanguage()
	{
		if( isset($_GET['language']) AND Validate::isInt($_GET['language']))
			$id_lang = (int)($_GET['language']);
		if (!isset($id_lang))
			$id_lang = ($this->getIdByHAL());
		$this->lang = $this->xml_file->lang[(int)($id_lang)];
	}
	
	public function getIncludeTradFilename()
	{
		return ($this->lang == NULL) ? false : dirname(__FILE__).$this->lang['trad_file'];
	}
}