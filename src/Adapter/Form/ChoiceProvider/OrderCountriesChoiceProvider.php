<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Context;
use Country;
use Db;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Choices for countries in which at least one order has been placed
 */
final class OrderCountriesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * The orders grid asks for these twice while it is being built, once to decide whether the delivery
     * column is worth showing and once to fill the country filter. The service is shared, so remembering
     * the answer saves the whole second round trip - and the query behind it scans every order.
     *
     * @var array<string, int>|null
     */
    private $choices;

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        if (null !== $this->choices) {
            return $this->choices;
        }

        if (!Country::isCurrentlyUsed('country', true)) {
            return $this->choices = [];
        }

        $countries = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cl.id_country, cl.`name`
            FROM `' . _DB_PREFIX_ . 'country_lang` cl
            WHERE EXISTS (
                SELECT 1
                FROM `' . _DB_PREFIX_ . 'orders` o
                INNER JOIN `' . _DB_PREFIX_ . 'address` a
			        ON a.id_address = o.id_address_delivery
                WHERE a.id_country = cl.id_country
            ) AND cl.`id_lang` = ' . (int) Context::getContext()->language->id . '
			ORDER BY cl.name ASC'
        );

        return $this->choices = FormChoiceFormatter::formatFormChoices(
            $countries,
            'id_country',
            'name'
        );
    }
}
