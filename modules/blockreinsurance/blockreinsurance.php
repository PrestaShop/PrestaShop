<?php
/*
* 2007-2010 PrestaShop
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
*  @author Prestashop SA <contact@prestashop.com>
*  @copyright  2007-2010 Prestashop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Blockreinsurance extends Module
{
	protected $_html = '';

	public function __construct()
	{
		$this->name = 'blockreinsurance';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Block reinsurance');
		$this->description = $this->l('Adds a block to display more infos to reassure your customers');
	}

	public function install()
	{
		// Module installation
		$res = parent::install();

		// Table creation
		$res &= Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'reinsurance` (
			`id_reinsurance` INT UNSIGNED NOT NULL,
			`filename` VARCHAR(100) NOT NULL,
			PRIMARY KEY (`id_reinsurance`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
		
		$res &= Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'reinsurance_lang` (
			`id_reinsurance` INT UNSIGNED NOT NULL,
			`id_lang` INT UNSIGNED NOT NULL,
			`text` VARCHAR(300) NOT NULL,
			PRIMARY KEY (`id_reinsurance`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
		
		$res &= $this->registerHook('header') AND $this->example();
		
		return $res;
	}

	public function uninstall()
	{
		// Drop table
		$res = Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'reinsurance`');
		$res &= Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'reinsurance_lang`');
		
		// Uninstall module
		$res &= parent::uninstall();
		
		return $res;
	}
	
	
	private function example()
	{
		$res = Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."reinsurance` VALUES
																(1, '10299ef6635307f2b4da1e04471ec981.jpg'),
																(2, 'd02eaac9390f84f26fe9c5cae966a8f7.jpg'),
																(3, 'b889e72091b006fd444f63e5de030604.jpg'),
																(4, 'a215cb7a215c6976be8b2b80f02765b0.jpg'),
																(5, '0f9d7fd7d13cfb5672ac7830be60d6a8.jpg');");
		$res &= Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."reinsurance_lang` VALUES
																(1, 1, 'Money-back guarantee'),
																(1, 2, 'SATISFAIT OU REMBOURSE'),
																(1, 3, 'Money-back guarantee'),
																(1, 4, 'Money-back guarantee'),
																(1, 5, 'Money-back guarantee'),
																(2, 1, 'Satisfied or refunded'),
																(2, 2, 'ECHANGE EN MAGASIN'),
																(2, 3, 'Satisfied or refunded'),
																(2, 4, 'Satisfied or refunded'),
																(2, 5, 'Satisfied or refunded'),
																(3, 1, 'Shipped and paid'),
																(3, 2, 'PAIEMENT A L''EXPEDITION'),
																(3, 3, 'Shipped and paid'),
																(3, 4, 'Shipped and paid'),
																(3, 5, 'Shipped and paid'),
																(4, 1, 'Free delivery'),
																(4, 2, 'LIVRAISON GRATUITE'),
																(4, 3, 'Free delivery'),
																(4, 4, 'Free delivery'),
																(4, 5, 'Free delivery'),
																(5, 1, 'Secure payment'),
																(5, 2, 'PAIEMENT 100% SECURISE'),
																(5, 3, 'Secure payment'),
																(5, 4, 'Secure payment'),
																(5, 5, 'Secure payment');");
		return $res;
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'.</h2>';

		if (Tools::isSubmit('submitBlock'))
			$this->postProcess();
		$this->displayForm();

		return $this->_html;
	}

	protected function displayForm()
	{	
		$languages = Language::getLanguages(false);
		$default_language = Configuration::get('PS_LANG_DEFAULT');
		$div_id_language = 'block_1_language¤block_2_language¤block_3_language¤block_4_language¤block_5_language';
		
		$data = $this->getAllReinsurances();
		$reinssuarances = array();
		foreach ($data as $row)
			$reinssuarances[$row['id_reinsurance']][$row['id_lang']] = $row;

		$this->_html .= '
		<script type="text/javascript">id_language = Number('.$default_language.');</script>
		<form method="POST" action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<fieldset class="width2">
				<legend>'.$this->l('Block configuration').'</legend>
		';
		
		for ($i = 1; $i != 6; $i++)
		{
			$this->_html .= '
					<h3>Block '.$i.'</h3>
			';
			if ($i == 1)
				$this->_html .= '<p style="color: red;">'.$this->l('This block is required').'</p>';
			
			if (isset($reinssuarances[$i][$default_language]) && $reinssuarances[$i][$default_language]['filename'] != null)
				$this->_html .= '<div class="margin-form">
					<img src="'.$this->_path.'/img/'.$reinssuarances[$i][$default_language]['filename'].'" alt="'.$reinssuarances[$i][$default_language]['text'].'" />
				</div>';
			$this->_html .= '
					<label>'.$this->l('Image:').'</label>
					<div class="margin-form">
						<input type="file" name="images['.$i.']" />
					</div>
			';
			
			foreach ($languages as $lang)
				$this->_html .= '
					<div id="block_'.$i.'_language_'.$lang['id_lang'].'" style="display: '.($lang['id_lang'] == $default_language ? 'block' : 'none').'; float:left;">
						<label>'.$this->l('Text:').'</label>
						<input type="text" name="texts['.$i.']['.$lang['id_lang'].']" value="'.((isset($reinssuarances[$i][$lang['id_lang']]) && $reinssuarances[$i][$lang['id_lang']]['text']) ? Tools::htmlentitiesUTF8($reinssuarances[$i][$lang['id_lang']]['text']) : 'default').'" />
					</div>
				';
			$this->_html .= $this->displayFlags($languages, $default_language, $div_id_language, 'block_'.$i.'_language', true);
			$this->_html .= '
			<div class="clear"></div>
			';
			if ($i != 5)
				$this->_html.= '<hr />';
		}
		$this->_html .= '
				<p class="center">
					<input type="submit" class="button" name="submitBlock" value="'.$this->l('Save').'" />
				</p>
			</fieldset>
		</form>
		';

		return $this->_html;
	}

	protected function postProcess()
	{
		$languages = Language::getLanguages(false);
		$default_language = Configuration::get('PS_LANG_DEFAULT');
		$max_image_size = 2 * 1024 * 1024; // 2 Mb

		$errors = array();
		$res = 1;
		for ($i = 1; $i < 6; $i++)
		{
				$image_name = $_FILES['images']['name'][$i];
				if ($image_name != null)
				{
					$new_image_name = md5($image_name).'.jpg';
					$file = array();
					$file['name'] = $_FILES['images']['name'][$i];
					$file['tmp_name'] = $_FILES['images']['tmp_name'][$i];
					$file['type'] = $_FILES['images']['type'][$i];
					$file['error'] = $_FILES['images']['error'][$i];
					$file['size'] = $_FILES['images']['size'][$i];
					if ($error = checkImage($file, $max_image_size))
						$errors[] = $error;
					elseif (!move_uploaded_file($file['tmp_name'], dirname(__FILE__).'/img/'.$new_image_name))
						$errors[] = $this->l('An error occurred during the image upload.');
						
					if (!sizeof($errors))
					{	
						// Clear old rows
						$res &= $this->cleanDb($i);
						
						// New rows
						$res &= Db::getInstance()->Execute('
							INSERT INTO `'._DB_PREFIX_.'reinsurance` (`id_reinsurance`, `filename`)
							VALUES (\''.(int)$i.'\', \''.(isset($new_image_name) ? pSQL($new_image_name) : '').'\')
						');
					}
			}
		}
		
		foreach ($_POST['texts'] as $key => $text)
		{
			$res &= $this->cleanTxt($key);
			if ($text[$default_language] != null && Validate::isCleanHtml($text[$default_language]))
			{	
				if (!sizeof($errors))
				{
					foreach ($languages as $lang)
					{
						if ($text[$lang['id_lang']] == '' || !Validate::isCleanHtml($text[$lang['id_lang']]))
							$text[$lang['id_lang']] = $text[$default_language];
						
						$res &= Db::getInstance()->Execute('
							INSERT INTO `'._DB_PREFIX_.'reinsurance_lang` (`id_reinsurance`, `id_lang`, `text`)
							VALUES (\''.(int)$key.'\', \''.(int)$lang['id_lang'].'\', \''.pSQL($text[$lang['id_lang']]).'\')
						');
					}
					
					if (!$res)
						$errors[] = $this->l('An error occured on save');
				}
			}
			else
			{
				if ($key == 1)
					$errors[] = $this->l('The block 1 is required');
				else
					// check if another language aren't empty
					foreach ($text as $id_lang => $val)
						if ($id_lang != $default_language)
							if ($val != null)
								$errors[] = $this->l('The text for the block number').' '.$key.' '.$this->l('is incorrect, the default language information is required ');
			}
		}
		
		if (!sizeof($errors))
			$this->_html .= $this->displayConfirmation($this->l('Configuration updated'));
		else
			$this->_html .= $this->displayError(implode('<br />', $errors));
	}

	protected function cleanDb($key)
	{
		$image = Db::getInstance()->ExecuteS('
		SELECT `filename` 
		FROM `'._DB_PREFIX_.'reinsurance` 
		WHERE `id_reinsurance` = '.(int)$key);
		
		// Delete image
		if (file_exists(dirname(__FILE__).'/images/'.$image))
			@unlink(dirname(__FILE__).'/images/'.$image);
		
		// Delete rows
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'reinsurance` WHERE `id_reinsurance` = '.(int)$key) &&
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'reinsurance_lang` WHERE `id_reinsurance` = '.(int)$key);
	}
	
	protected function cleanTxt($key)
	{
		// Delete rows
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'reinsurance_lang` WHERE `id_reinsurance` = '.(int)$key);
	}
	
	protected function getAllReinsurances($id_lang = null)
	{
		return Db::getInstance()->ExecuteS('
			SELECT * 
			FROM `'._DB_PREFIX_.'reinsurance` r 
			LEFT JOIN `'._DB_PREFIX_.'reinsurance_lang` rl ON (r.`id_reinsurance` = rl.`id_reinsurance`)
			'.($id_lang ? 'WHERE rl.`id_lang` = '.(int)$id_lang : ''));
	}

	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'style.css', 'all');
	}
	
	public function hookFooter($params)
	{
		global $smarty, $cookie;

		$reinssuarances = $this->getAllReinsurances($cookie->id_lang);

		if (!$nb_reinssurance = sizeof($reinssuarances))
			return;

		$smarty->assign(array(
			'nb_blocks' => $nb_reinssurance,
			'reinssurances' => $reinssuarances
		));
		
		return $this->display(__FILE__, 'blockreinsurance.tpl');
	}
}

