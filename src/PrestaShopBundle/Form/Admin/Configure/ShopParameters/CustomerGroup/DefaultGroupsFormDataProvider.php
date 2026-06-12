<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroup;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class DefaultGroupsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly DataConfigurationInterface $defaultGroupsConfiguration,
    ) {
    }

    public function getData(): array
    {
        return $this->defaultGroupsConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->defaultGroupsConfiguration->updateConfiguration($data);
    }
}
