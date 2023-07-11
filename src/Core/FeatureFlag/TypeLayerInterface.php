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

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

interface TypeLayerInterface
{
    /**
     * Get type name of this handler.
     */
    public function getTypeName(): string;

    /**
     * Define is this handler can change feature flag status.
     */
    public function isReadonly(): bool;

    /**
     * Define is this handler can be used.
     */
    public function canBeUsed(string $featureFlagName): bool;

    /**
     * Retrieve if the feature flag is enabled.
     */
    public function isEnabled(string $featureFlagName): bool;

    /**
     * Enable the feature flag with this handler method.
     */
    public function enable(string $featureFlagName): void;

    /**
     * Disable the feature flag with this handler method.
     */
    public function disable(string $featureFlagName): void;
}
