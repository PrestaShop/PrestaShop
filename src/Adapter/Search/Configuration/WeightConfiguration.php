<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Configuration;

use Exception;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeightConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'product_name_weight',
        'reference_weight',
        'short_description_weight',
        'description_weight',
        'category_weight',
        'brand_weight',
        'tags_weight',
        'attributes_weight',
        'features_weight',
    ];

    /**
     * @var array<string, string> Maps configuration field names to their configuration keys
     */
    private const FIELD_TO_KEY = [
        'product_name_weight' => 'PS_SEARCH_WEIGHT_PNAME',
        'reference_weight' => 'PS_SEARCH_WEIGHT_REF',
        'short_description_weight' => 'PS_SEARCH_WEIGHT_SHORTDESC',
        'description_weight' => 'PS_SEARCH_WEIGHT_DESC',
        'category_weight' => 'PS_SEARCH_WEIGHT_CNAME',
        'brand_weight' => 'PS_SEARCH_WEIGHT_MNAME',
        'tags_weight' => 'PS_SEARCH_WEIGHT_TAG',
        'attributes_weight' => 'PS_SEARCH_WEIGHT_ATTRIBUTE',
        'features_weight' => 'PS_SEARCH_WEIGHT_FEATURE',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        $configuration = [];
        foreach (self::FIELD_TO_KEY as $field => $key) {
            $configuration[$field] = (int) $this->configuration->get($key, 0, $shopConstraint);
        }

        return $configuration;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function updateConfiguration(array $config): array
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();

            foreach (self::FIELD_TO_KEY as $field => $key) {
                $this->updateConfigurationValue($key, $field, $config, $shopConstraint);
            }
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS);

        foreach (self::CONFIGURATION_FIELDS as $field) {
            $resolver->setAllowedTypes($field, ['null', 'int']);
        }

        return $resolver;
    }
}
