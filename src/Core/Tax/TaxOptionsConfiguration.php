<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Tax;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Tax\Ecotax\ProductEcotaxResetterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Handles configuration data for tax options.
 */
final class TaxOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['enable_tax', 'display_tax_in_cart', 'tax_address_type', 'use_eco_tax', 'eco_tax_rule_group'];

    /**
     * @var ProductEcotaxResetterInterface
     */
    private $productEcotaxResetter;

    /**
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param ProductEcotaxResetterInterface $productEcotaxResetter
     */
    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature, ProductEcotaxResetterInterface $productEcotaxResetter)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);

        $this->productEcotaxResetter = $productEcotaxResetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_tax' => (bool) $this->configuration->get('PS_TAX', false, $shopConstraint),
            'display_tax_in_cart' => (bool) $this->configuration->get('PS_TAX_DISPLAY', false, $shopConstraint),
            'tax_address_type' => $this->configuration->get('PS_TAX_ADDRESS_TYPE', null, $shopConstraint),
            'use_eco_tax' => (bool) $this->configuration->get('PS_USE_ECOTAX', false, $shopConstraint),
            'eco_tax_rule_group' => (int) $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID', 0, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_TAX', 'enable_tax', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_TAX_DISPLAY', 'display_tax_in_cart', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_TAX_ADDRESS_TYPE', 'tax_address_type', $configuration, $shopConstraint);
            $this->updateEcotax($configuration['use_eco_tax'], $configuration);

            if ($configuration['use_eco_tax'] && isset($configuration['eco_tax_rule_group'])) {
                $this->updateConfigurationValue('PS_ECOTAX_TAX_RULES_GROUP_ID', 'eco_tax_rule_group', $configuration, $shopConstraint);
            }

            if (false === $configuration['enable_tax']) {
                $configuration['multistore_display_tax_in_cart'] = false;
                $configuration['display_tax_in_cart'] = false;
                $this->updateConfigurationValue('PS_TAX_DISPLAY', 'display_tax_in_cart', $configuration, $shopConstraint);
            }
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enable_tax', 'bool')
            ->setAllowedTypes('display_tax_in_cart', 'bool')
            ->setAllowedTypes('tax_address_type', ['string', 'null'])
            ->setAllowedTypes('use_eco_tax', 'bool')
            ->setAllowedTypes('eco_tax_rule_group', 'int');
    }

    /**
     * Responsible for ecotax update
     *
     * @param bool $isEnabled
     * @param array $configuration
     */
    private function updateEcotax($isEnabled, $configuration)
    {
        $shopConstraint = $this->getShopConstraint();

        $wasEnabled = (bool) $this->configuration->get('PS_USE_ECOTAX', false, $shopConstraint);

        if (!$isEnabled && $wasEnabled !== $isEnabled && !$this->shopContext->isAllShopContext()) {
            $this->productEcotaxResetter->reset();
        }

        $this->updateConfigurationValue('PS_USE_ECOTAX', 'use_eco_tax', $configuration, $shopConstraint);
    }
}
