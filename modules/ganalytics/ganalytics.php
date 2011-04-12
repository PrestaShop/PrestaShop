<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class GAnalytics extends Module
{	
	function __construct()
	{
	 	$this->name = 'ganalytics';
	 	$this->tab = 'analytics_stats';
	 	$this->version = '1.3';
		$this->author = 'PrestaShop';
        $this->displayName = 'Google Analytics';
		
	 	parent::__construct();
		
		if ($this->id AND !Configuration::get('GANALYTICS_ID'))
			$this->warning = $this->l('You have not yet set your Google Analytics ID');
        $this->description = $this->l('Integrate Google Analytics script into your shop');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}
	
    function install()
    {
        if (!parent::install() OR !$this->registerHook('header') OR !$this->registerHook('orderConfirmation'))
			return false;
		return true;
    }
	
	function uninstall()
	{
		if (!Configuration::deleteByName('GANALYTICS_ID') OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function getContent()
	{
		$output = '<h2>Google Analytics</h2>';
		if (Tools::isSubmit('submitGAnalytics') AND ($gai = Tools::getValue('ganalytics_id')))
		{
			Configuration::updateValue('GANALYTICS_ID', $gai);
			$output .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings updated').'
			</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset class="width2">
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Your username').'</label>
				<div class="margin-form">
					<input type="text" name="ganalytics_id" value="'.Tools::getValue('ganalytics_id', Configuration::get('GANALYTICS_ID')).'" />
					<p class="clear">'.$this->l('Example:').' UA-1234567-1</p>
				</div>
				<center><input type="submit" name="submitGAnalytics" value="'.$this->l('Update ID').'" class="button" /></center>
			</fieldset>
		</form>';
		
		$output .= '
		<fieldset class="space">
			<legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Help').'</legend>
			 <h3>'.$this->l('The first step of tracking e-commerce transactions is to enable e-commerce reporting for your website\'s profile.').'</h3>
			 '.$this->l('To enable e-Commerce reporting, please follow these steps:').'
			 <ol>
			 	<li>'.$this->l('Log in to your account').'</li>
			 	<li>'.$this->l('Click Edit next to the profile you would like to enable.').'</li>
			 	<li>'.$this->l('On the Profile Settings page, click Edit (next to Main Website Profile Information).').'</li>
			 	<li>'.$this->l('Change the e-Commerce Website radio button from No to Yes').'</li>
			</ol>
			<h3>'.$this->l('To set up your goals, enter Goal Information:').'</h3>
			<ol>
				<li>'.$this->l('Return to Your Account main page').'</li>
				<li>'.$this->l('Find the profile for which you will be creating goals, then click Edit').'</li>
				<li>'.$this->l('Select one of the 4 goal slots available for that profile, then click Edit').'</li>
				<li>'.$this->l('Enter the Goal URL. Reaching this page marks a successful conversion.').'</li>
				<li>'.$this->l('Enter the Goal name as it should appear in your Google Analytics account.').'</li>
				<li>'.$this->l('Turn on Goal.').'</li>
			</ol>
			<h3>'.$this->l('Then, define a funnel by following these steps:').'</h3>
			<ol>
				<li>'.$this->l('Enter the URL of the first page of your conversion funnel. This page should be a common page to all users working their way towards your Goal.').'</li>
				<li>'.$this->l('Enter a Name for this step.').'</li>
				<li>'.$this->l('If this step is a required step in the conversion process, mark the checkbox to the right of the step.').'</li>
				<li>'.$this->l('Continue entering goal steps until your funnel has been completely defined. You may enter up to 10 steps, or only one step.').'</li>
			</ol>
			'.$this->l('Finally, configure Additional settings by following the steps below:').'
			<ol>
				<li>'.$this->l('If the URLs entered above are case sensitive, mark the checkbox.').'</li>
				<li>'.$this->l('Select the appropriate goal Match Type. (').'<a href="http://www.google.com/support/analytics/bin/answer.py?answer=72285">'.$this->l('Learn more').'</a> '.$this->l('about Match Types and how to choose the appropriate goal Match Type for your goal.)').'</li>
				<li>'.$this->l('Enter a Goal value. This is the value used in Google Analytics\' ROI calculations.').'</li>
				<li>'.$this->l('Click Save Changes to create this Goal and funnel, or Cancel to exit without saving.').'</li>
			</ol>
			<h3>'.$this->l('Demonstration: The order process').'</h3>
			<ol>
				<li>'.$this->l('After having enabled your e-commerce reports and selected the respective profile enter \'order-confirmation.php\' as the targeted page URL.').'</li>
				<li>'.$this->l('Name this goal (for example \'Order process\')').'</li>
				<li>'.$this->l('Activate the goal').'</li>
				<li>'.$this->l('Add \'product.php\' as the first page of your conversion funnel').'</li>
				<li>'.$this->l('Give it a name (for example, \'Product page\')').'</li>
				<li>'.$this->l('Do not mark the \'required\' checkbox because the customer could be visiting directly from an \'adding to cart\' button such as in the homefeatured block on the homepage.').'</li>
				<li>'.$this->l('Continue by entering the following URLs as goal steps:').'
					<ul>
						<li>order/step0.html '.$this->l('(required)').'</li>
						<li>authentication.php '.$this->l('(required)').'</li>
						<li>order/step1.html '.$this->l('(required)').'</li>
						<li>order/step2.html '.$this->l('(required)').'</li>
						<li>order/step3.html '.$this->l('(required)').'</li>
					</ul>
				</li>
				<li>'.$this->l('Check the \'Case sensitive\' option').'</li>
				<li>'.$this->l('Save this new goal').'</li>
			</ol>
		</fieldset>';
		
		return $output;
	}
	
	function hookHeader($params)
	{
		global $step, $smarty;
		
		// hookOrderConfirmation() already send the sats bypass this step
		if (strpos($_SERVER['REQUEST_URI'], __PS_BASE_URI__.'order-confirmation.php') === 0) return '';
	
		// Otherwise, create Google Analytics stats
		$ganalytics_id = Configuration::get('GANALYTICS_ID');
		$pageTrack = (strpos($_SERVER['REQUEST_URI'], __PS_BASE_URI__.'order.php') === 0 ? '"/order/step'.intval($step).'.html"' : '');
		$smarty->assign('ganalytics_id', $ganalytics_id);
		$smarty->assign('pageTrack', $pageTrack);
		$smarty->assign('isOrder', false);
		return $this->display(__FILE__, 'header.tpl');
	}
	
	function hookFooter($params)
	{
		// for retrocompatibility
		if (!$this->isRegisteredInHook('header')) $this->registerHook('header');
		return ;
	}

	function hookOrderConfirmation($params)
	{
		global $smarty;
		// Setting parameters
		$parameters = Configuration::getMultiple(array('PS_LANG_DEFAULT'));
		
		$order = $params['objOrder'];
		if (Validate::isLoadedObject($order))
		{
			$deliveryAddress = new Address(intval($order->id_address_delivery));

			$conversion_rate = 1;
			if ($order->id_currency != Configuration::get('PS_CURRENCY_DEFAULT'))
			{
				$currency = new Currency(intval($order->id_currency));
				$conversion_rate = floatval($currency->conversion_rate);
			}

			// Order general information
		$trans = array('id' => intval($order->id),				// order ID - required
						'store' => htmlentities(Configuration::get('PS_SHOP_NAME')), // affiliation or store name
						'total' => Tools::ps_round(floatval($order->total_paid) / floatval($conversion_rate), 2),		// total - required
						'tax' => '0', // tax
						'shipping' => Tools::ps_round(floatval($order->total_shipping) / floatval($conversion_rate), 2),	// shipping
						'city' => addslashes($deliveryAddress->city),		// city
						'state' => '',				// state or province
						'country' => addslashes($deliveryAddress->country) // country
						);

			// Product information
			$products = $order->getProducts();
			foreach ($products AS $product)
			{
				$category = Db::getInstance()->getRow('
								SELECT name FROM `'._DB_PREFIX_.'category_lang` , '._DB_PREFIX_.'product 
								WHERE `id_product` = '.intval($product['product_id']).' AND `id_category_default` = `id_category` 
								AND `id_lang` = '.intval($parameters['PS_LANG_DEFAULT']));
				
				$items[] = array('OrderId' => intval($order->id),				// order ID - required
								'SKU' => addslashes($product['product_id']),		// SKU/code - required
								'Product' => addslashes($product['product_name']),		// product name
								'Category' => addslashes($category['name']),			// category or variation
								'Price' => Tools::ps_round(floatval($product['product_price_wt']) / floatval($conversion_rate), 2),	// unit price - required
								'Quantity' => addslashes(intval($product['product_quantity']))	//quantity - required
								);
			}
			$ganalytics_id = Configuration::get('GANALYTICS_ID');
			$pageTrack = (strpos($_SERVER['REQUEST_URI'], __PS_BASE_URI__.'order.php') === 0 ? '"/order/step'.intval($step).'.html"' : '');
			$smarty->assign('items', $items);
			$smarty->assign('trans', $trans);
			$smarty->assign('ganalytics_id', $ganalytics_id);
			$smarty->assign('pageTrack', $pageTrack);
			$smarty->assign('isOrder', true);
			return $this->display(__FILE__, 'header.tpl');
		}
	}
}
