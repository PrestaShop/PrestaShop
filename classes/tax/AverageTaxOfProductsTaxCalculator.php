<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class AverageTaxOfProductsTaxCalculator
{
    private $id_order;
    private $configuration;
    private $db;

    public $computation_method = 'average_tax_of_products';

    public function __construct(PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface $db, PrestaShop\PrestaShop\Core\ConfigurationInterface $configuration)
    {
        $this->db = $db;
        $this->configuration = $configuration;
    }

    private function getProductTaxes()
    {
        $prefix = $this->configuration->get('_DB_PREFIX_');

        $sql = 'SELECT t.id_tax, t.rate, od.total_price_tax_excl FROM ' . $prefix . 'orders o
                INNER JOIN ' . $prefix . 'order_detail od ON od.id_order = o.id_order
                INNER JOIN ' . $prefix . 'order_detail_tax odt ON odt.id_order_detail = od.id_order_detail
                INNER JOIN ' . $prefix . 'tax t ON t.id_tax = odt.id_tax
                WHERE o.id_order = ' . (int) $this->id_order;

        return $this->db->select($sql);
    }

    public function setIdOrder($id_order)
    {
        $this->id_order = $id_order;

        return $this;
    }

    public function getTaxesAmount($price_before_tax, $price_after_tax = null, $round_precision = 2, $round_mode = null)
    {
        $amounts_arr = [];
        $total_base = 0;

        foreach ($this->getProductTaxes() as $row) {
            if (!array_key_exists($row['id_tax'], $amounts_arr)) {
                $amounts_arr[$row['id_tax']] = [
                    'rate' => $row['rate'],
                    'base' => 0,
                ];
            }

            $amounts_arr[$row['id_tax']]['base'] += $row['total_price_tax_excl'];
            $total_base += $row['total_price_tax_excl'];
        }

        $amounts = [];
        foreach ($amounts_arr as $k => $amount) {
            $amounts[$k] = Tools::ps_round(
                $price_before_tax * ($amount['base'] / $total_base) * $amount['rate'] / 100,
                $round_precision,
                $round_mode
            );
        }

        if ($price_after_tax) {
            $actual_tax = array_sum($amounts);

            Tools::spreadAmount(
                $price_after_tax - $price_before_tax - $actual_tax,
                $round_precision,
                $amounts,
                'id_tax'
            );
        }

        return $amounts;
    }
}
