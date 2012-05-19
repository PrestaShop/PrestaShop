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
*  @version  Release: $Revision: 7515 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Gsitemap extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'gsitemap';
		$this->tab = 'seo';
		$this->version = '1.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Google sitemap');
		$this->description = $this->l('Generate your Google sitemap file');

		if (!defined('GSITEMAP_FILE'))
			define('GSITEMAP_FILE', dirname(__FILE__).'/../../sitemap.xml');
	}

	public function uninstall()
	{
		file_put_contents(GSITEMAP_FILE, '');
		return parent::uninstall();
	}

	private function _postValidation()
	{
		file_put_contents(GSITEMAP_FILE, '');
		if (!($fp = fopen(GSITEMAP_FILE, 'w')))
			$this->_postErrors[] = sprintf($this->l('Cannot create %ssitemap.xml file.'), realpath(dirname(__FILE__.'/../..')).'/');
		else
			fclose($fp);
	}

	private function getUrlWith($url, $key, $value)
	{
		if (empty($value))
			return $url;
		if (strpos($url, '?') !== false)
			return $url.'&'.$key.'='.$value;
		return $url.'?'.$key.'='.$value;
	}

	private function _postProcess()
	{
		Configuration::updateValue('GSITEMAP_ALL_CMS', (int)Tools::getValue('GSITEMAP_ALL_CMS'));
		Configuration::updateValue('GSITEMAP_ALL_PRODUCTS', (int)Tools::getValue('GSITEMAP_ALL_PRODUCTS'));

		if (Shop::isFeatureActive())
			$res = $this->generateSitemapIndex();
		else
			$res = $this->generateSitemap(Configuration::get('PS_SHOP_DEFAULT'), GSITEMAP_FILE);

		$this->_html .= '<h3 class="'. ($res ? 'conf confirm' : 'alert error') .'" style="margin-bottom: 20px">';
		$this->_html .= $res ? $this->l('Sitemap file generated') : $this->l('Error while creating sitemap file');
		$this->_html .= '</h3>';
	}

	/**
	 * Generate sitemap index to reference the sitemap of each shop
	 *
	 * @return bool
	 */
	public function generateSitemapIndex()
	{
		$xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
</sitemapindex>
XML;
		$xml = new SimpleXMLElement($xmlString);

		$sql = 'SELECT s.id_shop, su.domain, su.domain_ssl, CONCAT(su.physical_uri, su.virtual_uri) as uri
				FROM '._DB_PREFIX_.'shop s
				INNER JOIN '._DB_PREFIX_.'shop_url su ON s.id_shop = su.id_shop AND su.main = 1
				WHERE s.active = 1
					AND s.deleted = 0
					AND su.active = 1';
		if (!$result = Db::getInstance()->executeS($sql))
			return false;

		$res = true;
		foreach ($result as $row)
		{
			$info = pathinfo(GSITEMAP_FILE);
			$filename = $info['filename'].'-'.$row['id_shop'].'.'.$info['extension'];

			$replaceUrl = array('http://'.$row['domain'].$row['uri'], ((Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://').$row['domain_ssl'].$row['uri']);

			$last = $this->generateSitemap($row['id_shop'], $info['dirname'].'/'.$filename, $replaceUrl);
			if ($last)
			{
				$this->_addSitemapIndexNode($xml, 'http://'.$row['domain'].(($row['uri']) ? $row['uri'] : '/').$filename, date('Y-m-d'));
			}
			$res &= $last;
		}

		$fp = fopen(GSITEMAP_FILE, 'w');
		fwrite($fp, $xml->asXML());
		fclose($fp);

		return $res && file_exists(GSITEMAP_FILE);
	}

	/**
	 * Generate a sitemap for a shop
	 *
	 * @param int $id_shop
	 * @param string $filename
	 * @return bool
	 */
	private function generateSitemap($id_shop, $filename = '', $replace_url = array())
	{
		$langs = Language::getLanguages();
		$shop = new Shop($id_shop);
		if (!$shop->id)
			return false;

		$xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
</urlset>
XML;

		$xml = new SimpleXMLElement($xmlString);

		if (Configuration::get('PS_REWRITING_SETTINGS') && count($langs) > 1)
			foreach($langs as $lang)
			{
				$this->_addSitemapNode($xml, Tools::getShopDomain(true, true).__PS_BASE_URI__.$lang['iso_code'].'/', '1.00', 'daily', date('Y-m-d'));
			}
		else
			$this->_addSitemapNode($xml, Tools::getShopDomain(true, true).__PS_BASE_URI__, '1.00', 'daily', date('Y-m-d'));

		/* Product Generator */
		$sql = 'SELECT p.id_product, pl.link_rewrite, DATE_FORMAT(IF(ps.date_upd,ps.date_upd,ps.date_add), \'%Y-%m-%d\') date_upd, pl.id_lang, cl.`link_rewrite` category, ean13, i.id_image, il.legend legend_image, (
					SELECT MIN(level_depth)
					FROM '._DB_PREFIX_.'product p2
					'.Shop::addSqlAssociation('product', 'p2').'
					LEFT JOIN '._DB_PREFIX_.'category_product cp2 ON p2.id_product = cp2.id_product
					LEFT JOIN '._DB_PREFIX_.'category c2 ON cp2.id_category = c2.id_category
					WHERE p2.id_product = p.id_product AND product_shop.`active` = 1 AND c2.`active` = 1) AS level_depth
				FROM '._DB_PREFIX_.'product p
				LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = '.(int)$id_shop.')
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product)
				LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (ps.id_category_default = cl.id_category AND pl.id_lang = cl.id_lang AND cl.id_shop = '.(int)$id_shop.')
				LEFT JOIN '._DB_PREFIX_.'image i ON p.id_product = i.id_product
				LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image)
				LEFT JOIN '._DB_PREFIX_.'lang l ON (pl.id_lang = l.id_lang)
				WHERE l.`active` = 1
					AND ps.`active` = 1
					AND ps.id_shop = '.(int)$id_shop.'
				'.(Configuration::get('GSITEMAP_ALL_PRODUCTS') ? '' : 'HAVING level_depth IS NOT NULL').'
				ORDER BY pl.id_product, pl.id_lang ASC';

		$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);

		// array used to know which product/image was already added (blacklist)
		$done = null;
		$sitemap = null;

		// iterates on the products, to gather the image ids
		while ($product = Db::getInstance()->nextRow($resource))
		{
			// if the product has not been added
			$id_product = $product['id_product'];
			if (!isset($done[$id_product]['added']))
			{
				// priority
				if (($priority = 0.7 - ($product['level_depth'] / 10)) < 0.1)
					$priority = 0.1;

				// adds the product
				$tmpLink = $this->context->link->getProductLink((int)($product['id_product']), $product['link_rewrite'], $product['category'], $product['ean13'], (int)($product['id_lang']));
				$sitemap = $this->_addSitemapNode($xml, $tmpLink, $priority, 'weekly', substr($product['date_upd'], 0, 10));

				// considers the product has added
				$done[$id_product]['added'] = true;
			}

			// if the image has not been added
			$id_image = $product['id_image'];
			if (!isset($done[$id_product][$id_image]) && $id_image)
			{
				// adds the image
				$this->_addSitemapNodeImage($sitemap, $product);

				// considers the image as added
				$done[$id_product][$id_image] = true;
			}
		}

		/* Categories Generator */
		if (Configuration::get('PS_REWRITING_SETTINGS'))
			$categories = Db::getInstance()->executeS('
			SELECT c.id_category, c.level_depth, link_rewrite, DATE_FORMAT(IF(date_upd,date_upd,date_add), \'%Y-%m-%d\') AS date_upd, cl.id_lang
			FROM '._DB_PREFIX_.'category c
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category = cl.id_category
			LEFT JOIN '._DB_PREFIX_.'lang l ON cl.id_lang = l.id_lang
			WHERE l.`active` = 1 AND c.`active` = 1 AND c.id_category != 1
			ORDER BY cl.id_category, cl.id_lang ASC');
		else
			$categories = Db::getInstance()->executeS(
			'SELECT c.id_category, c.level_depth, DATE_FORMAT(IF(date_upd,date_upd,date_add), \'%Y-%m-%d\') AS date_upd
			FROM '._DB_PREFIX_.'category c
			ORDER BY c.id_category ASC');


		foreach($categories as $category)
		{
			if (($priority = 0.9 - ($category['level_depth'] / 10)) < 0.1)
				$priority = 0.1;

			$tmpLink = Configuration::get('PS_REWRITING_SETTINGS') ?
				$this->context->link->getCategoryLink((int)$category['id_category'], $category['link_rewrite'], (int)$category['id_lang'])
				: $this->context->link->getCategoryLink((int)$category['id_category']);
			$this->_addSitemapNode($xml, htmlspecialchars($tmpLink), $priority, 'weekly', substr($category['date_upd'], 0, 10));
		}

		/* CMS Generator */
		if (Configuration::get('GSITEMAP_ALL_CMS') || !Module::isInstalled('blockcms'))
			$sql_cms = '
			SELECT DISTINCT '.(Configuration::get('PS_REWRITING_SETTINGS') ? 'cl.id_cms, cl.link_rewrite, cl.id_lang' : 'cl.id_cms').
			' FROM '._DB_PREFIX_.'cms_lang cl
			LEFT JOIN '._DB_PREFIX_.'lang l ON (cl.id_lang = l.id_lang)
			WHERE l.`active` = 1
			ORDER BY cl.id_cms, cl.id_lang ASC';
		else if (Module::isInstalled('blockcms'))
			$sql_cms = '
			SELECT DISTINCT '.(Configuration::get('PS_REWRITING_SETTINGS') ? 'cl.id_cms, cl.link_rewrite, cl.id_lang' : 'cl.id_cms').
			' FROM '._DB_PREFIX_.'cms_block_page b
			LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (b.id_cms = cl.id_cms)
			LEFT JOIN '._DB_PREFIX_.'lang l ON (cl.id_lang = l.id_lang)
			WHERE l.`active` = 1
			ORDER BY cl.id_cms, cl.id_lang ASC';

		$cmss = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_cms);
		foreach($cmss as $cms)
		{
			$tmpLink = Configuration::get('PS_REWRITING_SETTINGS') ?
				$this->context->link->getCMSLink((int)$cms['id_cms'], $cms['link_rewrite'], false, (int)$cms['id_lang'])
				: $this->context->link->getCMSLink((int)$cms['id_cms']);
			$this->_addSitemapNode($xml, $tmpLink, '0.8', 'daily');
		}

		/* Add classic pages (contact, best sales, new products...) */
		$pages = array(
			'supplier' => false,
			'manufacturer' => false,
			'new-products' => false,
			'prices-drop' => false,
			'stores' => false,
			'authentication' => true,
			'best-sales' => false,
			'contact-form' => true);

		// Don't show suppliers and manufacturers if they are disallowed
		if (!Module::getInstanceByName('blockmanufacturer')->id && !Configuration::get('PS_DISPLAY_SUPPLIERS'))
			unset($pages['manufacturer']);

		if (!Module::getInstanceByName('blocksupplier')->id && !Configuration::get('PS_DISPLAY_SUPPLIERS'))
			unset($pages['supplier']);

		// Generate nodes for pages
		if(Configuration::get('PS_REWRITING_SETTINGS'))
			foreach ($pages as $page => $ssl)
				foreach($langs as $lang)
					$this->_addSitemapNode($xml, $this->context->link->getPageLink($page, $ssl, $lang['id_lang']), '0.5', 'monthly');
		else
			foreach($pages as $page => $ssl)
				$this->_addSitemapNode($xml, $this->context->link->getPageLink($page, $ssl), '0.5', 'monthly');

		$xml_string = $xml->asXML();

		// Replace URL in XML strings by real shops URL
		if ($replace_url)
			$xml_string = str_replace(array(Tools::getShopDomain(true).__PS_BASE_URI__, Tools::getShopDomainSsl(true).__PS_BASE_URI__), $replace_url, $xml_string);

		$fp = fopen($filename, 'w');
		fwrite($fp, $xml_string);
		fclose($fp);

		return file_exists($filename);
	}

	private function _addSitemapIndexNode($xml, $loc, $last_mod)
	{
		$sitemap = $xml->addChild('sitemap');
		$sitemap->addChild('loc', htmlspecialchars($loc));
		$sitemap->addChild('lastmod', $last_mod);
		return $sitemap;
	}

	private function _addSitemapNode($xml, $loc, $priority, $change_freq, $last_mod = NULL)
	{
		$sitemap = $xml->addChild('url');
		$sitemap->addChild('loc', htmlspecialchars($loc));
		$sitemap->addChild('priority',  number_format($priority,1,'.',''));
		if ($last_mod)
			$sitemap->addChild('lastmod', $last_mod);
		$sitemap->addChild('changefreq', $change_freq);
		return $sitemap;
	}

	private function _addSitemapNodeImage($xml, $product)
	{
		$image = $xml->addChild('image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
		$image->addChild('loc', htmlspecialchars($this->context->link->getImageLink($product['link_rewrite'], (int)$product['id_product'].'-'.(int)$product['id_image'])), 'http://www.google.com/schemas/sitemap-image/1.1');

		$legend_image = preg_replace('/(&+)/i', '&amp;', $product['legend_image']);
		$image->addChild('caption', $legend_image, 'http://www.google.com/schemas/sitemap-image/1.1');
		$image->addChild('title', $legend_image, 'http://www.google.com/schemas/sitemap-image/1.1');
	}

	private function _displaySitemap()
	{
		if (Shop::isFeatureActive())
		{
			$sql = 'SELECT s.id_shop, su.domain, su.domain_ssl, CONCAT(su.physical_uri, su.virtual_uri) as uri
					FROM '._DB_PREFIX_.'shop s
					INNER JOIN '._DB_PREFIX_.'shop_url su ON s.id_shop = su.id_shop AND su.main = 1
					WHERE s.active = 1
						AND s.deleted = 0
						AND su.active = 1';
			if (!$result = Db::getInstance()->executeS($sql))
				return '';

			$this->_html .= '<h2>'.$this->l('Sitemap index').'</h2>';
			$this->_html .= '<p>'.$this->l('Your Google sitemap file is online at the following address:').'<br />
				<a href="'.Tools::getShopDomain(true, true).__PS_BASE_URI__.'sitemap.xml" target="_blank"><b>'.Tools::getShopDomain(true, true).__PS_BASE_URI__.'sitemap.xml</b></a></p><br />';

			$info = pathinfo(GSITEMAP_FILE);
			foreach ($result as $shop)
			{
				$filename = $info['dirname'].'/'.$info['filename'].'-'.$shop['id_shop'].'.'.$info['extension'];
				if (file_exists($filename) && filesize($filename))
				{
					$fp = fopen($filename, 'r');
					$fstat = fstat($fp);
					fclose($fp);
					$xml = simplexml_load_file($filename);

					$nbPages = count($xml->url);
					$sitemap_uri = 'http://'.$shop['domain'].$shop['uri'].$info['filename'].'-'.$shop['id_shop'].'.'.$info['extension'];

					$this->_html .= '<h2>'.$this->l('Sitemap for: ').$shop['domain'].$shop['uri'].'</h2>';
					$this->_html .= '<p>'.$this->l('Your Google sitemap file is online at the following address:').'<br />
					<a href="'.$sitemap_uri.'" target="_blank"><b>'.$sitemap_uri.'</b></a></p><br />';

					$this->_html .= $this->l('Update:').' <b>'.utf8_encode(strftime('%A %d %B %Y %H:%M:%S',$fstat['mtime'])).'</b><br />';
					$this->_html .= $this->l('Filesize:').' <b>'.number_format(($fstat['size']*.000001), 3).'MB</b><br />';
					$this->_html .= $this->l('Indexed pages:').' <b>'.$nbPages.'</b><br /><br />';
				}
			}
		}
		elseif (file_exists(GSITEMAP_FILE) && filesize(GSITEMAP_FILE))
		{
			$fp = fopen(GSITEMAP_FILE, 'r');
			$fstat = fstat($fp);
			fclose($fp);
			$xml = simplexml_load_file(GSITEMAP_FILE);

			$nbPages = count($xml->url);

			$this->_html .= '<p>'.$this->l('Your Google sitemap file is online at the following address:').'<br />
			<a href="'.Tools::getShopDomain(true, true).__PS_BASE_URI__.'sitemap.xml" target="_blank"><b>'.Tools::getShopDomain(true, true).__PS_BASE_URI__.'sitemap.xml</b></a></p><br />';

			$this->_html .= $this->l('Update:').' <b>'.utf8_encode(strftime('%A %d %B %Y %H:%M:%S',$fstat['mtime'])).'</b><br />';
			$this->_html .= $this->l('Filesize:').' <b>'.number_format(($fstat['size']*.000001), 3).'MB</b><br />';
			$this->_html .= $this->l('Indexed pages:').' <b>'.$nbPages.'</b><br /><br />';
		}
	}

	private function _displayForm()
	{
		if (Tools::usingSecureMode())
			$domain = Tools::getShopDomainSsl(true);
		else
			$domain = Tools::getShopDomain(true);

		$this->_html .= '
			<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
				<div style="margin:0 0 20px 0;">
					<input type="checkbox" name="GSITEMAP_ALL_PRODUCTS" id="GSITEMAP_ALL_PRODUCTS" style="vertical-align: middle;" value="1" '.(Configuration::get('GSITEMAP_ALL_PRODUCTS') ? 'checked="checked"' : '').' /> <label class="t" for="GSITEMAP_ALL_PRODUCTS">'.$this->l('Sitemap also includes products from inactive categories').'</label>
				</div>
				<div style="margin:0 0 20px 0;">
					<input type="checkbox" name="GSITEMAP_ALL_CMS" id="GSITEMAP_ALL_CMS" style="vertical-align: middle;" value="1" '.(Configuration::get('GSITEMAP_ALL_CMS') ? 'checked="checked"' : '').' /> <label class="t" for="GSITEMAP_ALL_CMS">'.$this->l('Sitemap also includes CMS pages which are not in a CMS block').'</label>
				</div>
				<input name="btnSubmit" class="button" type="submit"
				value="'.((!file_exists(GSITEMAP_FILE)) ? $this->l('Generate sitemap file') : $this->l('Update sitemap file')).'" />
			</form><br />
			<h2>'.$this->l('Use cron job to re-build the sitemap:').'</h2>
			<p>
				<b>'.$domain.__PS_BASE_URI__.'modules/gsitemap/gsitemap-cron.php?&token='.substr(Tools::encrypt('gsitemap/cron'),0,10).'&GSITEMAP_ALL_CMS='.((int)Configuration::get('GSITEMAP_ALL_CMS')).'&GSITEMAP_ALL_PRODUCTS='.((int)Configuration::get('GSITEMAP_ALL_PRODUCTS')).'</b>
			</p>';
	}

	public function getContent()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}

		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Search Engine Optimization').'</legend>
				<br />
				'.$this->l('See').' <a href="http://www.google.com/support/webmasters/bin/answer.py?hl=en&answer=156184&from=40318&rd=1" style="font-weight:bold;text-decoration:underline;" target="_blank">
				'.$this->l('this page').'</a> '.$this->l('for more information').'
				<br />';

		$this->_displaySitemap();
		$this->_displayForm();

		$this->_html .= '</fieldset>';
		return $this->_html;
	}

}

