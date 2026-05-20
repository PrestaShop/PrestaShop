<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShop\PrestaShop\Adapter\Search\Configuration\WeightConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class WeightFormDataProvider implements FormDataProviderInterface
{
    public function __construct(private readonly WeightConfiguration $configuration)
    {
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
