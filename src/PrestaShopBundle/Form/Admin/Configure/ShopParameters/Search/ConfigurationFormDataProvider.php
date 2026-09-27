<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Generic form data provider for the Search preferences settings blocks (indexation, search options, weight).
 * Each block wires this provider with its own DataConfiguration.
 */
class ConfigurationFormDataProvider implements FormDataProviderInterface
{
    public function __construct(private readonly DataConfigurationInterface $configuration)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->configuration->getConfiguration();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function setData(array $data): array
    {
        return $this->configuration->updateConfiguration($data);
    }
}
