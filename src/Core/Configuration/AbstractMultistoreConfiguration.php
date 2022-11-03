<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Configuration;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractMultistoreConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Context
     */
    protected $shopContext;

    /**
     * @var FeatureInterface
     */
    protected $multistoreFeature;

    /**
     * AbstractMultistoreConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature)
    {
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
        $this->multistoreFeature = $multistoreFeature;
    }

    abstract protected function buildResolver(): OptionsResolver;

    /**
     * @return ShopConstraint|null
     */
    public function getShopConstraint(): ?ShopConstraint
    {
        if ($this->shopContext->isAllShopContext()) {
            return null;
        }

        return $this->shopContext->getShopConstraint();
    }

    /**
     * @param string $configurationKey BO configuration key, ex: PS_SHOP_ENABLE (as stored in the configuration db table)
     * @param string $fieldName
     * @param array $input an associative array where keys are field names and values are field values
     * @param ShopConstraint|null $shopConstraint
     * @param array $options<int, string> Options @deprecated Will be removed in next major
     */
    protected function updateConfigurationValue(string $configurationKey, string $fieldName, array $input, ?ShopConstraint $shopConstraint, array $options = []): void
    {
        if (!array_key_exists($fieldName, $input)) {
            return;
        }

        $prefix = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX;

        // If we are in multistore context (not all shop) and the multistore checkbox value is absent (it was unchecked),
        // then the field multistore value must be removed from DB for current context
        if ($this->multistoreFeature->isUsed() && !$this->shopContext->isAllShopContext() && !isset($input[$prefix . $fieldName])) {
            $this->configuration->deleteFromContext($configurationKey, $shopConstraint);
        } else {
            $this->configuration->set($configurationKey, $input[$fieldName], $shopConstraint, $options);
        }
    }

    /**
     * This method checks that:
     * - Only defined configuration fields exist
     * - In single or group shop context, multistore fields are added to the list of defined fields, so that no error
     * is triggered because of additional checkboxes
     * - In all shop context, all fields are required
     *
     * @param array $configurationInputValues
     *
     * @return bool
     */
    public function validateConfiguration(array $configurationInputValues): bool
    {
        $resolver = $this->buildResolver();
        $definedOptions = $fields = $resolver->getDefinedOptions();

        if ($this->multistoreFeature->isUsed() && !$this->shopContext->isAllShopContext()) {
            // add multistore fields in list of defined fields
            foreach ($definedOptions as $value) {
                $fields[] = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $value;
            }
            $resolver->setDefined($fields);
        } else {
            // all fields are mandatory in all shop context. (not in single or group shop context because fields
            // can be disabled with checkboxes)
            $resolver->setRequired($definedOptions);
        }

        $resolver->resolve($configurationInputValues);

        return true;
    }
}
