<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Adapter\CustomerService\Configuration\CustomerServiceOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Bridges the "Contact options" panel form with the underlying configuration
 * adapter, so the controller does not need to know whether values land in
 * Configuration::updateValue or anywhere else.
 */
final class CustomerServiceOptionsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CustomerServiceOptionsConfiguration $configuration,
    ) {
    }

    public function getData(): array
    {
        return $this->configuration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->configuration->updateConfiguration($data);
    }
}
