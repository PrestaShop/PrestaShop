<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $amounts = [];
        $total_base = 0;
        $price_after_tax = (float) $price_after_tax;
        $price_before_tax = (float) $price_before_tax;

        foreach ($this->getProductTaxes() as $row) {
            if (!array_key_exists($row['id_tax'], $amounts)) {
                $amounts[$row['id_tax']] = [
                    'rate' => $row['rate'],
                    'base' => 0,
                ];
            }

            $amounts[$row['id_tax']]['base'] += $row['total_price_tax_excl'];
            $total_base += $row['total_price_tax_excl'];
        }

        foreach ($amounts as &$amount) {
            $amount['amount'] = Tools::ps_round(
                $price_before_tax * ($amount['base'] / $total_base) * $amount['rate'] / 100,
                $round_precision,
                $round_mode
            );
        }

        if ($price_after_tax) {
            $actual_tax = array_sum(array_column($amounts, 'amount'));

            Tools::spreadAmount(
                $price_after_tax - $price_before_tax - $actual_tax,
                $round_precision,
                $amounts,
                'amount'
            );
        }

        return array_combine(array_keys($amounts), array_column($amounts, 'amount'));
    }
}
