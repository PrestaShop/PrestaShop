<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Currencies;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class CurrencyFormDataProvider
 */
final class CurrencyFormDataProvider implements FormDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'exchange_rates' => [
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return [];
    }
}
