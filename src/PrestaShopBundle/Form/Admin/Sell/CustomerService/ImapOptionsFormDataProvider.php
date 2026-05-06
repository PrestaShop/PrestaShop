<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Adapter\CustomerService\Configuration\ImapConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

final class ImapOptionsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly ImapConfiguration $configuration,
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
