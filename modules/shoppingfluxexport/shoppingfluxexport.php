<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9074 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class shoppingfluxexport extends Module
{

	public function __construct()
	{
	 	$this->name = 'shoppingfluxexport';
	 	$this->tab = 'advertising_marketing';
	 	$this->version = '1.4.1';

	 	parent::__construct();

		$this->displayName = $this->l('Export Shopping Flux');
		$this->description = $this->l('Export du catalogue pour Shopping Flux');
		$this->confirmUninstall = $this->l('Êtes-vous sur de vouloir supprimer ce module ?');
	}

	public function install()
	{
		// Create Token
		if (!Configuration::updateValue('SHOPPING_FLUX_TOKEN', md5(rand())))
			return false;

		// Install Module
		if (!parent::install())
			return false;

		return true;
	}

	public function uninstall()
	{
		// Delete Token
		if (!Configuration::deleteByName('SHOPPING_FLUX_TOKEN'))
			return false;

		// Uninstall Module
		if (!parent::uninstall())
			return false;

		return true;
	}

	public function getContent()
	{
		if (isset($_POST['generateFlux']) && $_POST['generateFlux'] != NULL)
			$this->generateFlux();
		
		$uri = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/shoppingfluxexport/flux.php?token='.Configuration::get('SHOPPING_FLUX_TOKEN');

		$this->_html = '<h2>'.$this->displayName.'</h2>
		<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'">
			<fieldset>
				<legend>'.$this->l('Export Shopping Flux').'</legend>

				<p>'.$this->l('Adresse du fichier :').'
				<a href="'.$uri.'" target="_blank">
				'.$uri.'</a></p>
			</fieldset>

		</form>
		';
		
		return $this->_html;
	}

	private function clean($string)
	{
	
		$string = str_replace("\r\n", " ", $string); 
		$string = str_replace("|", " ", $string); 
		
		return $string;
	}

	public function generateFlux()
	{
		if (Tools::getValue('token') == '' || Tools::getValue('token') != Configuration::get('SHOPPING_FLUX_TOKEN'))
			die('Invalid Token');
		
		$titles = array(
			0 => 'id_produit',
			1 => 'nom_produit',
			2 => 'url_produit',
			3 => 'url_image',
			4 => 'description',
			5 => 'description_courte',
			6 => 'prix',
			7 => 'prix_barre',
			8 => 'frais_de_port',
			9 => 'delaiLiv',
			10 => 'marque',
			11 => 'rayon',
			12 => 'stock',
			13 => 'qte_stock',
			14 => 'EAN',
			15 => 'poids',
			16 => 'ecotaxe',
			17 => 'TVA',
			18 => 'Reference constructeur',
			19 => 'Reference fournisseur'
		);
		
		echo implode("|", $titles)."\r\n";
		
		//For Shipping
		$configuration = Configuration::getMultiple(array('PS_TAX_ADDRESS_TYPE','PS_CARRIER_DEFAULT','PS_COUNTRY_DEFAULT', 'PS_LANG_DEFAULT', 'PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD', 'PS_SHIPPING_FREE_WEIGHT'));
		
		$products = Product::getSimpleProducts($configuration['PS_LANG_DEFAULT']);
		
		$defaultCountry = new Country($configuration['PS_COUNTRY_DEFAULT'], Configuration::get('PS_LANG_DEFAULT'));
		$id_zone = (int)$defaultCountry->id_zone;
			
		$carrier = new Carrier((int)$configuration['PS_CARRIER_DEFAULT']);
		$carrierTax = Tax::getCarrierTaxRate((int)$carrier->id, (int)$this->{$configuration['PS_TAX_ADDRESS_TYPE']});
		
		foreach ($products as $key => $produit)
		{
			$product = new Product((int)($produit['id_product']), true, $configuration['PS_LANG_DEFAULT']);
			
			//For links
			$link = new Link();
			
			//For images
			$cover = $product->getCover($product->id);
			$ids = $product->id.'-'.$cover['id_image'];
			
			//For shipping
			
			if ($product->getPrice(true, NULL, 2, NULL, false, true, 1) >= (float)($configuration['PS_SHIPPING_FREE_PRICE']) AND (float)($configuration['PS_SHIPPING_FREE_PRICE']) > 0)
				$shipping = 0;
			elseif (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) AND $product->weight >= (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) AND (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) > 0)
				$shipping = 0;
			else
			{
				if (isset($configuration['PS_SHIPPING_HANDLING']) AND $carrier->shipping_handling)
				$shipping = (float)($configuration['PS_SHIPPING_HANDLING']);
				
				if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
					$shipping += $carrier->getDeliveryPriceByWeight($product->weight, $id_zone);
				else
					$shipping += $carrier->getDeliveryPriceByPrice($product->getPrice(true, NULL, 2, NULL, false, true, 1), $id_zone);
	  
				$shipping *= 1 + ($carrierTax / 100);
				$shipping = (float)(Tools::ps_round((float)($shipping), 2));
				
			}
			
			$data = array();
			$data[0] = $product->id;
			$data[1] = $product->name;
			$data[2] = $link->getProductLink($product);
			$data[3] = $link->getImageLink($product->link_rewrite, $ids, 'large');
			$data[4] = $product->description;
			$data[5] = $product->description_short;
			$data[6] = $product->getPrice(true, NULL, 2, NULL, false, true, 1);
			$data[7] = $product->getPrice(true, NULL, 2, NULL, false, false, 1);
			$data[8] = $shipping;
			$data[9] = $carrier->delay[2];
			$data[10] = $product->manufacturer_name;
			$data[11] = $product->category;
			$data[12] = ($product->quantity > 0) ? 'oui' : 'non';
			$data[13] = $product->quantity;
			$data[14] = $product->ean13;
			$data[15] = $product->weight;
			$data[16] = $product->ecotax;
			$data[17] = $product->tax_rate;
			$data[18] = $product->reference;
			$data[19] = $product->supplier_reference;
			
			foreach($data as $key => $value)
				$data[$key] = $this->clean($value);
				
			echo implode("|", $data)."\r\n";
			
		}
	}
}

