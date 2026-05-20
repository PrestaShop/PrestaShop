<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

class WeightConfiguration implements DataConfigurationInterface
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function getConfiguration(): array
    {
        return [
            'product_name_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_PNAME'),
            'reference_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_REF'),
            'short_description_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_SHORTDESC'),
            'description_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_DESC'),
            'category_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_CNAME'),
            'brand_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_MNAME'),
            'tags_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_TAG'),
            'attributes_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_ATTRIBUTE'),
            'features_weight' => $this->configuration->get('PS_SEARCH_WEIGHT_FEATURE'),
        ];
    }

    public function updateConfiguration(array $config): array
    {
        $this->configuration->set('PS_SEARCH_WEIGHT_PNAME', (int) $config['product_name_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_REF', (int) $config['reference_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_SHORTDESC', (int) $config['short_description_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_DESC', (int) $config['description_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_CNAME', (int) $config['category_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_MNAME', (int) $config['brand_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_TAG', (int) $config['tags_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_ATTRIBUTE', (int) $config['attributes_weight']);
        $this->configuration->set('PS_SEARCH_WEIGHT_FEATURE', (int) $config['features_weight']);

        return [];
    }

    public function validateConfiguration(array $config): bool
    {
        return true;
    }
}
