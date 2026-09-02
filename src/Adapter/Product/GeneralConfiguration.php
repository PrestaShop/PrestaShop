<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class loads and saves general configuration for product.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'catalog_mode',
        'catalog_mode_with_prices',
        'new_days_number',
        'short_description_limit',
        'quantity_discount',
        'force_friendly_url',
        'product_breadcrumb_category',
        'default_status',
        'specific_price_priorities',
        'disabled_products_behavior',
    ];

    /**
     * @var SpecificPricePriorityUpdater
     */
    private $specificPricePriorityUpdater;

    /**
     * @param Configuration $configuration
     * @param SpecificPricePriorityUpdater $specificPricePriorityUpdater
     */
    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        SpecificPricePriorityUpdater $specificPricePriorityUpdater
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
        $this->specificPricePriorityUpdater = $specificPricePriorityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'catalog_mode' => (bool) $this->configuration->get('PS_CATALOG_MODE', false, $shopConstraint),
            'catalog_mode_with_prices' => (bool) $this->configuration->get('PS_CATALOG_MODE_WITH_PRICES', false, $shopConstraint),
            'new_days_number' => (int) $this->configuration->get('PS_NB_DAYS_NEW_PRODUCT', 0, $shopConstraint),
            'short_description_limit' => (int) $this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT', 0, $shopConstraint),
            'quantity_discount' => (int) $this->configuration->get('PS_QTY_DISCOUNT_ON_COMBINATION', 0, $shopConstraint),
            'force_friendly_url' => (bool) $this->configuration->get('PS_FORCE_FRIENDLY_PRODUCT', false, $shopConstraint),
            'product_breadcrumb_category' => (string) $this->configuration->get('PS_PRODUCT_BREADCRUMB_CATEGORY', 'default', $shopConstraint),
            'default_status' => (bool) $this->configuration->get('PS_PRODUCT_ACTIVATION_DEFAULT', false, $shopConstraint),
            'specific_price_priorities' => $this->getPrioritiesData(),
            'disabled_products_behavior' => (string) $this->configuration->get('PS_PRODUCT_REDIRECTION_DEFAULT', RedirectType::TYPE_NOT_FOUND, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();
            $configWithCatalogModePrice = $config;
            $configWithCatalogModePrice['catalog_mode_with_prices'] = $config['catalog_mode'] ? $config['catalog_mode_with_prices'] : false;

            $this->updateConfigurationValue('PS_CATALOG_MODE', 'catalog_mode', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_CATALOG_MODE_WITH_PRICES', 'catalog_mode_with_prices', $configWithCatalogModePrice, $shopConstraint);
            $this->updateConfigurationValue('PS_NB_DAYS_NEW_PRODUCT', 'new_days_number', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_SHORT_DESC_LIMIT', 'short_description_limit', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_QTY_DISCOUNT_ON_COMBINATION', 'quantity_discount', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_FORCE_FRIENDLY_PRODUCT', 'force_friendly_url', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_BREADCRUMB_CATEGORY', 'product_breadcrumb_category', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_ACTIVATION_DEFAULT', 'default_status', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_REDIRECTION_DEFAULT', 'disabled_products_behavior', $config, $shopConstraint);
            try {
                $this->specificPricePriorityUpdater->updateDefaultPriorities(new PriorityList($config['specific_price_priorities']));
            } catch (SpecificPriceConstraintException $e) {
                if ($e->getCode() !== SpecificPriceConstraintException::DUPLICATE_PRIORITY) {
                    throw $e;
                }

                $errors[] = [
                    'key' => 'The selected condition must be different in each field to set an order of priority.',
                    'domain' => 'Admin.Notifications.Error',
                    'parameters' => [],
                ];
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('catalog_mode', 'bool')
            ->setAllowedTypes('catalog_mode_with_prices', 'bool')
            ->setAllowedTypes('new_days_number', 'int')
            ->setAllowedTypes('short_description_limit', 'int')
            ->setAllowedTypes('quantity_discount', 'int')
            ->setAllowedTypes('force_friendly_url', 'bool')
            ->setAllowedTypes('product_breadcrumb_category', 'string')
            ->setAllowedTypes('default_status', 'bool')
            ->setAllowedTypes('specific_price_priorities', 'array')
            ->setAllowedTypes('disabled_products_behavior', 'string');
    }

    /**
     * @return string[]
     */
    private function getPrioritiesData(): array
    {
        if (!empty($this->configuration->get('PS_SPECIFIC_PRICE_PRIORITIES'))) {
            return explode(';', $this->configuration->get('PS_SPECIFIC_PRICE_PRIORITIES'));
        }

        return array_values(PriorityList::AVAILABLE_PRIORITIES);
    }
}
