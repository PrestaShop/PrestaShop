<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class statspersonalinfos extends ModuleGraph
{
    private $html = '';
    private $option;

    public function __construct()
    {
        $this->name = 'statspersonalinfos';
        $this->tab = 'analytics_stats';
        $this->version = '2.0.3';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Registered customer information', array(), 'Modules.Statspersonalinfos.Admin');
        $this->description = $this->trans('Adds information about your registered customers (such as gender and age) to the Stats dashboard.', array(), 'Modules.Statspersonalinfos.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules()
    {
        $this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<h4>'.$this->trans('Guide', array(), 'Admin.Global').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->trans('Target your audience', array(), 'Modules.Statspersonalinfos.Admin').'</h4>
				<p>'.
                    $this->trans('In order for each message to have an impact, you need to know who it is being addressed to. ', array(), 'Modules.Statspersonalinfos.Admin').
                    '<br>'.
                    $this->trans('Defining your target audience is essential when choosing the right tools to win them over.', array(), 'Modules.Statspersonalinfos.Admin').
                    '<br>'.
                    $this->trans('It is best to limit an action to a group -- or to groups -- of clients.', array(), 'Modules.Statspersonalinfos.Admin').
                    '<br>'.
                    $this->trans('Storing registered customer information allows you to accurately define customer profiles so you can adapt your special deals and promotions.', array(), 'Modules.Statspersonalinfos.Admin').'
				</p>
				<div>
					'.$this->trans('You can increase your sales by:', array(), 'Modules.Statspersonalinfos.Admin').'
					<ul>
						<li class="bullet">'.$this->trans('Launching targeted advertisement campaigns.', array(), 'Modules.Statspersonalinfos.Admin').'</li>
						<li class="bullet">'.$this->trans('Contacting a group of clients by email or newsletter.', array(), 'Modules.Statspersonalinfos.Admin').'</li>
					</ul>
				</div>
			</div>';
        $has_customers = (bool)Db::getInstance()->getValue('SELECT id_customer FROM '._DB_PREFIX_.'customer');
        if ($has_customers) {
            if (Tools::getValue('export')) {
                if (Tools::getValue('exportType') == 'gender') {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => 'gender'
                    ));
                } elseif (Tools::getValue('exportType') == 'age') {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => 'age'
                    ));
                } elseif (Tools::getValue('exportType') == 'country') {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => 'country'
                    ));
                } elseif (Tools::getValue('exportType') == 'currency') {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => 'currency'
                    ));
                } elseif (Tools::getValue('exportType') == 'language') {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => 'language'
                    ));
                }
            }

            $this->html .= '
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
                    'type' => 'pie',
                    'option' => 'gender'
                )).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->trans('Gender distribution allows you to determine the percentage of men and women shoppers on your store.', array(), 'Modules.Statspersonalinfos.Admin').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=gender').'">
								<i class="icon-cloud-upload"></i> '.$this->trans('CSV Export', array(), 'Modules.Statspersonalinfos.Admin').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
                    'type' => 'pie',
                    'option' => 'age'
                )).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->trans('Age ranges allow you to better understand target demographics.', array(), 'Modules.Statspersonalinfos.Admin').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=age').'">
								<i class="icon-cloud-upload"></i> '.$this->trans('CSV Export', array(), 'Modules.Statspersonalinfos.Admin').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
                    'type' => 'pie',
                    'option' => 'country'
                )).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->trans('Country distribution allows you to analyze which part of the World your customers are shopping from.', array(), 'Modules.Statspersonalinfos.Admin').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=country').'">
								<i class="icon-cloud-upload"></i> '.$this->trans('CSV Export', array(), 'Modules.Statspersonalinfos.Admin').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
                    'type' => 'pie',
                    'option' => 'currency'
                )).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->trans('Currency range allows you to determine which currency your customers are using.', array(), 'Modules.Statspersonalinfos.Admin').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=currency').'">
								<i class="icon-cloud-upload"></i> '.$this->trans('CSV Export', array(), 'Modules.Statspersonalinfos.Admin').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
                    'type' => 'pie',
                    'option' => 'language'
                )).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->trans('Language distribution allows you to analyze the browsing language used by your customers.', array(), 'Modules.Statspersonalinfos.Admin').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=language').'">
								<i class="icon-cloud-upload"></i> '.$this->trans('CSV Export', array(), 'Modules.Statspersonalinfos.Admin').'
							</a>
						</div>
					</div>
				</div>';
        } else {
            $this->html .= '<p>'.$this->trans('No customers have registered yet.', array(), 'Modules.Statspersonalinfos.Admin').'</p>';
        }

        return $this->html;
    }

    public function setOption($option, $layers = 1)
    {
        $this->option = $option;
    }

    protected function getData($layers)
    {
        switch ($this->option) {
            case 'gender':
                $this->_titles['main'] = $this->trans('Gender distribution', array(), 'Modules.Statspersonalinfos.Admin');
                $genders = array(
                    0 => $this->trans('Male', array(), 'Admin.Shopparameters.Feature'),
                    1 => $this->trans('Female', array(), 'Admin.Shopparameters.Feature'),
                    2 => $this->trans('Unknown', array(), 'Admin.Shopparameters.Feature'),
                );

                $sql = 'SELECT g.type, c.id_gender, COUNT(c.id_customer) AS total
						FROM '._DB_PREFIX_.'customer c
						LEFT JOIN '._DB_PREFIX_.'gender g ON c.id_gender = g.id_gender
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c').'
						GROUP BY c.id_gender';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                $genders_results = array();
                foreach ($result as $row) {
                    $type = (is_null($row['type'])) ? 2 : $row['type'];
                    if (!isset($genders_results[$type])) {
                        $genders_results[$type] = 0;
                    }
                    $genders_results[$type] += $row['total'];
                }

                foreach ($genders_results as $type => $total) {
                    $this->_values[] = $total;
                    $this->_legend[] = $genders[$type];
                }
                break;

            case 'age':
                $this->_titles['main'] = $this->trans('Age range', array(), 'Modules.Statspersonalinfos.Admin');

                // 0 - 18 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 18
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('0-18', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // 18 - 24 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 18
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 25
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('18-24', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // 25 - 34 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 25
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 35
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('25-34', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // 35 - 49 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 35
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 50
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('35-49', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // 50 - 59 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 50
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('50-59', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // More than 60 years
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NOT NULL
							AND `birthday` != "0000-00-00"
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('60+', array(), 'Modules.Statspersonalinfos.Admin');
                }

                // Total unknown
                $sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NULL
							OR `birthday` = "0000-00-00"
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (isset($result['total']) && $result['total']) {
                    $this->_values[] = $result['total'];
                    $this->_legend[] = $this->trans('Unknown', array(), 'Admin.Shopparameters.Feature');
                }
                break;

            case 'country':
                $this->_titles['main'] = $this->trans('Country distribution', array(), 'Modules.Statspersonalinfos.Admin');
                $sql = 'SELECT cl.`name`, COUNT(c.`id_country`) AS total
						FROM `'._DB_PREFIX_.'address` a
						LEFT JOIN `'._DB_PREFIX_.'customer` cu ON cu.id_customer = a.id_customer
						LEFT JOIN `'._DB_PREFIX_.'country` c ON a.`id_country` = c.`id_country`
						LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
						WHERE a.id_customer != 0
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'cu').'
						GROUP BY c.`id_country`';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                foreach ($result as $row) {
                    $this->_values[] = $row['total'];
                    $this->_legend[] = $row['name'];
                }
                break;

            case 'currency':
                $this->_titles['main'] = $this->trans('Currency distribution', array(), 'Modules.Statspersonalinfos.Admin');
                $sql = 'SELECT cl.name, c.`iso_code`, COUNT(c.`id_currency`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'currency` c ON o.`id_currency` = c.`id_currency`
						LEFT JOIN `'._DB_PREFIX_.'currency_lang` cl ON (cl.`id_currency` = c.`id_currency` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_currency`';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                foreach ($result as $row) {
                    $this->_values[] = $row['total'];
                    $this->_legend[] = $row['name'] . ' (' . $row['iso_code'] . ')';
                }
                break;

            case 'language':
                $this->_titles['main'] = $this->trans('Language distribution', array(), 'Modules.Statspersonalinfos.Admin');
                $sql = 'SELECT c.`name`, COUNT(c.`id_lang`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'lang` c ON o.`id_lang` = c.`id_lang`
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_lang`';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                foreach ($result as $row) {
                    $this->_values[] = $row['total'];
                    $this->_legend[] = $row['name'];
                }
                break;
        }
    }
}
