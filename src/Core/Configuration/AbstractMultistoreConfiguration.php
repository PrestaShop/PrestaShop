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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
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
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;

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
     * AbstractMultistoreConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param Context $shopContext
     */
    public function __construct(Configuration $configuration, Context $shopContext)
    {
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
    }

    /**
     * @return ShopConstraint|null
     */
    protected function getShopConstraint(): ?ShopConstraint
    {
        if (!$this->shopContext->isAllShopContext()) {
            $contextShopGroup = $this->shopContext->getContextShopGroup();
            $contextShopId = $this->shopContext->getContextShopID();
            $contextShopId = (int) $contextShopId > 0 ? $contextShopId : null;

            return new ShopConstraint(
                $contextShopId,
                $contextShopGroup->id
            );
        }

        return null;
    }

    /**
     * @param array $configurationInput
     *
     * @return array
     */
    protected function getConfigurationInputValues(array $configurationInput): array
    {
        if (!$this->shopContext->isAllShopContext()) {
            return $this->removeDisabledFields($configurationInput);
        }

        return $configurationInput;
    }

    /**
     * Remove fields that are disabled (multistore checkbox unchecked)
     *
     * @param array $configuration
     *
     * @return array
     */
    public function removeDisabledFields(array $configuration): array
    {
        if ($this->shopContext->isAllShopContext()) {
            return $configuration;
        }

        $prefix = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX;

        foreach ($configuration as $key => $value) {
            // This is a multistore checkbox we ignore it
            if (substr($key, 0, strlen($prefix)) === $prefix) {
                continue;
            }

            // Check for this configuration key if the associated multistore checbox is enabled
            if (isset($configuration[$prefix . $key]) && $configuration[$prefix . $key] !== true) {
                unset($configuration[$key]);
            }
        }

        return $configuration;
    }

    protected function updateConfigurationValue(string $configurationKey, string $formKey, array $input, ?ShopConstraint $shopConstraint, array $options = []): void
    {
        if (isset($input[$formKey])) {
            $this->configuration->set($configurationKey, $input[$formKey], $shopConstraint, $options);
        }
    }
}
