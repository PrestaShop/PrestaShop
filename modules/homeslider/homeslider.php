<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 * @version 1.1 (2011-11-23)
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'homeslider/HomeSlide.php');

class HomeSlider extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'homeslider';
		$this->tab = 'front_office_features';
		$this->version = '1.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		$this->displayName = $this->l('Image slider for your homepage');
		$this->description = $this->l('Adds an image slider to your homepage.');
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		/* Adds Module */
		if (parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader'))
		{
			/* Sets up configuration */
			$res = Configuration::updateValue('HOMESLIDER_WIDTH', '535');
			$res &= Configuration::updateValue('HOMESLIDER_HEIGHT', '300');
			$res &= Configuration::updateValue('HOMESLIDER_SPEED', '1300');
			$res &= Configuration::updateValue('HOMESLIDER_PAUSE', '7700');
			/* Creates tables */
			$res &= $this->createTables();

			/* Adds samples */
			if ($res)
				$this->installSamples();

			return $res;
		}
		return false;
	}

	/**
	 * Adds samples
	 */
	private function installSamples()
	{
		$languages = Language::getLanguages(false);
		for ($i = 1; $i <= 5; ++$i)
		{
			$slide = new HomeSlide();
			$slide->position = $i;
			$slide->active = 1;
			foreach ($languages as $language)
			{
				$slide->title[$language['id_lang']] = 'Sample '.$i;
				$slide->description[$language['id_lang']] = 'This is a sample picture';
				$slide->legend[$language['id_lang']] = 'sample-'.$i;
				$slide->url[$language['id_lang']] = 'http://www.prestashop.com';
				$slide->image[$language['id_lang']] = 'sample-'.$i.'.jpg';
			}
			$slide->add();
		}
	}

	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall() && $this->unregisterHook('home') && $this->unregisterHook('header'))
		{
			/* Deletes tables */
			$res = $this->deleteTables();
			/* Unsets configuration */
			$res &= Configuration::deleteByName('HOMESLIDER_WIDTH');
			$res &= Configuration::deleteByName('HOMESLIDER_HEIGHT');
			$res &= Configuration::deleteByName('HOMESLIDER_SPEED');
			$res &= Configuration::deleteByName('HOMESLIDER_PAUSE');
			return $res;
		}
		return false;
	}

	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Slides */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'homeslider` (
				`id_homeslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_homeslider_slides`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'homeslider_slides` (
			  `id_homeslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_homeslider_slides`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'homeslider_slides_lang` (
			  `id_homeslider_slides` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `legend` varchar(255) NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `image` varchar(255) NOT NULL,
			  PRIMARY KEY (`id_homeslider_slides`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		$slides = $this->getSlides();
		foreach ($slides as $slide)
		{
			$to_del = new HomeSlide($slide['id_slide']);
			$to_del->delete();
		}
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'homeslider`, `'._DB_PREFIX_.'homeslider_slides`, `'._DB_PREFIX_.'homeslider_slides_lang`;
		');
	}

	public function getContent()
	{
		$this->_html .= $this->headerHTML();
		$this->_html .= '<h2>'.$this->displayName.'.</h2>';

		/* Validate & process */
		if (Tools::isSubmit('submitSlide') || Tools::isSubmit('delete_id_slide') ||
			Tools::isSubmit('submitSlider') ||
			Tools::isSubmit('changeStatus'))
		{
			if ($this->_postValidation())
				$this->_postProcess();
			$this->_displayForm();
		}
		else if (Tools::isSubmit('addSlide') || (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))))
			$this->_displayAddForm();
		else
			$this->_displayForm();

		return $this->_html;
	}

	private function _displayForm()
	{
		/* Gets Slides */
		$slides = $this->getSlides();

		/* Begin fieldset slider */
		$this->_html .= '
		<fieldset>
			<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Slider configuration').'</legend>';
		/* Begin form */
		$this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
		/* Height field */
		$this->_html .= '
			<label>'.$this->l('Height:').'</label>
			<div class="margin-form">
				<input type="text" name="HOMESLIDER_HEIGHT" id="speed" size="3" value="'.Configuration::get('HOMESLIDER_HEIGHT').'" /> px
			</div>';
		/* Width field */
		$this->_html .= '
		<label>'.$this->l('Width:').'</label>
		<div class="margin-form">
			<input type="text" name="HOMESLIDER_WIDTH" id="pause" size="3" value="'.Configuration::get('HOMESLIDER_WIDTH').'" /> px
		</div>';
		/* Speed field */
		$this->_html .= '
			<label>'.$this->l('Speed:').'</label>
			<div class="margin-form">
				<input type="text" name="HOMESLIDER_SPEED" id="speed" size="3" value="'.Configuration::get('HOMESLIDER_SPEED').'" /> ms
			</div>';
		/* Pause field */
		$this->_html .= '
		<label>'.$this->l('Pause:').'</label>
		<div class="margin-form">
			<input type="text" name="HOMESLIDER_PAUSE" id="pause" size="3" value="'.Configuration::get('HOMESLIDER_PAUSE').'" /> ms
		</div>';
		/* Save */
		$this->_html .= '
		<div class="margin-form">
			<input type="submit" class="button" name="submitSlider" value="'.$this->l('Save').'" />
		</div>';
		/* End form */
		$this->_html .= '</form>';
		/* End fieldset slider */
		$this->_html .= '</fieldset>';

		$this->_html .= '<br /><br />';

		/* Begin fieldset slides */
		$this->_html .= '
		<fieldset>
			<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Slides configuration').'</legend>
			<strong>
				<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addSlide">
					<img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('Add Slide').'
				</a>
			</strong>';

		/* Display notice if there are no slides yet */
		if (!$slides)
			$this->_html .= '<p style="margin-left: 40px;">'.$this->l('You did not add any slides yet.').'</p>';
		else /* Display slides */
		{
			$this->_html .= '
			<div id="slidesContent" style="width: 400px; margin-top: 30px;">
				<ul id="slides">';

			foreach ($slides as $slide)
			{
				$this->_html .= '
  					<li id="slides_'.$slide['id_slide'].'">
    					<strong>#'.$slide['id_slide'].'</strong> '.$slide['title'].'
    					<p style="float: right">'.
    						$this->displayStatus($slide['id_slide'], $slide['active']).'
    						<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&id_slide='.(int)($slide['id_slide']).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a>
							<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&delete_id_slide='.(int)($slide['id_slide']).'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>
						</p>
  					</li>';
			}
			$this->_html .= '</ul></div>';
		}
		// End fieldset
		$this->_html .= '</fieldset>';
	}

	private function _displayAddForm()
	{
		/* Sets Slide : depends if edited or added */
		$slide = null;
		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))
			$slide = new HomeSlide((int)Tools::getValue('id_slide'));
		/* Checks if directory is writable */
		if (!is_writable('.'))
			$this->displayWarning(sprintf($this->l('modules %s must be writable (CHMOD 755 / 777)'), $this->name));

		/* Gets languages and sets which div requires translations */
		$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$divLangName = 'image¤title¤url¤legend¤description';
		$this->_html .= '<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';

		/* Form */
		$this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="POST" enctype="multipart/form-data">';

		/* Fieldset Upload */
		$this->_html .= '
		<fieldset class="width3">
			<br />
			<legend><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" />1 - '.$this->l('Upload your slide').'</legend>';
		/* Image */
		$this->_html .= '<label>'.$this->l('Select a file:').' * </label><div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '<div id="image_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">';
			$this->_html .= '<input type="file" name="image_'.$language['id_lang'].'" id="image_'.$language['id_lang'].'" size="30" value="'.(isset($slide->image[$language['id_lang']]) ? $slide->image[$language['id_lang']] : '').'"/>';
			/* Sets image as hidden in case it does not change */
			if ($slide && $slide->image[$language['id_lang']])
				$this->_html .= '<input type="hidden" name="image_old_'.$language['id_lang'].'" value="'.($slide->image[$language['id_lang']]).'" id="image_old_'.$language['id_lang'].'" />';
			/* Display image */
			if ($slide && $slide->image[$language['id_lang']])
				$this->_html .= '<img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/images/'.$slide->image[$language['id_lang']].'" width="'.(Configuration::get('HOMESLIDER_WIDTH')/2).'" height="'.(Configuration::get('HOMESLIDER_HEIGHT')/2).'" alt=""/>';
			$this->_html .= '</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'image', true);
		/* End Fieldset Upload */
		$this->_html .= '</fieldset><br /><br />';

		/* Fieldset edit/add */
		$this->_html .= '<fieldset class="width3">';
		if (Tools::isSubmit('addSlide')) /* Configure legend */
			$this->_html .= '<legend><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> 2 - '.$this->l('Configure your slide').'</legend>';
		else if (Tools::isSubmit('id_slide')) /* Edit legend */
			$this->_html .= '<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> 2 - '.$this->l('Edit your slide').'</legend>';
		/* Sets id slide as hidden */
		if ($slide && Tools::getValue('id_slide'))
			$this->_html .= '<input type="hidden" name="id_slide" value="'.$slide->id.'" id="id_slide" />';
		/* Sets position as hidden */
		$this->_html .= '<input type="hidden" name="position" value="'.(($slide != null) ? ($slide->position) : ($this->getNextPosition())).'" id="position" />';

		/* Form content */
		/* Title */
		$this->_html .= '<br /><label>'.$this->l('Title:').' * </label><div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" id="title_'.$language['id_lang'].'" size="30" value="'.(isset($slide->title[$language['id_lang']]) ? $slide->title[$language['id_lang']] : '').'"/>
					</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
		$this->_html .= '</div><br /><br />';

		/* URL */
		$this->_html .= '<label>'.$this->l('URL:').' * </label><div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '
					<div id="url_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="url_'.$language['id_lang'].'" id="url_'.$language['id_lang'].'" size="30" value="'.(isset($slide->url[$language['id_lang']]) ? $slide->url[$language['id_lang']] : '').'"/>
					</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'url', true);
		$this->_html .= '</div><br /><br />';

		/* Legend */
		$this->_html .= '<label>'.$this->l('Legend:').' * </label><div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '
					<div id="legend_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="legend_'.$language['id_lang'].'" id="legend_'.$language['id_lang'].'" size="30" value="'.(isset($slide->legend[$language['id_lang']]) ? $slide->legend[$language['id_lang']] : '').'"/>
					</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'legend', true);
		$this->_html .= '</div><br /><br />';

		/* Description */
		$this->_html .= '
		<label>'.$this->l('Description:').' </label>
		<div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '<div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
				<textarea name="description_'.$language['id_lang'].'" rows="10" cols="29">'.(isset($slide->description[$language['id_lang']]) ? $slide->description[$language['id_lang']] : '').'</textarea>
			</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'description', true);
		$this->_html .= '</div><div class="clear"></div><br />';

		/* Active */
		$this->_html .= '
		<label for="active_on">'.$this->l('Active:').'</label>
		<div class="margin-form">
			<img src="../img/admin/enabled.gif" alt="Yes" title="Yes" />
	        <input type="radio" name="active_slide" id="active_on" '.(($slide && (isset($slide->active) && (int)$slide->active == 0)) ? '' : 'checked="checked" ').' value="1" />
		    <label class="t" for="active_on">'.$this->l('Yes').'</label>
		    <img src="../img/admin/disabled.gif" alt="No" title="No" style="margin-left: 10px;" />
		    <input type="radio" name="active_slide" id="active_off" '.(($slide && (isset($slide->active) && (int)$slide->active == 0)) ? 'checked="checked" ' : '').' value="0" />
		    <label class="t" for="active_off">'.$this->l('No').'</label>
		</div>';

		/* Save */
		$this->_html .= '
		<p class="center">
			<input type="submit" class="button" name="submitSlide" value="'.$this->l('Save').'" />
			<a class="button" style="position:relative; padding:3px 3px 4px 3px; top:1px" href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">'.$this->l('Cancel').'</a>
		</p>';

		/* End of fieldset & form */
		$this->_html .= '
			<p>*'.$this->l('Required fields').'</p>
			</fieldset>
		</form>';
	}

	private function _postValidation()
	{
		$errors = array();

		/* Validation for Slider configuration */
		if (Tools::isSubmit('submitSlider'))
		{

			if (!Validate::isInt(Tools::getValue('HOMESLIDER_SPEED')) || !Validate::isInt(Tools::getValue('HOMESLIDER_PAUSE')) ||
				!Validate::isInt(Tools::getValue('HOMESLIDER_WIDTH')) || !Validate::isInt(Tools::getValue('HOMESLIDER_HEIGHT')))
					$errors[] = $this->l('Invalid values');
		} /* Validation for status */
		else if (Tools::isSubmit('changeStatus'))
		{
			if (!Validate::isInt(Tools::getValue('id_slide')))
				$errors[] = $this->l('Invalid slide');
		}
		/* Validation for Slide */
		else if (Tools::isSubmit('submitSlide'))
		{
			/* Checks state (active) */
			if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1))
			    $errors[] = $this->l('Invalid slide state');
			/* Checks position */
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->l('Invalid slide position');
			/* If edit : checks id_slide */
			if (Tools::isSubmit('id_slide'))
			{
				if (!Validate::isInt(Tools::getValue('id_slide')) && !$this->slideExists(Tools::getValue('id_slide')))
					$errors[] = $this->l('Invalid id_slide');
			}
			/* Checks title/url/legend/description/image */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (strlen(Tools::getValue('title_'.$language['id_lang'])) > 40)
					$errors[] = $this->l('Title is too long');
				if (strlen(Tools::getValue('legend_'.$language['id_lang'])) > 40)
					$errors[] = $this->l('Legend is too long');
				if (strlen(Tools::getValue('url_'.$language['id_lang'])) > 200)
					$errors[] = $this->l('URL is too long');
				if (strlen(Tools::getValue('description_'.$language['id_lang'])) > 400)
					$errors[] = $this->l('Description is too long');
				if (strlen(Tools::getValue('url_'.$language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_'.$language['id_lang'])))
					$errors[] = $this->l('URL format is not correct');
				if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename');
				if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename');
			}

			/* Checks title/url/legend/description for default lang */
			$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
			if (strlen(Tools::getValue('title_'.$defaultLanguage)) == 0)
				$errors[] = $this->l('Title is not set');
			if (strlen(Tools::getValue('legend_'.$defaultLanguage)) == 0)
				$errors[] = $this->l('Legend is not set');
			if (strlen(Tools::getValue('url_'.$defaultLanguage)) == 0)
				$errors[] = $this->l('URL is not set');
			if (Tools::getValue('image_'.$defaultLanguage) == '' && !Validate::isFileName(Tools::getValue('image_'.$defaultLanguage)) && !Tools::getValue('image_old_'.defaultLanguage))
				$errors[] = $this->l('Image is not set');
			if (Tools::getValue('image_old_'.$defaultLanguage) && !Validate::isFileName(Tools::getValue('image_old_'.$defaultLanguage)))
				$errors[] = $this->l('Image is not set');
		} /* Validation for deletion */
		else if (Tools::isSubmit('delete_id_slide') && (!Validate::isInt(Tools::getValue('delete_id_slide')) || !$this->slideExists((int)Tools::getValue('delete_id_slide'))))
			$errors[] = $this->l('Invalid id_slide');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			return false;
		}

		/* Returns if validation is ok */
		return true;
	}

	private function _postProcess()
	{
		$errors = array();

		/* Processes Slider */
		if (Tools::isSubmit('submitSlider'))
		{
			$res = Configuration::updateValue('HOMESLIDER_WIDTH', (int)Tools::getValue('HOMESLIDER_WIDTH'));
			$res &= Configuration::updateValue('HOMESLIDER_HEIGHT', (int)Tools::getValue('HOMESLIDER_HEIGHT'));
			$res &= Configuration::updateValue('HOMESLIDER_SPEED', (int)Tools::getValue('HOMESLIDER_SPEED'));
			$res &= Configuration::updateValue('HOMESLIDER_PAUSE', (int)Tools::getValue('HOMESLIDER_PAUSE'));
			if (!$res)
				$errors .= $this->displayError($this->l('Configuration could not be updated'));
			$this->_html .= $this->displayConfirmation($this->l('Configuration updated'));
		} /* Process Slide status */
		else if (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_slide'))
		{
			$slide = new HomeSlide((int)Tools::getValue('id_slide'));
			if ($slide->active == 0)
				$slide->active = 1;
			else
				$slide->active = 0;
			$res = $slide->update();
			$this->_html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('Configuration could not be updated')));
		}
		/* Processes Slide */
		else if (Tools::isSubmit('submitSlide'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_slide'))
			{
				$slide = new HomeSlide((int)Tools::getValue('id_slide'));
				if (!Validate::isLoadedObject($slide))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_slide'));
					return;
				}
			}
			else
				$slide = new HomeSlide();
			/* Sets position */
			$slide->position = (int)Tools::getValue('position');
			/* Sets active */
			$slide->active = (int)Tools::getValue('active_slide');

			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::getValue('title_'.$language['id_lang']) != '')
					$slide->title[$language['id_lang']] = pSQL(Tools::getValue('title_'.$language['id_lang']));
				if (Tools::getValue('url_'.$language['id_lang']) != '')
					$slide->url[$language['id_lang']] = pSQL(Tools::getValue('url_'.$language['id_lang']));
				if (Tools::getValue('legend_'.$language['id_lang']) != '')
					$slide->legend[$language['id_lang']] = pSQL(Tools::getValue('legend_'.$language['id_lang']));
				if (Tools::getValue('description_'.$language['id_lang']) != '')
					$slide->description[$language['id_lang']] = pSQL(Tools::getValue('description_'.$language['id_lang']));
				/* Uploads image and sets slide */
				$type = strtolower(substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
				$imagesize = array();
				$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
				if (isset($_FILES['image_'.$language['id_lang']]) &&
					isset($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($imagesize) &&
					in_array(strtolower(substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) &&
					in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
						$errors .= $error;
					else if (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
						return false;
					else if (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.Tools::encrypt($_FILES['image_'.$language['id_lang']]['name'].$salt).$type))
						$errors .= $this->displayError($this->l('An error occurred during the image upload.'));
					if (isset($temp_name))
						@unlink($temp_name);
					$slide->image[$language['id_lang']] = pSQL(Tools::encrypt($_FILES['image_'.($language['id_lang'])]['name'].$salt).$type);
				}
				if (Tools::getValue('image_old_'.$language['id_lang']) != '')
					$slide->image[$language['id_lang']] = pSQL(Tools::getValue('image_old_'.$language['id_lang']));
			}

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_slide'))
				{
					if (!$slide->add())
						$errors .= $this->displayError($this->l('Slide could not be added'));
				} /* Update */
				else if (!$slide->update())
					$errors .= $this->displayError($this->l('Slide could not be updated'));
			}
		} /* Deletes */
		else if (Tools::isSubmit('delete_id_slide'))
		{
			$slide = new HomeSlide((int)Tools::getValue('delete_id_slide'));
			$res = $slide->delete();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete');
			else
				$this->_html .= $this->displayConfirmation($this->l('Slide deleted'));
		}

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		else if (Tools::isSubmit('submitSlide') && Tools::getValue('id_slide'))
			$this->_html .= $this->displayConfirmation($this->l('Slide updated'));
		else if (Tools::isSubmit('submitSlide'))
			$this->_html .= $this->displayConfirmation($this->l('Slide added'));
	}

	public function hookHome()
	{
		$slider = array(
			'width' => Configuration::get('HOMESLIDER_WIDTH'),
			'height' => Configuration::get('HOMESLIDER_HEIGHT'),
			'speed' => Configuration::get('HOMESLIDER_SPEED'),
			'pause' => Configuration::get('HOMESLIDER_PAUSE')
		);

		$slides = $this->getSlides(true);
		if (!$slides)
			return;

		$this->smarty->assign('homeslider_slides', $slides);
		$this->smarty->assign('homeslider', $slider);

		return $this->display(__FILE__, 'homeslider.tpl');
	}

	public function hookHeader()
	{
		if (!$this->getSlides(true))
			return;

		$this->context->controller->addJS($this->_path.'js/jquery.bxSlider.min.js');
		$this->context->controller->addCSS($this->_path.'bx_styles.css');
		$this->context->controller->addJS($this->_path.'js/homeslider.js');
	}

	public function headerHTML()
	{
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addJqueryUI('ui.sortable');
		/* Style & js for fieldset 'slides configuration' */
		$html = '
		<style>
		#slides li {
			list-style: none;
			margin: 0 0 4px 0;
			padding: 10px;
			background-color: #F4E6C9;
			border: #CCCCCC solid 1px;
			color:#000;
		}
		</style>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery-ui.will.be.removed.in.1.6.js"></script>
	 	<script type="text/javascript">
			$(function() {
				var $mySlides = $("#slides");
				$mySlides.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
						$.post("'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
						}
					});
				$mySlides.hover(function() {
 					$(this).css("cursor","move");
 					},
 					function() {
 					$(this).css("cursor","auto");
				});
			});
		</script>';

		return $html;
	}

	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT MAX(hss.`position`) AS `next_position`
				FROM `'._DB_PREFIX_.'homeslider_slides` hss, `'._DB_PREFIX_.'homeslider` hs
				WHERE hss.`id_homeslider_slides` = hs.`id_homeslider_slides` AND hs.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}

	public function getSlides($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_homeslider_slides` as id_slide,
					   hssl.`image`,
					   hss.`position`,
					   hss.`active`,
					   hssl.`title`,
					   hssl.`url`,
					   hssl.`legend`,
					   hssl.`description`
			FROM '._DB_PREFIX_.'homeslider hs
			LEFT JOIN '._DB_PREFIX_.'homeslider_slides hss ON (hs.id_homeslider_slides = hss.id_homeslider_slides)
			LEFT JOIN '._DB_PREFIX_.'homeslider_slides_lang hssl ON (hss.id_homeslider_slides = hssl.id_homeslider_slides)
			WHERE (id_shop = '.(int)$id_shop.' OR id_shop = 1)
			AND hssl.id_lang = '.(int)$id_lang.
			($active ? ' AND hss.`active` = 1' : ' ').'
			ORDER BY hss.position');
	}

	public function displayStatus($id_slide, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$img = ((int)$active == 0 ? 'disabled.gif' : 'enabled.gif');
		$html = '<a href="'.AdminController::$currentIndex.
				'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_slide='.(int)$id_slide.'" title="'.$title.'"><img src="'._PS_ADMIN_IMG_.''.$img.'" alt="" /></a>';
		return $html;
	}

	public function slideExists($id_slide)
	{
		$req = 'SELECT hs.`id_homeslider_slides` as id_slide
				FROM `'._DB_PREFIX_.'homeslider` hs
				WHERE hs.`id_homeslider_slides` = '.(int)$id_slide;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
		return ($row);
	}
}
