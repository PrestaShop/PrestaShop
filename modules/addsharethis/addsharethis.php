<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class AddShareThis extends Module
{
	function __construct()
	{
		$this->name = 'addsharethis';
		$this->author = 'Custom';
		$this->tab = 'front_office_features';
		$this->need_instance = 0;
		$this->_directory = dirname(__FILE__);
		parent::__construct();	
		$this->displayName = $this->l('Add Sharethis');
		$this->description = $this->l('Display social count button on the home page');
}

public function install()
	{
				Configuration::updateValue('CONF_ROW', 'ea22d519-9f98-4018-99a9-5b5f1b100fa8');
				Configuration::updateValue('ADDTHISSHARE_TWITTER',1);
				Configuration::updateValue('ADDTHISSHARE_GOOGLE',1);
				Configuration::updateValue('ADDTHISSHARE_PINTEREST',1);
				Configuration::updateValue('ADDTHISSHARE_FACEBOOK',1);
		if (!parent::install() OR 
		
				!$this->registerHook('Extraright') OR 
				!$this->registerHook('header'))
		return false;
		return true;
}

public function uninstall()
    {
		if (!parent::uninstall() OR
			!Configuration::deleteByName('CONF_ROW') OR
			!Configuration::deleteByName('ADDTHISSHARE_TWITTER') OR
			!Configuration::deleteByName('ADDTHISSHARE_GOOGLE') OR
			!Configuration::deleteByName('ADDTHISSHARE_PINTEREST') OR
			!Configuration::deleteByName('ADDTHISSHARE_FACEBOOK') OR
			!$this->unregisterHook('Extraright') OR 
			!$this->unregisterHook('header'))
 return false;
		else return true;
}
	
public function getContent()
  {
	
		$this->_html .= '<h2>'.$this->displayName.'<span style=" float:right;"></span></h2><div class="clear"></div>';
		if (isset($_POST['submitCog'])) $this->updateCog();
		
		if (Tools::isSubmit('submitCog'))
		{
		    $conf_row = Tools::getValue('conf_row');
			Configuration::updateValue('CONF_ROW', $conf_row);
		
	    }
		$this->_html .= '
		<fieldset class="space" id="cogField">
			<legend><img src="'.$this->_path.'logo.png" alt="" title="" /> '.$this->l('Configuration').'</legend>
			<form id="cogForm" name="cogForm" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">
			<br/>
			<em>'.$this->l('(Key in your account statistic http://sharethis.com)').'</em>
				<div class="clearfix"></div><br/><br/>
				<label>'.$this->l('Publisher Pub Key:').'</label>
				<div class="margin-form">
					<input type="text" name="conf_row" id="conf_row" size="60" value="'.Tools::getValue('conf_row', Configuration::get('CONF_ROW')).'" />
				</div><br/><br/>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;"  type="checkbox" name="Twitter" id="Twitter" '.( (Configuration::get('ADDTHISSHARE_TWITTER') == 1) ? 'checked="checked"' : '').' />
					<img  src="'.($this->_path).'img/twitter.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" type="checkbox" name="Google" id="Google" '.( (Configuration::get('ADDTHISSHARE_GOOGLE') == 1) ? 'checked="checked"' : '').' />
					<img src="'.($this->_path).'img/google.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;"  type="checkbox" name="Pinterest" id="Pinterest" '.( (Configuration::get('ADDTHISSHARE_PINTEREST') == 1) ? 'checked="checked"' : '').' />
					<img src="'.($this->_path).'img/pinterest.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" type="checkbox" name="Facebook" id="Facebook" '.( (Configuration::get('ADDTHISSHARE_FACEBOOK') == 1) ? 'checked="checked"' : '').' />
					<img src="'.($this->_path).'img/facebook.gif" />
					</div>
				<br/><br/><div class="margin-form">
					<input type="submit" class="button" name="submitCog" id="submitCog" value="'.$this->l('Save').'" />
				</div>
			</form>
		</fieldset>';

		return $this->_html;
  }

public function updateCog()
	{
		Configuration::updateValue('ADDTHISSHARE_TWITTER', (isset($_POST['Twitter']) ? 1 : 0));
		Configuration::updateValue('ADDTHISSHARE_GOOGLE', (isset($_POST['Google']) ? 1 : 0));
		Configuration::updateValue('ADDTHISSHARE_PINTEREST', (isset($_POST['Pinterest']) ? 1 : 0));
		Configuration::updateValue('ADDTHISSHARE_FACEBOOK', (isset($_POST['Facebook']) ? 1 : 0));
	
	}


function hookDisplayHeader($params)
	{
	global $smarty, $cookie;
	global $link;
		$product = new Product((int)Tools::getValue('id_product'), false, (int)$cookie->id_lang);
		$productLink = $link->getProductLink($product);
		$images = $product->getImages((int)$cookie->id_lang);
		foreach ($images AS $k => $image)
			if ($image['cover'])
			{
				$cover['id_image'] = (int)$product->id.'-'.(int)$image['id_image'];
				$cover['legend'] = $image['legend'];
			}
		if (!isset($cover))
			$cover = array('id_image' => Language::getIsoById((int)$cookie->id_lang).'-default', 'legend' => 'No picture');
  $this->context->smarty->assign(array(
			'cover' => $cover,
			'product' => $product,
			'productLink' => $productLink,
			'this_path' => $this->_path
		));
		return $this->display(__FILE__, 'addsharethis_header.tpl');
	}
	
public function hookExtraRight($params)
	{
		global $smarty, $cookie, $link;		
		
		$conf_row = Configuration::get('CONF_ROW');
		$this->context->smarty->assign(array(
				'conf_row' => $conf_row,
		    ));
			
		if (Configuration::get('ADDTHISSHARE_TWITTER') == 1)
			$data['twitter'] = '<span class="st_twitter_hcount sharebtn" displayText="Tweet"></span>';
		if (Configuration::get('ADDTHISSHARE_GOOGLE') == 1)
		{
			$data['google'] = '<span class="st_googleplus_hcount" displayText="Google +"></span>';
		}
		if (Configuration::get('ADDTHISSHARE_PINTEREST') == 1)
			$data['pinterest'] = '<span class="st_pinterest_hcount sharebtn" displayText="Pinterest"></span>';

		if (Configuration::get('ADDTHISSHARE_FACEBOOK') == 1)
			$data['facebook'] = '<span class="st_facebook_hcount sharebtn" displayText="Facebook"></span>';
			

		$smarty->assign('addsharethis_data', $data);
	
			
		return $this->display(__FILE__, 'addsharethis.tpl');
		} 
		
	function hookLeftColumn($params)
	{
			return $this->hookExtraRight($params);
	}

	function hookFooter($params)
	{
			return $this->hookExtraRight($params);
	}
	
	function hookHome($params)
	{
			return $this->hookExtraRight($params);
	}

	function hookExtraleft($params)
	{
			return $this->hookExtraRight($params);
	}
	function hookProductActions($params)
	{
		    return $this->hookExtraRight($params);
	}
	
	function hookProductFooter($params)
	{
			return $this->hookExtraRight($params);
	}

}
?>