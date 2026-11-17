<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\WithdrawalRequest;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provides the withdrawal function options form with data.
 */
final class WithdrawalRequestOptionsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly DataConfigurationInterface $optionsDataConfiguration,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->optionsDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->optionsDataConfiguration->updateConfiguration($data);
    }
}
